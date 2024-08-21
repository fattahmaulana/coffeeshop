<?php
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
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sales Report</title>
    <link rel="stylesheet" href="vendor/bootstrap-4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="vendor/materializeicon/material-icons.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="vendor/materializeicon/material-icons.css">

    <!-- Roboto fonts CSS -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet">

    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap-4.4.1/css/bootstrap.min.css" rel="stylesheet">

    <!-- Swiper CSS -->
    <link href="vendor/swiper/css/swiper.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1 class="my-4">Sales Report</h1>

        <!-- Form Input Tanggal -->
        <form method="GET" action="">
            <div class="form-group">
                <label for="start_date">Start Date:</label>
                <input type="text" id="start_date" name="start_date" class="form-control" value="<?php echo htmlspecialchars($startDate); ?>">
            </div>
            <div class="form-group">
                <label for="end_date">End Date:</label>
                <input type="text" id="end_date" name="end_date" class="form-control" value="<?php echo htmlspecialchars($endDate); ?>">
            </div>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="generate_pdf.php?start_date=<?php echo urlencode($startDate); ?>&end_date=<?php echo urlencode($endDate); ?>" class="btn btn-success my-3">Download PDF Report</a>
        </form>

        <h4 class="my-4">From: <?php echo htmlspecialchars($startDate); ?> To: <?php echo htmlspecialchars($endDate); ?></h4>

        <!-- Tab Navigation -->
        <ul class="nav nav-tabs" id="reportTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="daily-tab" data-toggle="tab" href="#daily" role="tab" aria-controls="daily" aria-selected="true">Daily Sales</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="monthly-tab" data-toggle="tab" href="#monthly" role="tab" aria-controls="monthly" aria-selected="false">Monthly Sales</a>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="reportTabContent">
            <!-- Daily Sales Tab -->
            <div class="tab-pane fade show active" id="daily" role="tabpanel" aria-labelledby="daily-tab">
                <?php foreach ($dailySales as $day => $sales): ?>
                <h4 class="mt-4"><?php echo htmlspecialchars($day); ?></h4>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sales as $sale): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($sale['sale_date']); ?></td>
                            <td><?php echo htmlspecialchars($sale['name']); ?></td>
                            <td><?php echo htmlspecialchars($sale['quantity']); ?></td>
                            <td>Rp. <?php echo number_format($sale['price'], 2); ?></td>
                            <td>Rp. <?php echo number_format($sale['quantity'] * $sale['price'], 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4">Total</th>
                            <th>Rp. <?php echo number_format($totalAmountDaily[$day], 2); ?></th>
                        </tr>
                    </tfoot>
                </table>
                <?php endforeach; ?>

                <!-- Tabel Statistik Harian -->
                <h3>Daily Statistics</h3>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Total Sales (Rp)</th>
                            <th>Total Products Sold</th>
                            <th>Average Sales per Day (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($totalAmountDaily as $day => $totalAmount): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($day); ?></td>
                            <td>Rp. <?php echo number_format($totalAmount, 2); ?></td>
                            <td><?php echo htmlspecialchars($totalQuantityDaily[$day]); ?></td>
                            <td>Rp. <?php echo number_format($totalAmount / count($dailySales[$day]), 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Monthly Sales Tab -->
            <div class="tab-pane fade" id="monthly" role="tabpanel" aria-labelledby="monthly-tab">
                <?php foreach ($monthlySales as $month => $sales): ?>
                <h4 class="mt-4"><?php echo htmlspecialchars($month); ?></h4>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sales as $sale): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($sale['sale_date']); ?></td>
                            <td><?php echo htmlspecialchars($sale['name']); ?></td>
                            <td><?php echo htmlspecialchars($sale['quantity']); ?></td>
                            <td>Rp. <?php echo number_format($sale['price'], 2); ?></td>
                            <td>Rp. <?php echo number_format($sale['quantity'] * $sale['price'], 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4">Total</th>
                            <th>Rp. <?php echo number_format($totalAmountMonthly[$month], 2); ?></th>
                        </tr>
                    </tfoot>
                </table>
                <?php endforeach; ?>

                <!-- Tabel Statistik Bulanan -->
                <h3>Monthly Statistics</h3>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Total Sales (Rp)</th>
                            <th>Total Products Sold</th>
                            <th>Average Sales per Month (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($totalAmountMonthly as $month => $totalAmount): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($month); ?></td>
                            <td>Rp. <?php echo number_format($totalAmount, 2); ?></td>
                            <td><?php echo htmlspecialchars($totalQuantityMonthly[$month]); ?></td>
                            <td>Rp. <?php echo number_format($totalAmount / count($monthlySales[$month]), 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="footer">
                <div class="no-gutters">
                    <div class="col-auto mx-auto">
                        <div class="row no-gutters justify-content-center">
                            
                            <div class="col-auto">
                                <a href="index.php" class="btn btn-link-default">
                                    <i class="material-icons">home</i>
                                </a>
                            </div>
                            
                            
                        </div>
                    </div>
                </div>
            </div>

    <!-- jQuery dan jQuery UI Datepicker -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script>
        $(function() {
            $("#start_date").datepicker({ dateFormat: 'yy-mm-dd' });
            $("#end_date").datepicker({ dateFormat: 'yy-mm-dd' });
        });
    </script>
    <script src="vendor/bootstrap-4.4.1/js/bootstrap.min.js"></script>
</body>
</html>
