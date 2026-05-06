<?php

require_once __DIR__ . '/helpers/response.php';
require_once __DIR__ . '/config/database.php';

try {
    // Tabel aset yang akan dilelang
    $pdo->exec("CREATE TABLE IF NOT EXISTS assets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nama VARCHAR(255) NOT NULL,
        jenis ENUM('tanah', 'tanah dan bangunan', 'kendaraan') NOT NULL,
        deskripsi TEXT,
        harga_awal DECIMAL(15,2) NOT NULL,
        status ENUM('belum_dilelang', 'dilelang', 'terjual') DEFAULT 'belum_dilelang',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Tabel histori lelang
    $pdo->exec("CREATE TABLE IF NOT EXISTS lelang_histori (
        id INT AUTO_INCREMENT PRIMARY KEY,
        asset_id INT NOT NULL,
        tanggal_lelang DATE NOT NULL,
        harga_penawaran DECIMAL(15,2) NOT NULL,
        FOREIGN KEY (asset_id) REFERENCES assets(id) ON DELETE CASCADE
    )");

    sendResponse(200, "Tabel berhasil dibuat!");
} catch (PDOException $e) {
    sendResponse(500, "Gagal membuat tabel: " . $e->getMessage());
}
?>
