<?php
// File: api/helpers/upload.php

function uploadFotoWebp($fileInfo, $targetFolder, $prefix = 'agunan') {
    if (!isset($fileInfo['tmp_name']) || empty($fileInfo['tmp_name']) || $fileInfo['error'] !== UPLOAD_ERR_OK) {
        return ['status' => false, 'msg' => 'Tidak ada file atau terjadi error upload.'];
    }

    $check = getimagesize($fileInfo["tmp_name"]);
    if($check === false) { return ['status' => false, 'msg' => 'File bukan gambar valid.']; }

    if ($fileInfo["size"] > 5000000) { return ['status' => false, 'msg' => 'Ukuran file maks 5MB.']; }

    if (!file_exists($targetFolder)) { mkdir($targetFolder, 0777, true); }

    // Fungsi Pembantu untuk Fallback Upload
    $fallbackUpload = function() use ($fileInfo, $targetFolder, $prefix) {
        $ext = strtolower(pathinfo($fileInfo['name'], PATHINFO_EXTENSION));
        if (!$ext) $ext = 'jpg';
        
        $generateNameFallback = $prefix . '_' . time() . '_' . substr(uniqid(), -5) . '.' . $ext;
        $targetFileFallback = rtrim($targetFolder, '/') . '/' . $generateNameFallback;
        
        if (move_uploaded_file($fileInfo['tmp_name'], $targetFileFallback)) {
            return ['status' => true, 'filename' => $generateNameFallback];
        } else {
            return ['status' => false, 'msg' => 'Gagal mengupload file (Fallback Mode).'];
        }
    };

    // FALLBACK: Jika server tidak mendukung GD image creation atau imagewebp
    if (!function_exists('imagewebp') || !function_exists('imagecreatefromjpeg') || !function_exists('imagecreatefrompng')) {
        return $fallbackUpload();
    }

    try {
        $generateName = $prefix . '_' . time() . '_' . substr(uniqid(), -5) . '.webp';
        $targetFile = rtrim($targetFolder, '/') . '/' . $generateName;

        $imageType = $check[2]; 
        $image = null;

        switch ($imageType) {
            case IMAGETYPE_JPEG: $image = imagecreatefromjpeg($fileInfo["tmp_name"]); break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($fileInfo["tmp_name"]);
                if ($image) {
                    imagepalettetotruecolor($image); imagealphablending($image, true); imagesavealpha($image, true);
                }
                break;
            case IMAGETYPE_GIF: $image = imagecreatefromgif($fileInfo["tmp_name"]); break;
            default: return $fallbackUpload();
        }

        if (!$image) { return $fallbackUpload(); }

        $success = imagewebp($image, $targetFile, 80);
        imagedestroy($image); 

        if ($success) { return ['status' => true, 'filename' => $generateName]; } 
        else { return $fallbackUpload(); }

    } catch (Throwable $e) {
        // Jika ada fatal error atau memory limit saat processing, langsung fallback
        return $fallbackUpload();
    }
}
?>