<?php
require_once __DIR__ . '/../helpers/response.php';

class DashboardController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getStats() {
        $kode_kantor = $_GET['kode_kantor'] ?? '';
        $status = $_GET['status'] ?? 'Semua';

        // ==========================================
        // BASE FILTER: HANYA CABANG (Berlaku untuk semua widget)
        // ==========================================
        $whereCabang = ["1=1"];
        $paramsCabang = [];

        if (!empty($kode_kantor)) {
            if ($kode_kantor === 'KORWIL_SMG') {
                $whereCabang[] = "kode_kantor IN ('001','002','003','004','005','006','007')";
            } elseif ($kode_kantor === 'KORWIL_SLO') {
                $whereCabang[] = "kode_kantor IN ('008','009','010','011','012','013','014')";
            } elseif ($kode_kantor === 'KORWIL_BMS') {
                $whereCabang[] = "kode_kantor IN ('015','016','017','018','019','020','021')";
            } elseif ($kode_kantor === 'KORWIL_PKL') {
                $whereCabang[] = "kode_kantor IN ('022','023','024','025','026','027','028')";
            } else {
                $whereCabang[] = "kode_kantor = :kode_kantor";
                $paramsCabang[':kode_kantor'] = $kode_kantor;
            }
        }
        $sqlCabang = implode(' AND ', $whereCabang);

        // ==========================================
        // 1. QUERY WIDGET CARDS (Tergantung Filter Status Dropdown)
        // ==========================================
        $whereCards = $whereCabang;
        $paramsCards = $paramsCabang;

        if ($status !== 'Semua' && !empty($status)) {
            $whereCards[] = "status = :status";
            $paramsCards[':status'] = $status;
        }
        $sqlCardsWhere = implode(' AND ', $whereCards);

        $sqlCards = "SELECT 
                        COUNT(*) as total_debitur,
                        SUM(baki_debet) as total_baki_debet,
                        SUM(nilai_agunan) as total_agunan
                     FROM calon_cessie WHERE $sqlCardsWhere";
        $stmtCards = $this->pdo->prepare($sqlCards);
        $stmtCards->execute($paramsCards);
        $dataCards = $stmtCards->fetch(PDO::FETCH_ASSOC);

        // Card Status Spesifik (Abaikan filter status dropdown)
        $stmtSiap = $this->pdo->prepare("SELECT COUNT(*) FROM calon_cessie WHERE $sqlCabang AND status = 'Siap Ditawarkan'");
        $stmtSiap->execute($paramsCabang);
        $siapCount = $stmtSiap->fetchColumn();

        $stmtMinat = $this->pdo->prepare("SELECT COUNT(*) FROM calon_cessie WHERE $sqlCabang AND status = 'Diminati'");
        $stmtMinat->execute($paramsCabang);
        $minatCount = $stmtMinat->fetchColumn();


        // ==========================================
        // 2. QUERY CHART & STATUS KESIAPAN (Group by Status)
        // ==========================================
        $sqlChart = "SELECT 
                        status,
                        COUNT(*) as jumlah,
                        SUM(baki_debet) as sum_baki,
                        SUM(nilai_agunan) as sum_agunan
                     FROM calon_cessie 
                     WHERE $sqlCabang 
                     GROUP BY status";
        $stmtChart = $this->pdo->prepare($sqlChart);
        $stmtChart->execute($paramsCabang);
        $chartRaw = $stmtChart->fetchAll(PDO::FETCH_ASSOC);

        $statusCounts = [
            'Draft' => 0, 'Review Legal' => 0, 'Siap Ditawarkan' => 0,
            'Diminati' => 0, 'Butuh Update' => 0
        ];
        $chartData = [];

        foreach ($chartRaw as $row) {
            $st = !empty($row['status']) ? $row['status'] : 'Draft';
            $statusCounts[$st] = (int)$row['jumlah'];
            $chartData[] = [
                'status' => $st,
                'baki_debet' => (float)$row['sum_baki'],
                'nilai_agunan' => (float)$row['sum_agunan']
            ];
        }

        // ==========================================
        // 3. QUERY TABEL PRIORITAS CESSIE (Top 5 Baki Debet Tertinggi)
        // ==========================================
        $sqlPrioritas = "SELECT id, nama_nasabah, kolektibilitas, nama_kantor, baki_debet, nilai_agunan, status 
                         FROM calon_cessie 
                         WHERE $sqlCabang AND status != 'Draft'
                         ORDER BY baki_debet DESC LIMIT 5";
        $stmtPrio = $this->pdo->prepare($sqlPrioritas);
        $stmtPrio->execute($paramsCabang);
        $prioritasData = $stmtPrio->fetchAll(PDO::FETCH_ASSOC);


        // ==========================================
        // 4. QUERY AGUNAN UNGGULAN (Placeholder tanpa Join dummy_asset)
        // Mengambil dari master calon_cessie sementara menunggu tabel cessie_agunan siap
        // ==========================================
        $sqlAgunan = "SELECT id, 
                             nama_nasabah as jenis_surat, -- Dipinjam untuk Judul Card UI
                             alamat as alamat_asset, 
                             nilai_agunan as harga_jual, 
                             '' as foto1, -- Placeholder agar tidak error UI
                             baki_debet, 
                             nilai_agunan 
                      FROM calon_cessie 
                      WHERE $sqlCabang AND status = 'Siap Ditawarkan' 
                      ORDER BY nilai_agunan DESC LIMIT 2";
        $stmtAgunan = $this->pdo->prepare($sqlAgunan);
        $stmtAgunan->execute($paramsCabang);
        $agunanData = $stmtAgunan->fetchAll(PDO::FETCH_ASSOC);


        // ==========================================
        // 5. BUNGKUS SEMUA KE JSON RESPONSE
        // ==========================================
        $response = [
            'cards' => [
                'total_debitur' => (int)$dataCards['total_debitur'],
                'total_baki_debet' => (float)$dataCards['total_baki_debet'],
                'total_agunan' => (float)$dataCards['total_agunan'],
                'siap_ditawarkan' => (int)$siapCount,
                'minat_pembeli' => (int)$minatCount
            ],
            'chart' => $chartData,
            'status_counts' => $statusCounts,
            'prioritas' => $prioritasData,     
            'agunan_unggulan' => $agunanData   
        ];

        sendResponse(200, "Berhasil ambil data dashboard", $response);
    }
}