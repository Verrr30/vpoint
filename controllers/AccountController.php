<?php

class AccountController {
    private $db;
    private $accounts;

    public function __construct($database) {
        $this->db = $database;
        $this->accounts = $this->db->accounts;
    }

    public function addAccount($data) {
        try {
            // Validasi field yang wajib diisi
            $requiredFields = ['account_name', 'server_id', 'level', 'rank', 'price', 'winrate', 'total_matches'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    return [
                        'success' => false,
                        'message' => "Field {$field} wajib diisi."
                    ];
                }
            }

            // Menangani upload gambar utama
            $mainImagePath = '';
            if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
                $mainImagePath = $this->handleImageUpload($_FILES['main_image']);
                if (!$mainImagePath) {
                    return [
                        'success' => false,
                        'message' => 'Gagal mengunggah gambar utama.'
                    ];
                }
            }

            // Menangani gambar tambahan
            $additionalImages = [];
            if (isset($_FILES['additional_images'])) {
                $additionalImages = $this->handleMultipleImageUploads($_FILES['additional_images']);
            }

            // Menyiapkan data emblem
            $emblems = ['physical', 'magic', 'tank', 'assassin', 'support', 'fighter'];
            $emblemData = [];
            foreach ($emblems as $emblem) {
                $fieldName = "emblem_" . $emblem;
                $emblemData[$emblem] = isset($data[$fieldName]) ? (int)$data[$fieldName] : 0;
            }

            // Menyiapkan dokumen akun
            $account = [
                'account_name' => $data['account_name'],
                'server_id' => $data['server_id'],
                'level' => (int)$data['level'],
                'rank' => $data['rank'],
                'price' => (int)$data['price'],
                'winrate' => (float)$data['winrate'],
                'total_matches' => (int)$data['total_matches'],
                'emblems' => $emblemData,
                'main_image' => $mainImagePath,
                'additional_images' => $additionalImages,
                'created_at' => new MongoDB\BSON\UTCDateTime(),
                'updated_at' => new MongoDB\BSON\UTCDateTime(),
                'status' => 'tersedia'
            ];

            // Memasukkan ke database
            $result = $this->accounts->insertOne($account);

            if ($result->getInsertedCount() > 0) {
                return [
                    'success' => true,
                    'message' => 'Akun berhasil ditambahkan.'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Gagal menambahkan akun.'
                ];
            }

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    private function handleImageUpload($file) {
        $targetDir = "../../assets/images/accounts/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileName = uniqid() . '_' . basename($file['name']);
        $targetPath = $targetDir . $fileName;

        // Cek tipe file
        $imageFileType = strtolower(pathinfo($targetPath, PATHINFO_EXTENSION));
        if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            return false;
        }

        // Cek ukuran file (maksimal 10MB)
        if ($file['size'] > 10000000) {
            return false;
        }

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return $fileName;
        }

        return false;
    }

    private function handleMultipleImageUploads($files) {
        $uploadedFiles = [];
        $fileCount = count($files['name']);

        for ($i = 0; $i < $fileCount; $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                $file = [
                    'name' => $files['name'][$i],
                    'type' => $files['type'][$i],
                    'tmp_name' => $files['tmp_name'][$i],
                    'error' => $files['error'][$i],
                    'size' => $files['size'][$i]
                ];

                $fileName = $this->handleImageUpload($file);
                if ($fileName) {
                    $uploadedFiles[] = $fileName;
                }
            }
        }

        return $uploadedFiles;
    }

    public function getAllAccounts() {
        try {
            $accounts = $this->accounts->find([], [
                'sort' => ['created_at' => -1]
            ]);
            return iterator_to_array($accounts);
        } catch (Exception $e) {
            return [];
        }
    }

    public function getAccountById($id) {
        try {
            return $this->accounts->findOne(['_id' => new MongoDB\BSON\ObjectId($id)]);
        } catch (Exception $e) {
            return null;
        }
    }

    public function updateAccount($id, $data) {
        try {
            $updateData = [
                'account_name' => $data['account_name'],
                'server_id' => $data['server_id'],
                'level' => (int)$data['level'],
                'rank' => $data['rank'],
                'price' => (int)$data['price'],
                'winrate' => (float)$data['winrate'],
                'total_matches' => (int)$data['total_matches'],
                'updated_at' => new MongoDB\BSON\UTCDateTime()
            ];

            // Update emblem jika ada
            $emblems = ['physical', 'magic', 'tank', 'assassin', 'support', 'fighter'];
            $emblemData = [];
            foreach ($emblems as $emblem) {
                $fieldName = "emblem_" . $emblem;
                if (isset($data[$fieldName])) {
                    $emblemData[$emblem] = (int)$data[$fieldName];
                }
            }
            if (!empty($emblemData)) {
                $updateData['emblems'] = $emblemData;
            }

            // Menangani update gambar utama jika ada
            if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
                $mainImagePath = $this->handleImageUpload($_FILES['main_image']);
                if ($mainImagePath) {
                    $updateData['main_image'] = $mainImagePath;
                }
            }

            // Menangani gambar tambahan jika ada
            if (isset($_FILES['additional_images'])) {
                $additionalImages = $this->handleMultipleImageUploads($_FILES['additional_images']);
                if (!empty($additionalImages)) {
                    $updateData['additional_images'] = $additionalImages;
                }
            }

            $result = $this->accounts->updateOne(
                ['_id' => new MongoDB\BSON\ObjectId($id)],
                ['$set' => $updateData]
            );

            if ($result->getModifiedCount() > 0) {
                return [
                    'success' => true,
                    'message' => 'Akun berhasil diperbarui.'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Tidak ada perubahan pada akun.'
                ];
            }

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    public function deleteAccount($id) {
        try {
            $result = $this->accounts->deleteOne(['_id' => new MongoDB\BSON\ObjectId($id)]);

            if ($result->getDeletedCount() > 0) {
                return [
                    'success' => true,
                    'message' => 'Akun berhasil dihapus.'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Akun tidak ditemukan.'
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