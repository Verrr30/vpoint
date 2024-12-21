<?php
function uploadImage($file, $destination) {
    // Function untuk upload gambar
}

function formatPrice($price) {
    return number_format($price, 0, ',', '.');
}

function generateInvoiceNumber() {
    return 'INV-' . date('Ymd') . rand(1000, 9999);
}
