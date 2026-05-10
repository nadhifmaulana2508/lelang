<?php

require_once __DIR__ . '/../helpers/response.php';

class CessieController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // ==========================================
    // 1. READ ALL (GET) + REKAP SESUAI FILTER
    // ==========================================
    public function getAll() {
        $keyword = $_GET['keyword'] ?? '';
        $status = $_GET['status'] ?? 'Semua';
        $kode_kantor = $_GET['kode_kantor'] ?? ''; 
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5; // Default 5 data per page
        $offset = ($page - 1) * $limit;

        $whereClauses = ["1=1"];
        $params = [];

        // FIX BUG: PDO tidak membolehkan 1 nama parameter dipakai 2 kali.
        // Jadi kita pecah menjadi :kw1 dan :kw2
        if (!empty($keyword)) {
            $whereClauses[] = "(nama_nasabah LIKE :kw1 OR no_rekening LIKE :kw2)";
            $params[':kw1'] = "%$keyword%";
            $params[':kw2'] = "%$keyword%";
        }

        // Filter Status
        if ($status !== 'Semua' && !empty($status)) {
            $whereClauses[] = "status = :status";
            $params[':status'] = $status;
        }

        // Filter Cabang / Korwil
        if (!empty($kode_kantor)) {
            if ($kode_kantor === 'KORWIL_SMG') {
                $whereClauses[] = "kode_kantor IN ('001','002','003','004','005','006','007')";
            } elseif ($kode_kantor === 'KORWIL_SLO') {
                $whereClauses[] = "kode_kantor IN ('008','009','010','011','012','013','014')";
            } elseif ($kode_kantor === 'KORWIL_BMS') {
                $whereClauses[] = "kode_kantor IN ('015','016','017','018','019','020','021')";
            } elseif ($kode_kantor === 'KORWIL_PKL') {
                $whereClauses[] = "kode_kantor IN ('022','023','024','025','026','027','028')";
            } else {
                $whereClauses[] = "kode_kantor = :kode_kantor";
                $params[':kode_kantor'] = $kode_kantor;
            }
        }

        // Gabungkan semua kondisi Filter
        $whereSql = implode(' AND ', $whereClauses);

        // 1. Hitung Total Baris (Untuk Pagination)
        $countSql = "SELECT COUNT(*) FROM calon_cessie WHERE $whereSql";
        $stmtCount = $this->pdo->prepare($countSql);
        foreach ($params as $key => $val) { $stmtCount->bindValue($key, $val); }
        $stmtCount->execute();
        $totalData = $stmtCount->fetchColumn();
        $totalPages = ceil($totalData / $limit);

        // 2. QUERY REKAP (SUM) SESUAI FILTER
        $sumSql = "SELECT 
                    SUM(baki_debet) as sum_bd, 
                    SUM(saldo_bank) as sum_sb, 
                    SUM(totung) as sum_tt, 
                    SUM(nilai_agunan) as sum_agunan 
                   FROM calon_cessie WHERE $whereSql"; 
        $stmtSum = $this->pdo->prepare($sumSql);
        foreach ($params as $key => $val) { $stmtSum->bindValue($key, $val); }
        $stmtSum->execute();
        $sums = $stmtSum->fetch(PDO::FETCH_ASSOC);

        $sum_bd = $sums['sum_bd'] ?? 0;
        $sum_sb = $sums['sum_sb'] ?? 0;
        $sum_tt = $sums['sum_tt'] ?? 0;
        $sum_agunan = $sums['sum_agunan'] ?? 0;

        // Kalkulasi Persentase Rekap
        $pct_bd = $sum_bd > 0 ? ($sum_agunan / $sum_bd) * 100 : 0;
        $pct_sb = $sum_sb > 0 ? ($sum_agunan / $sum_sb) * 100 : 0;
        $pct_tt = $sum_tt > 0 ? ($sum_agunan / $sum_tt) * 100 : 0;

        $rekap = [
            'total_baki_debet' => $sum_bd,
            'total_saldo_bank' => $sum_sb,
            'total_totung' => $sum_tt,
            'total_nilai_agunan' => $sum_agunan,
            'pct_bd' => round($pct_bd, 2),
            'pct_sb' => round($pct_sb, 2),
            'pct_tt' => round($pct_tt, 2)
        ];

        // 3. Ambil Data Tabel (Limit 5)
        $sql = "SELECT * FROM calon_cessie WHERE $whereSql ORDER BY id DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $val) { $stmt->bindValue($key, $val); }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 4. Kirim Response ke Frontend
        sendResponse(200, "Berhasil ambil data calon cessie", [
            'data' => $data,
            'rekap' => $rekap,
            'pagination' => [
                'total_data' => $totalData,
                'total_pages' => $totalPages,
                'current_page' => $page,
                'limit' => $limit
            ]
        ]);
    }

    // ==========================================
    // 2. READ DETAIL BY ID (GET)
    // ==========================================
    public function getDetail($id) {
        $sql = "SELECT * FROM calon_cessie WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            sendResponse(200, "Detail calon cessie ditemukan", $data);
        } else {
            sendResponse(404, "Data tidak ditemukan");
        }
    }

    // ==========================================
    // 3. CREATE DATA (POST JSON)
    // ==========================================
    public function create() {
        // Tangkap JSON Body
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            sendResponse(400, "Format JSON tidak valid atau kosong");
            return;
        }

        $no_rekening = $input['no_rekening'] ?? '';

        // ---------------------------------------------------------
        // VALIDASI: CEK APAKAH NO REKENING SUDAH ADA DI DATABASE
        // ---------------------------------------------------------
        if (empty($no_rekening)) {
            sendResponse(400, "Gagal: Nomor Rekening wajib diisi.");
            return;
        }

        $stmtCheck = $this->pdo->prepare("SELECT id FROM calon_cessie WHERE no_rekening = :no_rekening LIMIT 1");
        $stmtCheck->execute([':no_rekening' => $no_rekening]);
        
        if ($stmtCheck->fetch()) {
            // Jika ditemukan, langsung tolak dan hentikan proses
            sendResponse(400, "Gagal: Nomor Rekening '$no_rekening' sudah terdaftar di sistem.");
            return;
        }
        // ---------------------------------------------------------

        $cleanNumber = function($val) {
            return !empty($val) ? (float)str_replace('.', '', $val) : 0;
        };

        $data = [
            'kode_kantor' => $input['kode_kantor'] ?? '',
            'nama_kantor' => $input['nama_kantor'] ?? '',
            'no_rekening' => $no_rekening,
            'nama_nasabah' => $input['nama_nasabah'] ?? '',
            'alamat' => $input['alamat'] ?? '',
            'kolektibilitas' => $input['kolektibilitas'] ?? '',
            'ckpn' => $cleanNumber($input['ckpn'] ?? 0),
            'nilai_taksasi' => $cleanNumber($input['nilai_taksasi'] ?? 0),
            'nilai_apht' => $cleanNumber($input['nilai_apht'] ?? 0),
            'totung' => $cleanNumber($input['totung'] ?? 0),
            'baki_debet' => $cleanNumber($input['baki_debet'] ?? 0),
            'saldo_bank' => $cleanNumber($input['saldo_bank'] ?? 0),
            'nilai_agunan' => $cleanNumber($input['nilai_agunan'] ?? 0),
            'status' => $input['status'] ?? 'Draft'
        ];

        $sql = "INSERT INTO calon_cessie (kode_kantor, nama_kantor, no_rekening, nama_nasabah, alamat, kolektibilitas, ckpn, nilai_taksasi, nilai_apht, totung, baki_debet, saldo_bank, nilai_agunan, status) 
                VALUES (:kode_kantor, :nama_kantor, :no_rekening, :nama_nasabah, :alamat, :kolektibilitas, :ckpn, :nilai_taksasi, :nilai_apht, :totung, :baki_debet, :saldo_bank, :nilai_agunan, :status)";
        
        $stmt = $this->pdo->prepare($sql);
        if($stmt->execute($data)) {
            sendResponse(201, "Data master calon cessie berhasil ditambahkan", ["id" => $this->pdo->lastInsertId()]);
        } else {
            sendResponse(500, "Gagal menambahkan data");
        }
    }

    // ==========================================
    // 4. UPDATE DATA (PUT/POST JSON)
    // ==========================================
    public function update($id) {
        $stmtCheck = $this->pdo->prepare("SELECT * FROM calon_cessie WHERE id = ?");
        $stmtCheck->execute([$id]);
        $oldData = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if (!$oldData) {
            sendResponse(404, "Data tidak ditemukan");
            return;
        }

        // Tangkap JSON Body
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            sendResponse(400, "Format JSON tidak valid atau kosong");
            return;
        }

        $cleanNumber = function($val) { return !empty($val) ? (float)str_replace('.', '', $val) : 0; };

        // Kalau datanya nggak dikirim di JSON, pakai data lama yang ada di DB
        $data = [
            'id' => $id,
            'kode_kantor' => $input['kode_kantor'] ?? $oldData['kode_kantor'],
            'nama_kantor' => $input['nama_kantor'] ?? $oldData['nama_kantor'],
            'no_rekening' => $input['no_rekening'] ?? $oldData['no_rekening'],
            'nama_nasabah' => $input['nama_nasabah'] ?? $oldData['nama_nasabah'],
            'alamat' => $input['alamat'] ?? $oldData['alamat'],
            'kolektibilitas' => $input['kolektibilitas'] ?? $oldData['kolektibilitas'],
            'ckpn' => isset($input['ckpn']) ? $cleanNumber($input['ckpn']) : $oldData['ckpn'],
            'nilai_taksasi' => isset($input['nilai_taksasi']) ? $cleanNumber($input['nilai_taksasi']) : $oldData['nilai_taksasi'],
            'nilai_apht' => isset($input['nilai_apht']) ? $cleanNumber($input['nilai_apht']) : $oldData['nilai_apht'],
            'totung' => isset($input['totung']) ? $cleanNumber($input['totung']) : $oldData['totung'],
            'baki_debet' => isset($input['baki_debet']) ? $cleanNumber($input['baki_debet']) : $oldData['baki_debet'],
            'saldo_bank' => isset($input['saldo_bank']) ? $cleanNumber($input['saldo_bank']) : $oldData['saldo_bank'],
            'nilai_agunan' => isset($input['nilai_agunan']) ? $cleanNumber($input['nilai_agunan']) : $oldData['nilai_agunan'],
            'status' => $input['status'] ?? $oldData['status']
        ];

        $sql = "UPDATE calon_cessie SET kode_kantor=:kode_kantor, nama_kantor=:nama_kantor, no_rekening=:no_rekening, nama_nasabah=:nama_nasabah, alamat=:alamat, kolektibilitas=:kolektibilitas, ckpn=:ckpn, nilai_taksasi=:nilai_taksasi, nilai_apht=:nilai_apht, totung=:totung, baki_debet=:baki_debet, saldo_bank=:saldo_bank, nilai_agunan=:nilai_agunan, status=:status WHERE id=:id";
        
        $stmt = $this->pdo->prepare($sql);
        if($stmt->execute($data)) {
            sendResponse(200, "Data master calon cessie berhasil diupdate");
        } else {
            sendResponse(500, "Gagal mengupdate data");
        }
    }

    // ==========================================
    // 5. DELETE DATA (DELETE/POST)
    // ==========================================
    public function delete($id) {
        $stmtCheck = $this->pdo->prepare("SELECT id FROM calon_cessie WHERE id = ?");
        $stmtCheck->execute([$id]);
        if (!$stmtCheck->fetch()) {
            sendResponse(404, "Data tidak ditemukan");
            return;
        }

        // Hapus agunan terkait terlebih dahulu untuk menghindari Foreign Key Constraint
        $stmtDeleteAgunan = $this->pdo->prepare("DELETE FROM cessie_agunan WHERE id_calon_cessie = ?");
        $stmtDeleteAgunan->execute([$id]);

        // Setelah aman, hapus master cessie
        $stmt = $this->pdo->prepare("DELETE FROM calon_cessie WHERE id = ?");
        if ($stmt->execute([$id])) {
            sendResponse(200, "Data calon cessie berhasil dihapus");
        } else {
            sendResponse(500, "Gagal menghapus data");
        }
    }
}