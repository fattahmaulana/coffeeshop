<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save') {
    $products = json_decode($_POST['products'], true);

    try {
        $pdo->beginTransaction();

        foreach ($products as $product) {
            $product_id = $product['id'];
            $quantity = $product['quantity'];
            
            // Ambil harga produk
            $stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
            $stmt->execute([$product_id]);
            $product_price = $stmt->fetchColumn();

            // Hitung total
            $total = $product_price * $quantity;

            // Simpan data penjualan
            $stmt = $pdo->prepare("INSERT INTO sales (product_id, quantity, total, sale_date) VALUES (?, ?, ?, CURDATE())");
            $stmt->execute([$product_id, $quantity, $total]);
        }

        $pdo->commit();
        echo 'Data berhasil disimpan';
    } catch (Exception $e) {
        $pdo->rollBack();
        echo 'Gagal menyimpan data: ' . $e->getMessage();
    }
}
?>
