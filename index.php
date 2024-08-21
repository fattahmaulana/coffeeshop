<?php include 'db.php'; ?>
<!doctype html>
<html lang="en" class="blue-theme">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, viewport-fit=cover, user-scalable=no">
    <meta name="description" content="">
    <title>GC · Coffeeshop</title>

    <!-- Material design icons CSS -->
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

    <!-- Sidebar and Header -->
    <div class="sidebar">
        <div class="text-center">
            <div class="figure-menu shadow">
                <figure><img src="img/gc.jpeg" alt=""></figure>
            </div>
            <h5 class="mb-1">GC CoffeeShop</h5>
            <p class="text-mute small">aplikasi pencatatan keuangan</p>
        </div>
        <br>
        <div class="row mx-0">
            <div class="col">
                <div class="card mb-3 border-0 shadow-sm bg-template-light">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <p class="text-secondary small mb-0">penghasilan harian</p>
                                <?php
                                // Mengambil total penjualan harian
                                $stmt = $pdo->prepare("SELECT SUM(total) AS daily_income FROM sales WHERE sale_date = CURDATE()");
                                $stmt->execute();
                                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                $daily_income = $result['daily_income'] ?: 0;
                                ?>
                                <h6 class="text-dark my-0">Rp.<?php echo number_format($daily_income, 2); ?></h6>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-default button-rounded-36 shadow"><i class="material-icons">add</i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <h5 class="subtitle text-uppercase"><span>Menu</span></h5>
                <div class="list-group main-menu">
                    <a href="index.php" class="list-group-item list-group-item-action active">All Products</a>
                    <a href="report.php" class="list-group-item list-group-item-action">Laporan</a>
                    <a href="admin.php" class="list-group-item list-group-item-action">Edit Menu</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="wrapper">
        <div class="header">
            <div class="row no-gutters">
                <div class="col-auto">
                    <button class="btn btn-link text-dark menu-btn"><img src="img/menu.png" alt=""><span class="new-notification"></span></button>
                </div>
                <div class="col text-center mt-3"><h4>GC CoffeeShop</h4></div>
                <div class="col-auto">
                    <a href="profile.html" class="btn btn-link text-dark"><i class="material-icons">account_circle</i></a>
                </div>
            </div>
        </div>

        <div class="container">
        <input type="text" id="search-input" class="form-control form-control-lg search my-3" placeholder="Search">

            <h6 class="subtitle">Categories</h6>
            <div class="kate">
                <!-- Swiper -->
                <div class="swiper-container small-slide">
                    <div class="swiper-wrapper">
                        <?php
                        // Mengambil kategori
                        $stmt = $pdo->query("SELECT * FROM categories");
                        while ($category = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo '<div class="swiper-slide">';
                            echo '<div class="card shadow-sm border-0 category-btn" data-category-id="' . htmlspecialchars($category['id']) . '">';
                            echo '<div class="card-body">';
                            echo '<div class="row no-gutters h-100">';
                            echo '<img src="img/coffee1.png" alt="" class="small-slide-right">';
                            echo '<div class="col-8">';
                            echo '<span class="text-dark mb-1 mt-2 h6 d-block">' . htmlspecialchars($category['name']) . '</span>';
                            echo '<p class="text-secondary small">... </p>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
            <h6 class="subtitle">Products <a href="#" id="view-all" class="float-right small">View All</a></h6>

            <div class="row">
                <div class="col-12 px-0">
                    <ul class="list-group list-group-flush mb-4" id="product-list">
                        <?php
                        // Mengambil produk
                        $stmt = $pdo->query("SELECT * FROM products");
                        while ($product = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo '<li class="list-group-item" data-category-id="' . htmlspecialchars($product['category_id']) . '" data-product-name="' . htmlspecialchars($product['name']) . '">';
                            echo '<div class="row">';
                            echo '<div class="col-3 px-0 ml-2">';
                            // Menampilkan gambar produk
                            echo '<img src="img/products/' . htmlspecialchars($product['image']) . '" alt="' . htmlspecialchars($product['name']) . '" class="img-fluid" width="50px" height="50px">';
                            echo '</div>';
                            echo '<div class="col px-0">';
                            echo '<h5 class="text-dark font-weight-normal mt-3">' . htmlspecialchars($product['name']) . '</h5>';
                            echo '<p class="text-secondary small text-mute mb-0">Rp.' . number_format($product['price'], 2) . '</p>';
                            echo '</div>';
                            echo '<div class="col-auto align-self-center">';
                            echo '<div class="input-group input-group-sm">';
                            echo '<div class="input-group-prepend">';
                            echo '<button class="btn btn-light-grey px-1 quantity-btn" type="button" data-action="decrease" data-product-id="' . htmlspecialchars($product['id']) . '" data-price="' . htmlspecialchars($product['price']) . '"><i class="material-icons">remove</i></button>';
                            echo '</div>';
                            echo '<input type="text" class="form-control w-35 quantity-input" id="quantity-' . htmlspecialchars($product['id']) . '" name="quantity[' . htmlspecialchars($product['id']) . ']" value="0">';
                            echo '<div class="input-group-append">';
                            echo '<button class="btn btn-light-grey px-1 quantity-btn" type="button" data-action="increase" data-product-id="' . htmlspecialchars($product['id']) . '" data-price="' . htmlspecialchars($product['price']) . '"><i class="material-icons">add</i></button>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                            echo '</li>';
                        }
                        
                        ?>
                    </ul>
                </div>
            </div>
        </div>

        <div class="footer">
            <div class="no-gutters">
                <div class="col-auto mx-auto">
                    <div class="row no-gutters justify-content-center">
                        <div class="col-auto mt-2 mr-4">
                            <h7>Total: </h7>
                            <p>Rp. <span id="total-price">0</span></p>
                        </div>
                        
                        <div class="col-auto">
                            <button class="btn btn-default shadow centerbutton" id="save-order">
                                <i class="material-icons">save</i>
                            </button>
                        </div>
                        <div class="col-auto">
                            <a href="report.php" class="btn btn-link-default">
                                <i class="material-icons">insert_chart_outline</i>
                            </a>
                        </div>
                        <div class="col-auto">
                            <a href="admin.php" class="btn btn-link-default">
                                <i class="material-icons">edit menu</i>
                            </a>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jquery, popper and bootstrap js -->
    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="vendor/bootstrap-4.4.1/js/bootstrap.min.js"></script>

    <!-- swiper js -->
    <script src="vendor/swiper/js/swiper.min.js"></script>

    <!-- template custom js -->
    <script src="js/main.js"></script>
    <script>
        const categoryButtons = document.querySelectorAll('.category-btn');
            const productList = document.getElementById('product-list');

            categoryButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const categoryId = this.getAttribute('data-category-id');

                    const items = productList.querySelectorAll('.list-group-item');
                    items.forEach(item => {
                        if (item.getAttribute('data-category-id') === categoryId) {
                            item.style.display = 'block';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            });

                    document.getElementById('search-input').addEventListener('input', function() {
                let filter = this.value.toLowerCase(); // Ambil teks pencarian dan ubah ke huruf kecil
                let products = document.querySelectorAll('#product-list .list-group-item'); // Ambil semua item produk

                products.forEach(function(product) {
                    let productName = product.getAttribute('data-product-name').toLowerCase(); // Ambil nama produk dan ubah ke huruf kecil
                    if (productName.includes(filter)) { // Periksa apakah nama produk mengandung teks pencarian
                        product.style.display = ''; // Tampilkan produk jika cocok
                    } else {
                        product.style.display = 'none'; // Sembunyikan produk jika tidak cocok
                    }
                });
            });

            document.getElementById('view-all').addEventListener('click', function(event) {
            event.preventDefault(); // Mencegah tindakan default link

            let products = document.querySelectorAll('#product-list .list-group-item'); // Ambil semua item produk

            products.forEach(function(product) {
                product.style.display = ''; // Tampilkan semua produk
            });

            // Hapus filter pencarian dan kategori
            document.getElementById('search-input').value = '';
        });
    </script>
</body>
</html>
