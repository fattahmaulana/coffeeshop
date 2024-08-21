<?php
require 'vendor/autoload.php';  // Pastikan autoloading DOMPDF dilakukan dengan Composer
use Dompdf\Dompdf;
use Dompdf\Options;
include 'db.php';  // Pastikan Anda menghubungkan ke database

// Menentukan rentang tanggal
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');

// Ambil data penjualan
$sql = "SELECT s.id, s.sale_date, s.total, si.product_id, si.quantity, si.price, p.name 
        FROM sales s
        JOIN sale_items si ON s.id = si.sale_id
        JOIN products p ON si.product_id = p.id
        WHERE s.sale_date BETWEEN :start_date AND :end_date";
$stmt = $pdo->prepare($sql);
$stmt->execute(['start_date' => $startDate, 'end_date' => $endDate]);
$sales = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Pisahkan data berdasarkan hari dan bulan
$dailySales = [];
$monthlySales = [];
$totalAmountDaily = [];
$totalAmountMonthly = [];
$totalQuantityDaily = [];
$totalQuantityMonthly = [];

foreach ($sales as $sale) {
    $day = date('Y-m-d', strtotime($sale['sale_date']));
    $month = date('Y-m', strtotime($sale['sale_date']));

    // Data Harian
    if (!isset($dailySales[$day])) {
        $dailySales[$day] = [];
        $totalAmountDaily[$day] = 0;
        $totalQuantityDaily[$day] = 0;
    }
    $dailySales[$day][] = $sale;
    $totalAmountDaily[$day] += $sale['quantity'] * $sale['price'];
    $totalQuantityDaily[$day] += $sale['quantity'];

    // Data Bulanan
    if (!isset($monthlySales[$month])) {
        $monthlySales[$month] = [];
        $totalAmountMonthly[$month] = 0;
        $totalQuantityMonthly[$month] = 0;
    }
    $monthlySales[$month][] = $sale;
    $totalAmountMonthly[$month] += $sale['quantity'] * $sale['price'];
    $totalQuantityMonthly[$month] += $sale['quantity'];
}

// Buat instance DOMPDF
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true);
$dompdf = new Dompdf($options);

// HTML untuk laporan
$html = '<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Sales Report</title>
    <style>
        body { font-family: Arial, sans-serif; }
        h1 { text-align: center; }
        h3 { margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        tfoot tr { font-weight: bold; }
    </style>
</head>
<body>
    <h1>Sales Report</h1>
    <p>From: ' . htmlspecialchars($startDate) . ' To: ' . htmlspecialchars($endDate) . '</p>';

$html .= '<h3>Daily Sales</h3>';

foreach ($dailySales as $day => $sales) {
    $html .= '<h4>Date: ' . htmlspecialchars($day) . '</h4>';
    $html .= '<table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>';

    foreach ($sales as $sale) {
        $html .= '<tr>
            <td>' . htmlspecialchars($sale['name']) . '</td>
            <td>' . htmlspecialchars($sale['quantity']) . '</td>
            <td>Rp. ' . number_format($sale['price'], 2) . '</td>
            <td>Rp. ' . number_format($sale['quantity'] * $sale['price'], 2) . '</td>
        </tr>';
    }

    $html .= '</tbody>
        <tfoot>
            <tr>
                <th colspan="3">Total</th>
                <th>Rp. ' . number_format($totalAmountDaily[$day], 2) . '</th>
            </tr>
        </tfoot>
    </table>';
}

$html .= '<h3>Monthly Sales</h3>';

foreach ($monthlySales as $month => $sales) {
    $html .= '<h4>Month: ' . htmlspecialchars($month) . '</h4>';
    $html .= '<table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>';

    foreach ($sales as $sale) {
        $html .= '<tr>
            <td>' . htmlspecialchars($sale['name']) . '</td>
            <td>' . htmlspecialchars($sale['quantity']) . '</td>
            <td>Rp. ' . number_format($sale['price'], 2) . '</td>
            <td>Rp. ' . number_format($sale['quantity'] * $sale['price'], 2) . '</td>
        </tr>';
    }

    $html .= '</tbody>
        <tfoot>
            <tr>
                <th colspan="3">Total</th>
                <th>Rp. ' . number_format($totalAmountMonthly[$month], 2) . '</th>
            </tr>
        </tfoot>
    </table>';
}

$html .= '</body>
</html>';

// Load HTML ke DOMPDF
$dompdf->loadHtml($html);

// (Opsional) Atur ukuran dan orientasi kertas
$dompdf->setPaper('A4', 'portrait');

// Render PDF
$dompdf->render();

// Output PDF ke browser
$dompdf->stream('Sales_Report.pdf', array('Attachment' => 1));
?>
