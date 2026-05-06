<?php

require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../middlewares/auth.php';
// require_once __DIR__ . '/../config/database.php';

class AuthController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function login($data) {
        $id_karyawan = $data['id_karyawan'] ?? '';
        $password = $data['password'] ?? '';

        // Cek user di database
        $stmt = $this->pdo->prepare("SELECT * FROM admin WHERE id_karyawan = :id_karyawan");
        $stmt->execute([':id_karyawan' => $id_karyawan]);
        $user = $stmt->fetch();

        if (!$user || $password !== 'bkkjtg123') {
            sendResponse(401, "id_karyawan atau password salah");
        }

        $payload = [
            "id" => $user['id'],
            "id_karyawan" => $user['id_karyawan'],
            "iat" => time(),
            "exp" => time() + (60 * 60) // 1 jam
        ];

        $token = generateJWT($payload);
        sendResponse(200, "Login berhasil", ["token" => $token]);
    }

    public function whoami($token) {
        $decoded = verifyJWT($token);
    
        if (!$decoded) {
            sendResponse(401, "Token tidak valid atau kadaluarsa");
        }
    
        // Ambil id_karyawan dari payload token
        $id_karyawan = $decoded['id_karyawan'] ?? null;
    
        if (!$id_karyawan) {
            sendResponse(400, "ID Karyawan tidak ditemukan dalam token");
        }
    
        // Query data user dari database
        $stmt = $this->pdo->prepare("SELECT id, id_karyawan, username, telepon FROM admin WHERE id_karyawan = :id_karyawan");
        $stmt->execute([':id_karyawan' => $id_karyawan]);
        $user = $stmt->fetch();
    
        if (!$user) {
            sendResponse(404, "User tidak ditemukan");
        }
    
        sendResponse(200, "Data user berhasil diambil", $user);
    }
}
