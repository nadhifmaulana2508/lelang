<?php

require_once __DIR__ . '/../helpers/response.php';

class CatalogController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

// ✅ READ ALL (KATALOG) DENGAN FILTER & PAGINATION
    public function getAll() {
        // 1. Tangkap parameter dari URL (GET)
        $keyword = $_GET['keyword'] ?? '';
        $status = $_GET['status'] ?? 'Semua';
        $jenis = $_GET['jenis'] ?? 'Semua';
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 8; // Default 8 data
        $offset = ($page - 1) * $limit;

        // 2. Siapkan pondasi Query
        $whereClauses = ["tampil = 1"];
        $params = [];

        // Filter Keyword (Pencarian Text)
        if (!empty($keyword)) {
            $whereClauses[] = "(nomor_surat LIKE :kw OR deskripsi LIKE :kw OR alamat_asset LIKE :kw OR jenis_surat LIKE :kw)";
            $params[':kw'] = "%$keyword%";
        }

        // Filter Status
        if ($status !== 'Semua' && !empty($status)) {
            $whereClauses[] = "proses_penjualan = :status";
            $params[':status'] = $status;
        }

        // Filter Jenis Aset
        if ($jenis !== 'Semua' && !empty($jenis)) {
            if ($jenis === 'Tanah') {
                $whereClauses[] = "jenis_surat LIKE '%SHM%' AND (luas_bangunan IS NULL OR luas_bangunan = 0)";
            } elseif ($jenis === 'Bangunan') {
                $whereClauses[] = "luas_bangunan > 0";
            } elseif ($jenis === 'Kendaraan') {
                $whereClauses[] = "jenis_surat LIKE '%BPKB%'";
            }
        }

        // Gabungkan semua kondisi WHERE
        $whereSql = implode(' AND ', $whereClauses);

        // 3. Hitung total data untuk Pagination
        $countSql = "SELECT COUNT(*) FROM dummy_asset WHERE $whereSql";
        $stmtCount = $this->pdo->prepare($countSql);
        foreach ($params as $key => $val) {
            $stmtCount->bindValue($key, $val);
        }
        $stmtCount->execute();
        $totalData = $stmtCount->fetchColumn();
        $totalPages = ceil($totalData / $limit);

        // 4. Ambil data sesuai filter & limit
        $sql = "
            SELECT 
                id, foto1, harga_jual, luas_bangunan, luas_tanah, 
                jenis_surat, nomor_surat, proses_penjualan, alamat_asset
            FROM dummy_asset
            WHERE $whereSql
            ORDER BY id DESC
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->pdo->prepare($sql);
        // Bind parameter filter
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        // Bind parameter pagination (Wajib tipe integer)
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 5. Bungkus response dengan metadata pagination
        $response = [
            'data' => $data,
            'pagination' => [
                'total_data' => $totalData,
                'total_pages' => $totalPages,
                'current_page' => $page,
                'limit' => $limit
            ]
        ];

        sendResponse(200, "Berhasil ambil data catalog dengan filter", $response);
    }
    

    // ✅ READ HOME - PUBLIC (Menampilkan aset tanpa filter rumit, cukup limit saja)
    public function getHome() {
        // Perbaikan syntax: WHERE harus sebelum ORDER BY
        $sql = "
            SELECT 
                id, foto1, harga_jual, luas_bangunan, luas_tanah, 
                jenis_surat, nomor_surat, proses_penjualan, alamat_asset
            FROM dummy_asset 
            WHERE tampil = 1
            ORDER BY id DESC
            LIMIT 10
        ";
    
        $stmt = $this->pdo->query($sql);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Response home kita bikin struktur array 'data' langsung biar sama dengan fetch FE sebelumnya
        sendResponse(200, "Berhasil ambil data home dummy asset", $data);
    }
    
    
    
    

    // ✅ READ BY JENIS JAMINAN - PUBLIC
    public function getByJenisAgunan($jenis) {
        $sql = "
            SELECT * 
            FROM dummy_asset 
            WHERE jenis_agunan = :jenis
        ";
    
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':jenis' => $jenis]);
        $data = $stmt->fetchAll();
    
        sendResponse(200, "Berhasil ambil data berdasarkan jenis agunan", $data);
    }
    


    // ✅ READ DETAIL BY ID - PUBLIC
    public function getDetail($id) {
        $sql = "
            SELECT 
                da.*, 
                kk.nama_kantor
            FROM 
                dummy_asset da
            INNER JOIN 
                kode_kantor kk 
                ON da.kode_kantor = kk.kode_kantor
            WHERE 
                da.id = :id
        ";
    
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch();
    
        if ($data) {
            sendResponse(200, "Detail dummy asset ditemukan", $data);
        } else {
            sendResponse(404, "Dummy asset tidak ditemukan");
        }
    }

    // ✅ TRACK VIEW COUNT - Ditaruh di dalam class CatalogController
    public function trackView() {
        // Tangkap data JSON dari req.body
        $input = json_decode(file_get_contents('php://input'), true);
        $id_aset = $input['id_aset'] ?? null;

        if (!$id_aset) {
            sendResponse(400, "Gagal: ID Aset tidak ditemukan");
            return;
        }

        // Update nilai view_count + 1
        $sql = "UPDATE dummy_asset SET view_count = view_count + 1 WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        
        if ($stmt->execute([':id' => $id_aset])) {
            sendResponse(200, "View count berhasil diupdate");
        } else {
            sendResponse(500, "Gagal mengupdate view count");
        }
    }
    
    



}
