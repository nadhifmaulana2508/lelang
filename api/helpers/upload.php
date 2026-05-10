<?php
// File: api/helpers/upload.php

function uploadFotoWebp($fileInfo, $targetFolder, $prefix = 'agunan') {
    // Validasi dasar: file harus ada dan tidak error
    if (!isset($fileInfo['tmp_name']) || empty($fileInfo['tmp_name']) || $fileInfo['error'] !== UPLOAD_ERR_OK) {
        $phpErrors = [
            1 => 'File melebihi upload_max_filesize di php.ini',
            2 => 'File melebihi MAX_FILE_SIZE di form HTML',
            3 => 'File hanya ter-upload sebagian',
            4 => 'Tidak ada file yang diupload',
            6 => 'Folder temp tidak ditemukan di server',
            7 => 'Gagal menulis file ke disk — cek permission folder',
        ];
        $errMsg = $phpErrors[$fileInfo['error'] ?? 0] ?? 'Error upload tidak diketahui (kode: ' . ($fileInfo['error'] ?? '?') . ')';
        error_log("[UPLOAD ERROR] {$errMsg} | File: {$fileInfo['name']}");
        return ['status' => false, 'msg' => $errMsg];
    }

    // Validasi file adalah gambar
    $check = getimagesize($fileInfo["tmp_name"]);
    if ($check === false) { return ['status' => false, 'msg' => 'File bukan gambar valid.']; }

    // Validasi Maksimal 2MB
    if ($fileInfo["size"] > 2000000) { 
        return ['status' => false, 'msg' => 'Ukuran file foto maksimal 2MB. Ukuran file Anda: ' . round($fileInfo['size']/1024/1024, 2) . 'MB']; 
    }

    // Pastikan folder tujuan ada dan bisa ditulis
    if (!file_exists($targetFolder)) {
        if (!@mkdir($targetFolder, 0777, true)) {
            error_log("[UPLOAD ERROR] Gagal membuat folder: {$targetFolder}");
            return ['status' => false, 'msg' => 'Gagal membuat folder upload di server. Hubungi administrator.'];
        }
    }

    if (!is_writable($targetFolder)) {
        error_log("[UPLOAD ERROR] Folder tidak writable: {$targetFolder}");
        return ['status' => false, 'msg' => 'Folder upload tidak memiliki izin tulis. Hubungi administrator server.'];
    }

    // Tentukan ekstensi file
    $ext = strtolower(pathinfo($fileInfo['name'], PATHINFO_EXTENSION));
    if (!$ext || !in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
        $imageType = $check[2];
        if ($imageType === IMAGETYPE_JPEG) $ext = 'jpg';
        elseif ($imageType === IMAGETYPE_PNG) $ext = 'png';
        elseif ($imageType === IMAGETYPE_GIF) $ext = 'gif';
        else $ext = 'jpg';
    }

    // Rename file agar unik
    $generateName = $prefix . '_' . time() . '_' . substr(uniqid(), -5) . '.' . $ext;
    $targetFile = rtrim($targetFolder, '/') . '/' . $generateName;

    try {
        if (move_uploaded_file($fileInfo['tmp_name'], $targetFile)) {
            return ['status' => true, 'filename' => $generateName];
        } else {
            $errDetail = "Target: {$targetFile} | is_uploaded: " . (is_uploaded_file($fileInfo['tmp_name']) ? 'yes' : 'no');
            error_log("[UPLOAD ERROR] move_uploaded_file gagal. {$errDetail}");
            return ['status' => false, 'msg' => 'Gagal memindahkan file. Pastikan folder uploads/ dapat ditulis (chmod 755 atau 777).'];
        }
    } catch (Throwable $e) {
        error_log("[UPLOAD EXCEPTION] " . $e->getMessage());
        return ['status' => false, 'msg' => 'Exception saat upload: ' . $e->getMessage()];
    }
}
?>