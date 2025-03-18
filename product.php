<?php
require_once 'includes/config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$product_id = $_GET['id'];

// Fetch product details
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header('Location: index.php');
    exit();
}

// Handle new review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT);
    $review_text = filter_input(INPUT_POST, 'review_text', FILTER_SANITIZE_STRING);
    $reviewer_name = filter_input(INPUT_POST, 'reviewer_name', FILTER_SANITIZE_STRING);

    if ($rating && $review_text && $reviewer_name) {
        $stmt = $pdo->prepare("INSERT INTO reviews (product_id, rating, review_text, reviewer_name) VALUES (?, ?, ?, ?)");
        $stmt->execute([$product_id, $rating, $review_text, $reviewer_name]);
    }
}

// Fetch reviews
$stmt = $pdo->prepare("SELECT * FROM reviews WHERE product_id = ? ORDER BY created_at DESC");
$stmt->execute([$product_id]);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - Product Reviews</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1><?php echo htmlspecialchars($product['name']); ?></h1>
        <a href="index.php" class="back-link">← Back to Products</a>
    </header>

    <main class="product-detail">
        <div class="product-info">
            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
            <div class="product-details">
                <p class="price">$<?php echo number_format($product['price'], 2); ?></p>
                <p class="description"><?php echo htmlspecialchars($product['description']); ?></p>
            </div>
        </div>

        <div class="reviews-section">
            <h2>Reviews</h2>
            
            <form class="review-form" method="POST">
                <h3>Write a Review</h3>
                <div class="form-group">
                    <label for="reviewer_name">Your Name:</label>
                    <input type="text" id="reviewer_name" name="reviewer_name" required>
                </div>
                <div class="form-group">
                    <label for="rating">Rating:</label>
                    <select id="rating" name="rating" required>
                        <option value="5">5 Stars</option>
                        <option value="4">4 Stars</option>
                        <option value="3">3 Stars</option>
                        <option value="2">2 Stars</option>
                        <option value="1">1 Star</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="review_text">Your Review:</label>
                    <textarea id="review_text" name="review_text" required></textarea>
                </div>
                <button type="submit">Submit Review</button>
            </form>

            <div class="reviews-list">
                <?php if (empty($reviews)): ?>
                    <p>No reviews yet. Be the first to review this product!</p>
                <?php else: ?>
                    <?php foreach ($reviews as $review): ?>
                        <div class="review">
                            <div class="review-header">
                                <span class="reviewer-name"><?php echo htmlspecialchars($review['reviewer_name']); ?></span>
                                <span class="rating">
                                    <?php for ($i = 0; $i < $review['rating']; $i++) echo '★'; ?>
                                    <?php for ($i = $review['rating']; $i < 5; $i++) echo '☆'; ?>
                                </span>
                                <span class="review-date"><?php echo date('M d, Y', strtotime($review['created_at'])); ?></span>
                            </div>
                            <p class="review-text"><?php echo htmlspecialchars($review['review_text']); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Product Reviews. All rights reserved.</p>
    </footer>
</body>
</html> 