<?php
include 'db.php';

// Menangani CRUD Kategori
if (isset($_POST['action']) && $_POST['action'] === 'manage_category') {
    $action = $_POST['manage_action'];
    $id = $_POST['id'] ?? null;
    $name = $_POST['name'] ?? null;

    if ($action === 'add') {
        $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (:name)");
        $stmt->execute([':name' => $name]);
    } elseif ($action === 'update') {
        $stmt = $pdo->prepare("UPDATE categories SET name = :name WHERE id = :id");
        $stmt->execute([':name' => $name, ':id' => $id]);
    } elseif ($action === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }
}

// Menangani CRUD Produk
if (isset($_POST['action']) && $_POST['action'] === 'manage_product') {
    $action = $_POST['manage_action'];
    $id = $_POST['id'] ?? null;
    $name = $_POST['name'] ?? null;
    $price = $_POST['price'] ?? null;
    $category_id = $_POST['category_id'] ?? null;
    $image = $_FILES['image']['name'] ?? null;

    if ($action === 'add') {
        $uploadFileDir = './img/products/';
        $dest_path = $uploadFileDir . $image;
        move_uploaded_file($_FILES['image']['tmp_name'], $dest_path);

        $stmt = $pdo->prepare("INSERT INTO products (name, price, image, category_id) VALUES (:name, :price, :image, :category_id)");
        $stmt->execute([
            ':name' => $name,
            ':price' => $price,
            ':image' => $image,
            ':category_id' => $category_id
        ]);
    } elseif ($action === 'update') {
        if ($image) {
            $uploadFileDir = './img/products/';
            $dest_path = $uploadFileDir . $image;
            move_uploaded_file($_FILES['image']['tmp_name'], $dest_path);

            $stmt = $pdo->prepare("UPDATE products SET name = :name, price = :price, image = :image, category_id = :category_id WHERE id = :id");
            $stmt->execute([
                ':name' => $name,
                ':price' => $price,
                ':image' => $image,
                ':category_id' => $category_id,
                ':id' => $id
            ]);
        } else {
            $stmt = $pdo->prepare("UPDATE products SET name = :name, price = :price, category_id = :category_id WHERE id = :id");
            $stmt->execute([
                ':name' => $name,
                ':price' => $price,
                ':category_id' => $category_id,
                ':id' => $id
            ]);
        }
    } elseif ($action === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }
}

// Ambil data kategori
$categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);

// Ambil data produk
$products = $pdo->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, viewport-fit=cover, user-scalable=no">
    <meta name="description" content="">
    <title>ADMIN · Coffeeshop</title>

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
    <div class="container">
        <h1>Admin Panel</h1>
        
        <!-- Categories Section -->
        <h2>Manage Categories</h2>
        <form action="admin.php" method="post">
            <input type="hidden" name="action" value="manage_category">
            <input type="hidden" name="manage_action" value="add">
            <div class="form-group">
                <label for="category_name">Category Name:</label>
                <input type="text" id="category_name" name="name" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Category</button>
        </form>
        
        <h3>Existing Categories</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $category): ?>
                <tr>
                    <td><?php echo htmlspecialchars($category['id']); ?></td>
                    <td><?php echo htmlspecialchars($category['name']); ?></td>
                    <td>
                        <form action="admin.php" method="post" class="d-inline-block">
                            <input type="hidden" name="action" value="manage_category">
                            <input type="hidden" name="manage_action" value="update">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($category['id']); ?>">
                            <input type="text" name="name" value="<?php echo htmlspecialchars($category['name']); ?>" class="form-control d-inline-block w-75" required>
                            <button type="submit" class="btn btn-warning">Update</button>
                        </form>
                        <form action="admin.php" method="post" class="d-inline-block">
                            <input type="hidden" name="action" value="manage_category">
                            <input type="hidden" name="manage_action" value="delete">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($category['id']); ?>">
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <!-- Products Section -->
        <h2>Manage Products</h2>
        <form action="admin.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="action" value="manage_product">
            <input type="hidden" name="manage_action" value="add">
            <div class="form-group">
                <label for="product_name">Product Name:</label>
                <input type="text" id="product_name" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="price">Price:</label>
                <input type="number" id="price" name="price" class="form-control" step="0.01" required>
            </div>
            <div class="form-group">
                <label for="category">Category:</label>
                <select id="category" name="category_id" class="form-control" required>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo htmlspecialchars($category['id']); ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="image">Image:</label>
                <input type="file" id="image" name="image" class="form-control-file" accept="image/*">
            </div>
            <button type="submit" class="btn btn-primary">Add Product</button>
        </form>
        
        <h3>Existing Products</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Image</th>
                    <th>Category</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo htmlspecialchars($product['id']); ?></td>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td>Rp. <?php echo number_format($product['price'], 2); ?></td>
                    <td><img src="img/products/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" width="100"></td>
                    <td>
                        <?php
                        // Ambil nama kategori
                        $stmt = $pdo->prepare("SELECT name FROM categories WHERE id = :id");
                        $stmt->execute([':id' => $product['category_id']]);
                        $category = $stmt->fetch(PDO::FETCH_ASSOC);
                        echo htmlspecialchars($category['name']);
                        ?>
                    </td>
                    <td>
                        <form action="admin.php" method="post" class="d-inline-block" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="manage_product">
                            <input type="hidden" name="manage_action" value="update">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($product['id']); ?>">
                            <div class="form-group">
                                <label for="product_name">Product Name:</label>
                                <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="price">Price:</label>
                                <input type="number" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" class="form-control" step="0.01" required>
                            </div>
                            <div class="form-group">
                                <label for="category">Category:</label>
                                <select name="category_id" class="form-control" required>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo htmlspecialchars($category['id']); ?>" <?php echo $category['id'] == $product['category_id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="image">Image:</label>
                                <input type="file" name="image" class="form-control-file" accept="image/*">
                            </div>
                            <button type="submit" class="btn btn-warning">Update</button>
                        </form>
                        <form action="admin.php" method="post" class="d-inline-block">
                            <input type="hidden" name="action" value="manage_product">
                            <input type="hidden" name="manage_action" value="delete">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($product['id']); ?>">
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
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
</body>
</html>
