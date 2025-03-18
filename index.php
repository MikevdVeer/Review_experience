<?php
require_once 'includes/config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

// Get some statistics for the dashboard
$totalProducts = $conn->query("SELECT COUNT(*) FROM products")->fetch_row()[0];
$totalReviews = $conn->query("SELECT COUNT(*) FROM reviews")->fetch_row()[0];
$avgRating = $conn->query("SELECT AVG(rating) FROM reviews")->fetch_row()[0];
$latestProducts = $conn->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 3")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Review System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Dashboard</h1>
            <div class="user-menu">
                Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>
                <?php if (isAdmin()): ?>
                    <span class="admin-badge">Admin</span>
                <?php endif; ?>
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>

        <?php if (isAdmin()): ?>
        <div class="admin-actions">
            <a href="product_form.php" class="btn btn-primary">Add New Product</a>
            <a href="manage_users.php" class="btn btn-secondary">Manage Users</a>
        </div>
        <?php endif; ?>

        <!-- Statistics Cards -->
        <div class="stats-container">
            <div class="stat-card">
                <h3>Total Products</h3>
                <p><?php echo $totalProducts; ?></p>
            </div>
            <div class="stat-card">
                <h3>Total Reviews</h3>
                <p><?php echo $totalReviews; ?></p>
            </div>
            <div class="stat-card">
                <h3>Average Rating</h3>
                <p><?php echo number_format($avgRating, 1); ?> / 5</p>
            </div>
        </div>

        <!-- Latest Products -->
        <div class="latest-products">
            <h2>Latest Products</h2>
            <div class="products-grid">
                <?php foreach ($latestProducts as $product): ?>
                <div class="product-card">
                    <?php if ($product['image_url']): ?>
                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <?php endif; ?>
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p class="price">$<?php echo number_format($product['price'], 2); ?></p>
                    <p class="description"><?php echo htmlspecialchars($product['description']); ?></p>
                    <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="btn btn-info">View Details</a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html> 