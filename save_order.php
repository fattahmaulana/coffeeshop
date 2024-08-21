<?php
include 'db.php';  // Pastikan Anda menghubungkan ke database

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $items = $_POST['items'];
    $total = $_POST['total'];
    
    // Mulai transaksi
    $pdo->beginTransaction();
    
    try {
        // Masukkan data penjualan
        $stmt = $pdo->prepare("INSERT INTO sales (sale_date, total) VALUES (CURDATE(), ?)");
        $stmt->execute([$total]);
        $saleId = $pdo->lastInsertId();
        
        // Masukkan data item penjualan
        $stmt = $pdo->prepare("INSERT INTO sale_items (sale_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        foreach ($items as $item) {
            $stmt->execute([$saleId, $item['product_id'], $item['quantity'], $item['price']]);
        }
        
        // Commit transaksi
        $pdo->commit();
        
        echo 'Order saved successfully!';
    } catch (Exception $e) {
        // Rollback transaksi jika ada kesalahan
        $pdo->rollBack();
        echo 'Error: ' . $e->getMessage();
    }
}
?>
