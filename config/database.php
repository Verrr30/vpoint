<?php
require_once __DIR__ . '/../vendor/autoload.php';

try {
    $mongoClient = new MongoDB\Client("mongodb://localhost:27017");
    $database = $mongoClient->vpoint;
} catch (Exception $e) {
    die("Error connecting to MongoDB: " . $e->getMessage());
}
