<?php
// File: api/helpers/upload.php

function uploadFotoWebp($fileInfo, $targetFolder, $prefix = 'agunan') {
    if (!isset($fileInfo['tmp_name']) || empty($fileInfo['tmp_name']) || $fileInfo['error'] !== UPLOAD_ERR_OK) {
        return ['status' => false, 'msg' => 'Tidak ada file atau terjadi error upload.'];
    }

    $check = getimagesize($fileInfo["tmp_name"]);
    if($check === false) { return ['status' => false, 'msg' => 'File bukan gambar valid.']; }

    if ($fileInfo["size"] > 5000000) { return ['status' => false, 'msg' => 'Ukuran file maks 5MB.']; }

    $generateName = $prefix . '_' . time() . '_' . substr(uniqid(), -5) . '.webp';
    $targetFile = rtrim($targetFolder, '/') . '/' . $generateName;

    if (!file_exists($targetFolder)) { mkdir($targetFolder, 0777, true); }

    $imageType = $check[2]; 
    $image = null;

    switch ($imageType) {
        case IMAGETYPE_JPEG: $image = imagecreatefromjpeg($fileInfo["tmp_name"]); break;
        case IMAGETYPE_PNG:
            $image = imagecreatefrompng($fileInfo["tmp_name"]);
            imagepalettetotruecolor($image); imagealphablending($image, true); imagesavealpha($image, true);
            break;
        case IMAGETYPE_GIF: $image = imagecreatefromgif($fileInfo["tmp_name"]); break;
        default: return ['status' => false, 'msg' => 'Format hanya JPG, PNG, GIF.'];
    }

    if (!$image) { return ['status' => false, 'msg' => 'Gagal memproses gambar.']; }

    $success = imagewebp($image, $targetFile, 80);
    imagedestroy($image); 

    if ($success) { return ['status' => true, 'filename' => $generateName]; } 
    else { return ['status' => false, 'msg' => 'Gagal menyimpan WebP.']; }
}
?>