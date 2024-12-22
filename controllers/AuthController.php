<?php

class AuthController {
    private $database;

    public function __construct($database) {
        $this->database = $database;
    }

    public function register($data) {
        try {
            // Validasi data
            $errors = $this->validateRegistration($data);
            if (!empty($errors)) {
                return [
                    'success' => false,
                    'message' => implode(', ', $errors)
                ];
            }
    
            // Cek username dan email sudah ada atau belum
            $existingUser = $this->database->users->findOne([
                '$or' => [
                    ['username' => $data['username']],
                    ['email' => $data['email']]
                ]
            ]);
    
            if ($existingUser) {
                if ($existingUser->username === $data['username']) {
                    return [
                        'success' => false,
                        'message' => 'Username sudah digunakan'
                    ];
                }
                if ($existingUser->email === $data['email']) {
                    return [
                        'success' => false,
                        'message' => 'Email sudah terdaftar'
                    ];
                }
            }
    
            // Hash password
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
    
            // Siapkan data user sesuai schema
            $user = [
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => $hashedPassword,
                'role' => $data['role'],
                'created_at' => new MongoDB\BSON\UTCDateTime(),
                'status' => true // default active
            ];
    
            // Insert ke database
            $result = $this->database->users->insertOne($user);
    
            if ($result->getInsertedCount() > 0) {
                return [
                    'success' => true,
                    'message' => 'User berhasil ditambahkan'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Gagal menambahkan user'
                ];
            }
    
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }
    
    private function validateRegistration($data) {
        $errors = [];
    
        // Validasi username
        if (empty($data['username'])) {
            $errors[] = 'Username wajib diisi';
        } elseif (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $data['username'])) {
            $errors[] = 'Username hanya boleh mengandung huruf, angka, dan underscore (3-20 karakter)';
        }
    
        // Validasi email
        if (empty($data['email'])) {
            $errors[] = 'Email wajib diisi';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Format email tidak valid';
        }
    
        // Validasi password
        if (empty($data['password'])) {
            $errors[] = 'Password wajib diisi';
        } elseif (strlen($data['password']) < 6) {
            $errors[] = 'Password minimal 6 karakter';
        }
    
        // Validasi role
        if (empty($data['role']) || !in_array($data['role'], ['user', 'admin'])) {
            $errors[] = 'Role tidak valid';
        }
    
        return $errors;
    }
}