<?php

class AccountController {
    private $database;

    public function __construct($database) {
        $this->database = $database;
    }

    public function getAccount($id) {
        try {
            return $this->database->accounts->findOne([
                '_id' => new MongoDB\BSON\ObjectId($id)
            ]);
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
                'status' => $data['status'],
                'details' => [
                    'winrate' => (float)$data['winrate'],
                    'total_matches' => (int)$data['total_matches'],
                    'emblem_status' => [
                        'physical' => (int)$data['emblem_physical'],
                        'magic' => (int)$data['emblem_magic'],
                        'tank' => (int)$data['emblem_tank'],
                        'assassin' => (int)$data['emblem_assassin'],
                        'support' => (int)$data['emblem_support'],
                        'fighter' => (int)$data['emblem_fighter']
                    ]
                ]
            ];

            $result = $this->database->accounts->updateOne(
                ['_id' => new MongoDB\BSON\ObjectId($id)],
                ['$set' => $updateData]
            );

            if ($result->getModifiedCount() > 0) {
                return [
                    'success' => true,
                    'message' => 'Akun berhasil diperbarui'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Tidak ada perubahan yang disimpan'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    public function getAllAccounts() {
        try {
            return $this->database->accounts->find()->toArray();
        } catch (Exception $e) {
            return [];
        }
    }

    public function getAvailableAccounts() {
        try {
            return $this->database->accounts->find(['status' => 'available'])->toArray();
        } catch (Exception $e) {
            return [];
        }
    }
}