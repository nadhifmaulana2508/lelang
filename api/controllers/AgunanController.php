<?php
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/upload.php'; 

class AgunanController {
    private $pdo;
    private $uploadDir;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->uploadDir = __DIR__ . '/../../uploads/agunan'; 
    }

    // ==========================================
    // 1. CREATE DATA (Anti-Badai Strict Mode)
    // ==========================================
    public function create() {
        $id_calon_cessie = $_POST['id_calon_cessie'] ?? '';
        $jenis_agunan = $_POST['jenis_agunan'] ?? '';
        $alamat_agunan = $_POST['alamat_agunan'] ?? '';
        
        if (empty($id_calon_cessie) || empty($jenis_agunan) || empty($alamat_agunan)) {
            sendResponse(400, "ID Calon Cessie, Jenis Agunan, dan Alamat wajib diisi!");
            return;
        }

        // Proses Upload 4 FOTO
        $namaFoto1 = null; $namaFoto2 = null; $namaFoto3 = null; $namaFoto4 = null;
        if (isset($_FILES['foto1']) && $_FILES['foto1']['error'] === UPLOAD_ERR_OK) {
            $up1 = uploadFotoWebp($_FILES['foto1'], $this->uploadDir, 'f1');
            if ($up1['status']) $namaFoto1 = $up1['filename'];
        }
        if (isset($_FILES['foto2']) && $_FILES['foto2']['error'] === UPLOAD_ERR_OK) {
            $up2 = uploadFotoWebp($_FILES['foto2'], $this->uploadDir, 'f2');
            if ($up2['status']) $namaFoto2 = $up2['filename'];
        }
        if (isset($_FILES['foto3']) && $_FILES['foto3']['error'] === UPLOAD_ERR_OK) {
            $up3 = uploadFotoWebp($_FILES['foto3'], $this->uploadDir, 'f3');
            if ($up3['status']) $namaFoto3 = $up3['filename'];
        }
        if (isset($_FILES['foto4']) && $_FILES['foto4']['error'] === UPLOAD_ERR_OK) {
            $up4 = uploadFotoWebp($_FILES['foto4'], $this->uploadDir, 'f4');
            if ($up4['status']) $namaFoto4 = $up4['filename'];
        }

        $sql = "INSERT INTO cessie_agunan 
                (id_calon_cessie, jenis_agunan, jenis_surat, nomor_surat, alamat_agunan, koordinat, link_maps, 
                 luas_tanah, luas_bangunan, nilai_pasar, nilai_likuidasi, deskripsi, status, tampil, 
                 foto1, foto2, foto3, foto4, views) 
                VALUES 
                (:id_calon_cessie, :jenis_agunan, :jenis_surat, :nomor_surat, :alamat_agunan, :koordinat, :link_maps,
                 :luas_tanah, :luas_bangunan, :nilai_pasar, :nilai_likuidasi, :deskripsi, :status, :tampil, 
                 :foto1, :foto2, :foto3, :foto4, 0)";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':id_calon_cessie' => (int)$id_calon_cessie,
                ':jenis_agunan' => $jenis_agunan,
                ':jenis_surat' => !empty($_POST['jenis_surat']) ? $_POST['jenis_surat'] : null,
                ':nomor_surat' => !empty($_POST['nomor_surat']) ? $_POST['nomor_surat'] : null,
                ':alamat_agunan' => $alamat_agunan,
                ':koordinat' => !empty($_POST['koordinat']) ? $_POST['koordinat'] : null,
                ':link_maps' => !empty($_POST['link_maps']) ? $_POST['link_maps'] : null,
                // Cast ke angka untuk cegah SQL Strict Mode Error
                ':luas_tanah' => (int)($_POST['luas_tanah'] ?: 0),
                ':luas_bangunan' => (int)($_POST['luas_bangunan'] ?: 0),
                ':nilai_pasar' => (float)($_POST['nilai_pasar'] ?: 0),
                ':nilai_likuidasi' => (float)($_POST['nilai_likuidasi'] ?: 0),
                ':deskripsi' => !empty($_POST['deskripsi']) ? $_POST['deskripsi'] : null,
                ':status' => !empty($_POST['status']) ? $_POST['status'] : 'Open',
                ':tampil' => isset($_POST['tampil']) ? (int)$_POST['tampil'] : 1,
                ':foto1' => $namaFoto1,
                ':foto2' => $namaFoto2,
                ':foto3' => $namaFoto3,
                ':foto4' => $namaFoto4
            ]);
            sendResponse(201, "Data Agunan berhasil disimpan!");
        } catch (PDOException $e) {
            sendResponse(500, "Gagal menyimpan: " . $e->getMessage());
        }
    }

    // ==========================================
    // 2. UPDATE DATA (Fix id_calon_cessie)
    // ==========================================
    public function update() {
        $id = $_GET['id'] ?? $_POST['id'] ?? null;
        
        if (!$id || empty($_POST['jenis_agunan']) || empty($_POST['alamat_agunan'])) {
            sendResponse(400, "ID, Jenis Agunan, dan Alamat wajib diisi!");
            return;
        }

        $stmtOld = $this->pdo->prepare("SELECT foto1, foto2, foto3, foto4 FROM cessie_agunan WHERE id = :id");
        $stmtOld->execute([':id' => $id]);
        $oldData = $stmtOld->fetch(PDO::FETCH_ASSOC);

        if (!$oldData) {
            sendResponse(404, "Data agunan tidak ditemukan!");
            return;
        }

        $namaFoto1 = $oldData['foto1']; $namaFoto2 = $oldData['foto2']; 
        $namaFoto3 = $oldData['foto3']; $namaFoto4 = $oldData['foto4'];

        if (isset($_FILES['foto1']) && $_FILES['foto1']['error'] === UPLOAD_ERR_OK) {
            $up1 = uploadFotoWebp($_FILES['foto1'], $this->uploadDir, 'f1');
            if ($up1['status']) {
                if($namaFoto1 && file_exists($this->uploadDir.'/'.$namaFoto1)) @unlink($this->uploadDir.'/'.$namaFoto1);
                $namaFoto1 = $up1['filename'];
            }
        }
        if (isset($_FILES['foto2']) && $_FILES['foto2']['error'] === UPLOAD_ERR_OK) {
            $up2 = uploadFotoWebp($_FILES['foto2'], $this->uploadDir, 'f2');
            if ($up2['status']) {
                if($namaFoto2 && file_exists($this->uploadDir.'/'.$namaFoto2)) @unlink($this->uploadDir.'/'.$namaFoto2);
                $namaFoto2 = $up2['filename'];
            }
        }
        if (isset($_FILES['foto3']) && $_FILES['foto3']['error'] === UPLOAD_ERR_OK) {
            $up3 = uploadFotoWebp($_FILES['foto3'], $this->uploadDir, 'f3');
            if ($up3['status']) {
                if($namaFoto3 && file_exists($this->uploadDir.'/'.$namaFoto3)) @unlink($this->uploadDir.'/'.$namaFoto3);
                $namaFoto3 = $up3['filename'];
            }
        }
        if (isset($_FILES['foto4']) && $_FILES['foto4']['error'] === UPLOAD_ERR_OK) {
            $up4 = uploadFotoWebp($_FILES['foto4'], $this->uploadDir, 'f4');
            if ($up4['status']) {
                if($namaFoto4 && file_exists($this->uploadDir.'/'.$namaFoto4)) @unlink($this->uploadDir.'/'.$namaFoto4);
                $namaFoto4 = $up4['filename'];
            }
        }

        // KUNCI FIX: Tambahkan id_calon_cessie di script UPDATE
        $sql = "UPDATE cessie_agunan SET 
                id_calon_cessie = :id_calon_cessie,
                jenis_agunan = :jenis_agunan, jenis_surat = :jenis_surat, nomor_surat = :nomor_surat, 
                alamat_agunan = :alamat_agunan, koordinat = :koordinat, link_maps = :link_maps,
                luas_tanah = :luas_tanah, luas_bangunan = :luas_bangunan, 
                nilai_pasar = :nilai_pasar, nilai_likuidasi = :nilai_likuidasi, 
                deskripsi = :deskripsi, status = :status, tampil = :tampil,
                foto1 = :foto1, foto2 = :foto2, foto3 = :foto3, foto4 = :foto4
                WHERE id = :id";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':id' => $id,
                ':id_calon_cessie' => (int)$_POST['id_calon_cessie'],
                ':jenis_agunan' => $_POST['jenis_agunan'],
                ':jenis_surat' => !empty($_POST['jenis_surat']) ? $_POST['jenis_surat'] : null,
                ':nomor_surat' => !empty($_POST['nomor_surat']) ? $_POST['nomor_surat'] : null,
                ':alamat_agunan' => $_POST['alamat_agunan'],
                ':koordinat' => !empty($_POST['koordinat']) ? $_POST['koordinat'] : null,
                ':link_maps' => !empty($_POST['link_maps']) ? $_POST['link_maps'] : null,
                ':luas_tanah' => (int)($_POST['luas_tanah'] ?: 0),
                ':luas_bangunan' => (int)($_POST['luas_bangunan'] ?: 0),
                ':nilai_pasar' => (float)($_POST['nilai_pasar'] ?: 0),
                ':nilai_likuidasi' => (float)($_POST['nilai_likuidasi'] ?: 0),
                ':deskripsi' => !empty($_POST['deskripsi']) ? $_POST['deskripsi'] : null,
                ':status' => !empty($_POST['status']) ? $_POST['status'] : 'Open',
                ':tampil' => isset($_POST['tampil']) ? (int)$_POST['tampil'] : 1,
                ':foto1' => $namaFoto1,
                ':foto2' => $namaFoto2,
                ':foto3' => $namaFoto3,
                ':foto4' => $namaFoto4
            ]);
            sendResponse(200, "Data Agunan berhasil diupdate!");
        } catch (PDOException $e) {
            sendResponse(500, "Gagal update: " . $e->getMessage());
        }
    }

    // ==========================================
    // 3. READ ALL (Dengan Filter Cabang & Search)
    // ==========================================
    public function getAll() {
        $id_calon_cessie = $_GET['id_calon_cessie'] ?? null;
        $kode_kantor = $_GET['kode_kantor'] ?? null;
        $search = $_GET['search'] ?? null;
        
        try {
            // Join sesuai request: kode_kantor, nama_kantor, nama_nasabah, no_rekening, kol, baki
            $sql = "SELECT a.*, 
                           c.kode_kantor, c.nama_kantor, c.nama_nasabah, c.no_rekening, 
                           c.kolektibilitas, c.baki_debet 
                    FROM cessie_agunan a
                    JOIN calon_cessie c ON a.id_calon_cessie = c.id
                    WHERE 1=1";
            $params = [];

            // Filter by ID Nasabah (Jika di klik dari tabel Calon Cessie)
            if ($id_calon_cessie) {
                $sql .= " AND a.id_calon_cessie = :id_calon_cessie";
                $params[':id_calon_cessie'] = $id_calon_cessie;
            }

            // Filter by Kode Kantor (Selain 'Konsolidasi')
            if ($kode_kantor && $kode_kantor !== 'Konsolidasi' && $kode_kantor !== '') {
                $sql .= " AND c.kode_kantor = :kode_kantor";
                $params[':kode_kantor'] = $kode_kantor;
            }

            // Filter Search (Cari jenis, alamat aset, nama nasabah, atau no rekening)
            if ($search) {
                $sql .= " AND (a.jenis_agunan LIKE :search OR a.alamat_agunan LIKE :search OR c.nama_nasabah LIKE :search OR c.no_rekening LIKE :search)";
                $params[':search'] = "%$search%";
            }

            $sql .= " ORDER BY a.id DESC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            sendResponse(200, "Berhasil ambil data agunan", ['data' => $data]);
            
        } catch (PDOException $e) {
            // Kalau ada error SQL, buang ke JSON, jangan dibikin Fatal Error!
            sendResponse(500, "Gagal mengambil data dari database: " . $e->getMessage());
        }
    }

    // ==========================================
    // 4. GET DETAIL (Khusus Admin - Tidak Nambah Views)
    // ==========================================
    public function getDetail() {
        $id = $_GET['id'] ?? null;
        if (!$id) { sendResponse(400, "ID tidak ditemukan"); return; }

        try {
            // FIX BUG: Hapus c.alamat dan samakan kolomnya dengan getAll
            $sql = "SELECT a.*, 
                           c.kode_kantor, c.nama_kantor, c.nama_nasabah, c.no_rekening, 
                           c.kolektibilitas, c.baki_debet 
                    FROM cessie_agunan a
                    JOIN calon_cessie c ON a.id_calon_cessie = c.id
                    WHERE a.id = :id";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data) sendResponse(200, "Berhasil ambil detail admin", ['data' => $data]);
            else sendResponse(404, "Data tidak ditemukan");
            
        } catch (PDOException $e) {
            sendResponse(500, "Error Database: " . $e->getMessage());
        }
    }

    // ==========================================
    // 4b. GET VIEW (Khusus Public/User - Nambah Views +1)
    // ==========================================
    public function getView() {
        $id = $_GET['id'] ?? null;
        if (!$id) { sendResponse(400, "ID tidak ditemukan"); return; }

        try {
            // 1. Tambah view dulu
            $stmtUpdate = $this->pdo->prepare("UPDATE cessie_agunan SET views = views + 1 WHERE id = :id");
            $stmtUpdate->execute([':id' => $id]);

            // 2. Ambil datanya (FIX BUG: Hapus c.alamat)
            $sql = "SELECT a.*, 
                           c.kode_kantor, c.nama_kantor, c.nama_nasabah, c.no_rekening, 
                           c.kolektibilitas, c.baki_debet 
                    FROM cessie_agunan a
                    JOIN calon_cessie c ON a.id_calon_cessie = c.id
                    WHERE a.id = :id";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data) sendResponse(200, "Berhasil ambil view user", ['data' => $data]);
            else sendResponse(404, "Data tidak ditemukan");
            
        } catch (PDOException $e) {
            sendResponse(500, "Error Database: " . $e->getMessage());
        }
    }

    // ==========================================
    // 5. DELETE DATA
    // ==========================================
    public function delete() {
        $id = $_GET['id'] ?? null;
        if (!$id) { sendResponse(400, "ID tidak valid"); return; }

        try {
            $stmtFile = $this->pdo->prepare("SELECT foto1, foto2, foto3, foto4 FROM cessie_agunan WHERE id = :id");
            $stmtFile->execute([':id' => $id]);
            $files = $stmtFile->fetch(PDO::FETCH_ASSOC);

            if ($files) {
                if ($files['foto1'] && file_exists($this->uploadDir.'/'.$files['foto1'])) @unlink($this->uploadDir.'/'.$files['foto1']);
                if ($files['foto2'] && file_exists($this->uploadDir.'/'.$files['foto2'])) @unlink($this->uploadDir.'/'.$files['foto2']);
                if ($files['foto3'] && file_exists($this->uploadDir.'/'.$files['foto3'])) @unlink($this->uploadDir.'/'.$files['foto3']);
                if ($files['foto4'] && file_exists($this->uploadDir.'/'.$files['foto4'])) @unlink($this->uploadDir.'/'.$files['foto4']);
            }

            $stmt = $this->pdo->prepare("DELETE FROM cessie_agunan WHERE id = :id");
            $stmt->execute([':id' => $id]);
            sendResponse(200, "Agunan dan fotonya berhasil dihapus bersih!");
        } catch (PDOException $e) {
            sendResponse(500, "Gagal menghapus: " . $e->getMessage());
        }
    }
}