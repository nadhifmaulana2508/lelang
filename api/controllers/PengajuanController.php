<?php
require_once __DIR__ . '/../helpers/response.php';

class PengajuanController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // ✅ CREATE PENGAJUAN
    public function store() {
        // Tangkap data JSON dari req.body form detail.php
        $input = json_decode(file_get_contents('php://input'), true);

        // Ambil value-nya
        $id_aset = $input['id_aset'] ?? null;
        $nama_lengkap = $input['nama_lengkap'] ?? null;
        $no_wa = $input['no_wa'] ?? null;
        $minat_skema = $input['minat_skema'] ?? null;
        $pesan = $input['pesan'] ?? '';

        // Validasi sederhana (Cegah input kosong)
        if (!$id_aset || !$nama_lengkap || !$no_wa || !$minat_skema) {
            sendResponse(400, "Gagal: Semua form berlogo (*) wajib diisi!");
            return;
        }

        // Insert ke database
        $sql = "INSERT INTO pengajuan_aset (id_aset, nama_lengkap, no_wa, minat_skema, pesan) 
                VALUES (:id_aset, :nama_lengkap, :no_wa, :minat_skema, :pesan)";
        
        $stmt = $this->pdo->prepare($sql);
        
        $berhasil = $stmt->execute([
            ':id_aset' => $id_aset,
            ':nama_lengkap' => $nama_lengkap,
            ':no_wa' => $no_wa,
            ':minat_skema' => $minat_skema,
            ':pesan' => $pesan
        ]);

        if ($berhasil) {
            sendResponse(200, "Mantap brokuu, Pengajuan berhasil disimpan ke database!");
        } else {
            sendResponse(500, "Waduh, Gagal menyimpan pengajuan");
        }
    }
}