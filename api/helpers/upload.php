<?php
// File: api/helpers/upload.php

function uploadFotoWebp($fileInfo, $targetFolder, $prefix = 'agunan') {
    if (!isset($fileInfo['tmp_name']) || empty($fileInfo['tmp_name']) || $fileInfo['error'] !== UPLOAD_ERR_OK) {
        return ['status' => false, 'msg' => 'Tidak ada file atau terjadi error upload.'];
    }

    $check = getimagesize($fileInfo["tmp_name"]);
    if($check === false) { return ['status' => false, 'msg' => 'File bukan gambar valid.']; }

    // Validasi Maksimal 2MB sesuai instruksi
    if ($fileInfo["size"] > 2000000) { 
        return ['status' => false, 'msg' => 'Ukuran file foto maksimal 2MB.']; 
    }

    if (!file_exists($targetFolder)) { mkdir($targetFolder, 0777, true); }

    $ext = strtolower(pathinfo($fileInfo['name'], PATHINFO_EXTENSION));
    if (!$ext) {
        // Coba deteksi dari mime type jika extension kosong
        $imageType = $check[2];
        if ($imageType === IMAGETYPE_JPEG) $ext = 'jpg';
        elseif ($imageType === IMAGETYPE_PNG) $ext = 'png';
        elseif ($imageType === IMAGETYPE_GIF) $ext = 'gif';
        else $ext = 'jpg';
    }

    // Rename file agar unik tanpa mengubah format
    $generateName = $prefix . '_' . time() . '_' . substr(uniqid(), -5) . '.' . $ext;
    $targetFile = rtrim($targetFolder, '/') . '/' . $generateName;

    try {
        if (move_uploaded_file($fileInfo['tmp_name'], $targetFile)) {
            return ['status' => true, 'filename' => $generateName];
        } else {
            return ['status' => false, 'msg' => 'Gagal memindahkan file ke server.'];
        }
    } catch (Throwable $e) {
        return ['status' => false, 'msg' => 'Gagal mengupload file: ' . $e->getMessage()];
    }
}
?>