<?php
// includes/cessie_logic.php
require_once __DIR__ . '/../../api/config/database.php';

if ($is_logged_in) {
    $upload_dir = __DIR__ . '/../../img/cessie/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

    // --- PROSES DELETE ---
    if (isset($_GET['delete_cessie']) && $is_superadmin) {
        $id_del = $_GET['delete_cessie'];
        $stmt = $pdo->prepare("DELETE FROM calon_cessie WHERE id = ?");
        $stmt->execute([$id_del]);
        header("Location: admin.php?page=calon_cessie&msg=deleted");
        exit;
    }

    // --- PROSES SIMPAN (CREATE & UPDATE) ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_cessie'])) {
        $id = $_POST['id'] ?? '';
        $is_update = !empty($id);

        // Ambil Data Teks (Hapus titik pada inputan uang)
        $data = [
            'kode_kantor' => $is_superadmin ? $_POST['kode_kantor'] : $user_kode,
            'nama_kantor' => $_POST['nama_kantor'] ?? '',
            'no_rekening' => $_POST['no_rekening'] ?? '',
            'nama_nasabah' => $_POST['nama_nasabah'] ?? '',
            'alamat' => $_POST['alamat'] ?? '',
            'kolektibilitas' => $_POST['kolektibilitas'] ?? '',
            'ckpn' => !empty($_POST['ckpn']) ? str_replace('.', '', $_POST['ckpn']) : 0,
            'nilai_taksasi' => !empty($_POST['nilai_taksasi']) ? str_replace('.', '', $_POST['nilai_taksasi']) : 0,
            'nilai_apht' => !empty($_POST['nilai_apht']) ? str_replace('.', '', $_POST['nilai_apht']) : 0,
            'totung' => !empty($_POST['totung']) ? str_replace('.', '', $_POST['totung']) : 0,
            'baki_debet' => !empty($_POST['baki_debet']) ? str_replace('.', '', $_POST['baki_debet']) : 0,
            'saldo_bank' => !empty($_POST['saldo_bank']) ? str_replace('.', '', $_POST['saldo_bank']) : 0,
            'nilai_agunan' => !empty($_POST['nilai_agunan']) ? str_replace('.', '', $_POST['nilai_agunan']) : 0,
            'status' => $_POST['status'] ?? 'Draft',
            'link_maps' => $_POST['link_maps'] ?? ''
        ];

        // Proses Upload 4 Foto
        foreach (['foto1', 'foto2', 'foto3', 'foto4'] as $f) {
            $data[$f] = $_POST['old_' . $f] ?? ''; 
            if (isset($_FILES[$f]) && $_FILES[$f]['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES[$f]['name'], PATHINFO_EXTENSION);
                $new_filename = 'cessie_' . time() . '_' . rand(100, 999) . '_' . $f . '.' . $ext;
                if (move_uploaded_file($_FILES[$f]['tmp_name'], $upload_dir . $new_filename)) {
                    $data[$f] = $new_filename;
                }
            }
        }

        if ($is_update) {
            $sql = "UPDATE calon_cessie SET kode_kantor=:kode_kantor, nama_kantor=:nama_kantor, no_rekening=:no_rekening, nama_nasabah=:nama_nasabah, alamat=:alamat, kolektibilitas=:kolektibilitas, ckpn=:ckpn, nilai_taksasi=:nilai_taksasi, nilai_apht=:nilai_apht, totung=:totung, baki_debet=:baki_debet, saldo_bank=:saldo_bank, nilai_agunan=:nilai_agunan, status=:status, link_maps=:link_maps, foto1=:foto1, foto2=:foto2, foto3=:foto3, foto4=:foto4 WHERE id=:id";
            if(!$is_superadmin) $sql .= " AND kode_kantor='$user_kode'";
            $data['id'] = $id;
            $stmt = $pdo->prepare($sql);
            $stmt->execute($data);
            header("Location: admin.php?page=calon_cessie&msg=updated");
        } else {
            $sql = "INSERT INTO calon_cessie (kode_kantor, nama_kantor, no_rekening, nama_nasabah, alamat, kolektibilitas, ckpn, nilai_taksasi, nilai_apht, totung, baki_debet, saldo_bank, nilai_agunan, status, link_maps, foto1, foto2, foto3, foto4) VALUES (:kode_kantor, :nama_kantor, :no_rekening, :nama_nasabah, :alamat, :kolektibilitas, :ckpn, :nilai_taksasi, :nilai_apht, :totung, :baki_debet, :saldo_bank, :nilai_agunan, :status, :link_maps, :foto1, :foto2, :foto3, :foto4)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($data);
            header("Location: admin.php?page=calon_cessie&msg=created");
        }
        exit;
    }
}
?>