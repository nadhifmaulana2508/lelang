<?php
// Pastikan koneksi tersedia
require_once __DIR__ . '/../../api/config/database.php';

if ($is_logged_in) {
    $upload_dir = __DIR__ . '/../../img/agunan/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

    // --- PROSES DELETE ---
    if (isset($_GET['delete']) && $is_superadmin) {
        $id_del = $_GET['delete'];
        $stmt = $pdo->prepare("DELETE FROM dummy_asset WHERE id = ?");
        $stmt->execute([$id_del]);
        header("Location: " . BASE_URL . "/client/data?msg=deleted");
        exit;
    }

    // --- PROSES SIMPAN (CREATE & UPDATE) ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_asset'])) {
        $id = $_POST['id'] ?? '';
        $is_update = !empty($id);
        $tampil_val = isset($_POST['tampil']) ? 1 : 0;

        $data = [
            'kode_kantor' => $is_superadmin ? $_POST['kode_kantor'] : $user_kode,
            'rekening' => $_POST['rekening'] ?? '',
            'nasabah' => $_POST['nasabah'] ?? '',
            'alamat_asset' => $_POST['alamat_asset'] ?? '',
            'letak_jaminan' => $_POST['letak_jaminan'] ?? '',
            'jenis_agunan' => $_POST['jenis_agunan'] ?? '',
            'jenis_surat' => $_POST['jenis_surat'] ?? '',
            'nomor_surat' => $_POST['nomor_surat'] ?? '',
            'luas_bangunan' => !empty($_POST['luas_bangunan']) ? $_POST['luas_bangunan'] : 0,
            'luas_tanah' => !empty($_POST['luas_tanah']) ? $_POST['luas_tanah'] : 0,
            'lantai' => !empty($_POST['lantai']) ? $_POST['lantai'] : 0,
            'harga_jual' => !empty($_POST['harga_jual']) ? str_replace('.', '', $_POST['harga_jual']) : 0,
            'proses_penjualan' => $_POST['proses_penjualan'] ?? 'Jual',
            'deskripsi' => $_POST['deskripsi'] ?? '',
            'link_maps' => $_POST['link_maps'] ?? '',
            'latitude' => $_POST['latitude'] ?? '',
            'longitude' => $_POST['longitude'] ?? '',
            'tampil' => $tampil_val 
        ];

        // Upload Foto
        foreach (['foto1', 'foto2', 'foto3', 'foto4'] as $f) {
            $data[$f] = $_POST['old_' . $f] ?? '';
            if (isset($_FILES[$f]) && $_FILES[$f]['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES[$f]['name'], PATHINFO_EXTENSION);
                $new_filename = time() . '_' . rand(100, 999) . '_' . $f . '.' . $ext;
                if (move_uploaded_file($_FILES[$f]['tmp_name'], $upload_dir . $new_filename)) {
                    $data[$f] = $new_filename;
                }
            }
        }

        if ($is_update) {
            $sql = "UPDATE dummy_asset SET kode_kantor=:kode_kantor, rekening=:rekening, nasabah=:nasabah, alamat_asset=:alamat_asset, letak_jaminan=:letak_jaminan, jenis_agunan=:jenis_agunan, jenis_surat=:jenis_surat, nomor_surat=:nomor_surat, luas_bangunan=:luas_bangunan, luas_tanah=:luas_tanah, lantai=:lantai, harga_jual=:harga_jual, proses_penjualan=:proses_penjualan, deskripsi=:deskripsi, link_maps=:link_maps, latitude=:latitude, longitude=:longitude, tampil=:tampil, foto1=:foto1, foto2=:foto2, foto3=:foto3, foto4=:foto4 WHERE id=:id";
            if(!$is_superadmin) $sql .= " AND kode_kantor='$user_kode'";
            $data['id'] = $id;
            $stmt = $pdo->prepare($sql);
            $stmt->execute($data);
            header("Location: " . BASE_URL . "/client/data?msg=updated");
        } else {
            $sql = "INSERT INTO dummy_asset (kode_kantor, rekening, nasabah, alamat_asset, letak_jaminan, jenis_agunan, jenis_surat, nomor_surat, luas_bangunan, luas_tanah, lantai, harga_jual, proses_penjualan, deskripsi, link_maps, latitude, longitude, tampil, foto1, foto2, foto3, foto4) VALUES (:kode_kantor, :rekening, :nasabah, :alamat_asset, :letak_jaminan, :jenis_agunan, :jenis_surat, :nomor_surat, :luas_bangunan, :luas_tanah, :lantai, :harga_jual, :proses_penjualan, :deskripsi, :link_maps, :latitude, :longitude, :tampil, :foto1, :foto2, :foto3, :foto4)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($data);
            header("Location: " . BASE_URL . "/client/data?msg=created");
        }
        exit;
    }
}