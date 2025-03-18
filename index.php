<?php
require_once 'includes/config.php';

// Fetch all products
$stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Reviews</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>Product Reviews</h1>
    </header>
    
    <main class="product-grid">
        <?php foreach ($products as $product): ?>
            <div class="product-card">
                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                <h2><?php echo htmlspecialchars($product['name']); ?></h2>
                <p class="price">$<?php echo number_format($product['price'], 2); ?></p>
                <p class="description"><?php echo htmlspecialchars(substr($product['description'], 0, 100)) . '...'; ?></p>
                <a href="product.php?id=<?php echo $product['id']; ?>" class="view-details">View Details</a>
            </div>
        <?php endforeach; ?>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Product Reviews. All rights reserved.</p>
    </footer>
</body>
</html> 