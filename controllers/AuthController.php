<?php

class AuthController {
    private $db;
    private $users;

    public function __construct($database) {
        $this->db = $database;
        $this->users = $this->db->users;
    }

    public function login($username, $password) {
        try {
            $user = $this->users->findOne(['username' => $username]);
            
            if ($user && password_verify($password, $user->password)) {
                $_SESSION['user_id'] = (string)$user->_id;
                $_SESSION['username'] = $user->username;
                $_SESSION['role'] = $user->role;
                
                return [
                    'success' => true,
                    'message' => 'Login berhasil'
                ];
            }
            
            return [
                'success' => false,
                'message' => 'Username atau password salah'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    public function register($data) {
        try {
            // Validasi input
            if (empty($data['username']) || empty($data['password']) || empty($data['email'])) {
                return [
                    'success' => false,
                    'message' => 'Semua field harus diisi'
                ];
            }

            // Cek username sudah ada atau belum
            $existingUser = $this->users->findOne(['username' => $data['username']]);
            if ($existingUser) {
                return [
                    'success' => false,
                    'message' => 'Username sudah digunakan'
                ];
            }

            // Cek email sudah ada atau belum
            $existingEmail = $this->users->findOne(['email' => $data['email']]);
            if ($existingEmail) {
                return [
                    'success' => false,
                    'message' => 'Email sudah digunakan'
                ];
            }

            // Hash password
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

            // Siapkan data user
            $user = [
                'username' => $data['username'],
                'password' => $hashedPassword,
                'email' => $data['email'],
                'role' => 'user',
                'created_at' => new MongoDB\BSON\UTCDateTime(),
                'updated_at' => new MongoDB\BSON\UTCDateTime()
            ];

            // Insert ke database
            $result = $this->users->insertOne($user);

            if ($result->getInsertedCount() > 0) {
                return [
                    'success' => true,
                    'message' => 'Registrasi berhasil'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Gagal melakukan registrasi'
                ];
            }

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    public function logout() {
        session_destroy();
        return [
            'success' => true,
            'message' => 'Logout berhasil'
        ];
    }

    public function getCurrentUser() {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }

        try {
            return $this->users->findOne([
                '_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])
            ]);
        } catch (Exception $e) {
            return null;
        }
    }

    public function updateUser($userId, $data) {
        try {
            $updateData = [
                'updated_at' => new MongoDB\BSON\UTCDateTime()
            ];

            if (!empty($data['email'])) {
                // Cek email sudah digunakan user lain atau belum
                $existingEmail = $this->users->findOne([
                    'email' => $data['email'],
                    '_id' => ['$ne' => new MongoDB\BSON\ObjectId($userId)]
                ]);
                
                if ($existingEmail) {
                    return [
                        'success' => false,
                        'message' => 'Email sudah digunakan'
                    ];
                }
                
                $updateData['email'] = $data['email'];
            }

            if (!empty($data['password'])) {
                $updateData['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }

            $result = $this->users->updateOne(
                ['_id' => new MongoDB\BSON\ObjectId($userId)],
                ['$set' => $updateData]
            );

            if ($result->getModifiedCount() > 0) {
                return [
                    'success' => true,
                    'message' => 'Data user berhasil diperbarui'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Tidak ada perubahan data'
                ];
            }

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    public function getAllUsers() {
        try {
            $users = $this->users->find([], [
                'sort' => ['created_at' => -1]
            ]);
            return iterator_to_array($users);
        } catch (Exception $e) {
            return [];
        }
    }

    public function deleteUser($userId) {
        try {
            $result = $this->users->deleteOne([
                '_id' => new MongoDB\BSON\ObjectId($userId)
            ]);

            if ($result->getDeletedCount() > 0) {
                return [
                    'success' => true,
                    'message' => 'User berhasil dihapus'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }
}