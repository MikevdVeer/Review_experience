<?php
require_once 'includes/db_connection.php';

if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit();
}

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $reviewer_name = trim($_POST['reviewer_name']);
    $rating = (int)$_POST['rating'];
    $review_text = trim($_POST['review_text']);
    $product_id = $_GET['id'];

    // Validate input
    if (!empty($reviewer_name) && !empty($review_text) && $rating >= 1 && $rating <= 5) {
        $stmt = $pdo->prepare("INSERT INTO reviews (product_id, reviewer_name, rating, review_text) VALUES (?, ?, ?, ?)");
        $stmt->execute([$product_id, $reviewer_name, $rating, $review_text]);
        
        // Redirect to refresh the page and show the new review
        header("Location: product_detail.php?id=" . $product_id . "&review_added=1");
        exit();
    }
}

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$_GET['id']]);
$product = $stmt->fetch();

if (!$product) {
    header("Location: products.php");
    exit();
}

// Fetch reviews for this product
$stmt = $pdo->prepare("SELECT * FROM reviews WHERE product_id = ? ORDER BY created_at DESC");
$stmt->execute([$_GET['id']]);
$reviews = $stmt->fetchAll();

// Calculate average rating
$avgRating = 0;
if (count($reviews) > 0) {
    $totalRating = array_sum(array_column($reviews, 'rating'));
    $avgRating = round($totalRating / count($reviews), 1);
}

$page_title = $product['name'] . ' - Product Details';
require_once 'includes/header.php';
?>

<div class="container mt-4">
    <?php if (isset($_GET['review_added'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        Thank you for your review!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="products.php">Products</a></li>
            <li class="breadcrumb-item active"><?php echo htmlspecialchars($product['name']); ?></li>
        </ol>
    </nav>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <?php if ($product['image_url']): ?>
                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" class="img-fluid rounded" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <?php else: ?>
                        <div class="text-center p-5 bg-light rounded">
                            <i class="fas fa-image fa-4x text-secondary"></i>
                            <p class="mt-2 text-muted">No image available</p>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <h1 class="h2 mb-3"><?php echo htmlspecialchars($product['name']); ?></h1>
                    <p class="lead"><?php echo htmlspecialchars($product['description']); ?></p>
                    <p class="h3 text-primary mb-4">$<?php echo number_format($product['price'], 2); ?></p>
                    
                    <div class="mb-4">
                        <h4>Average Rating: <?php echo $avgRating; ?> / 5</h4>
                        <div class="mb-2">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star <?php echo $i <= $avgRating ? 'text-warning' : 'text-secondary'; ?>"></i>
                            <?php endfor; ?>
                            <span class="ms-2">(<?php echo count($reviews); ?> reviews)</span>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="product_form.php?id=<?php echo $product['id']; ?>" class="btn btn-warning">
                            <i class="fas fa-edit me-1"></i> Edit Product
                        </a>
                        <a href="products.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Products
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Review Form -->
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="h5 mb-0">Write a Review</h3>
        </div>
        <div class="card-body">
            <form method="POST" class="needs-validation" novalidate>
                <div class="mb-3">
                    <label for="reviewer_name" class="form-label">Your Name</label>
                    <input type="text" class="form-control" id="reviewer_name" name="reviewer_name" required>
                    <div class="invalid-feedback">Please enter your name.</div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Rating</label>
                    <div class="rating">
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="rating" id="rating<?php echo $i; ?>" value="<?php echo $i; ?>" <?php echo $i === 5 ? 'checked' : ''; ?> required>
                            <label class="form-check-label" for="rating<?php echo $i; ?>">
                                <?php echo $i; ?> <i class="fas fa-star text-warning"></i>
                            </label>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="review_text" class="form-label">Your Review</label>
                    <textarea class="form-control" id="review_text" name="review_text" rows="4" required></textarea>
                    <div class="invalid-feedback">Please write your review.</div>
                </div>

                <button type="submit" name="submit_review" class="btn btn-primary">
                    <i class="fas fa-paper-plane me-1"></i> Submit Review
                </button>
            </form>
        </div>
    </div>

    <!-- Reviews List -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0">Reviews</h2>
            <span class="badge bg-primary"><?php echo count($reviews); ?> Reviews</span>
        </div>
        <div class="card-body">
            <?php if (count($reviews) > 0): ?>
                <?php foreach ($reviews as $review): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-1"><?php echo htmlspecialchars($review['reviewer_name']); ?></h5>
                                <div>
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star <?php echo $i <= $review['rating'] ? 'text-warning' : 'text-secondary'; ?>"></i>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <p class="card-text mt-2"><?php echo htmlspecialchars($review['review_text']); ?></p>
                            <small class="text-muted">Posted on <?php echo date('F j, Y', strtotime($review['created_at'])); ?></small>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-4">
                    <i class="fas fa-comments fa-3x text-secondary mb-3"></i>
                    <p class="text-muted mb-0">No reviews yet. Be the first to review this product!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Form validation
(function () {
    'use strict'
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    })
})()
</script>

<?php require_once 'includes/footer.php'; ?> 