<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'paste_code');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Extract user information from the session
$user_id = $_SESSION['user_id'];

// Fetch business name for the logged-in user
$businessQuery = "SELECT businessName FROM apply WHERE user_id = '$user_id'";
$businessResult = $conn->query($businessQuery);
$businessData = $businessResult->fetch_assoc();
$businessName = $businessData['businessName'];

// Fetch products for the logged-in user
$productQuery = "SELECT * FROM products WHERE user_id = '$user_id'";
$productResult = $conn->query($productQuery);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Products</title>
    <style>
        /* Add your styling for flexboxes here */
        .products-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .product-card {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
            width: 200px;
        }

        .product-image {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>
<body>

<h1>My Products</h1>

<div class="products-container">
    <?php
    // Display products
    while ($productData = $productResult->fetch_assoc()) {
        echo '<div class="product-card">';
        echo '<img src="uploaded_products/' . $productData['product_img'] . '" class="product-image" alt="Product Image">';
        echo '<h3>' . $productData['product_name'] . '</h3>';
	echo '<p>Seller: ' . $businessName . '</p>';
        echo '<p>' . $productData['product_description'] . '</p>';
        echo '<p>Category: ' . $productData['product_category'] . '</p>';
        echo '<p>Quantity: ' . $productData['product_quantity'] . '</p>';
        echo '<p>Price: $' . $productData['product_price'] . '</p>';
        echo '</div>';
    }
    ?>
</div>

</body>
</html>

<?php
$conn->close();
?>