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

// Check if the user has already applied for a business account
$existingApplicationQuery = "SELECT id FROM apply WHERE user_id = '$user_id'";
$existingApplicationResult = $conn->query($existingApplicationQuery);

if ($existingApplicationResult->num_rows > 0) {
    // User has already applied, provide information or request deletion
    echo "You have already applied for a business account. If you want to apply again, please request account deletion.";
    // Add a link or button for account deletion request
} else {
    // User has not applied, proceed with the application process

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Other form data
        $businessName = isset($_POST['businessName']) ? $_POST['businessName'] : '';
        $businessDescription = isset($_POST['businessDescription']) ? $_POST['businessDescription'] : '';
        $completeAddress = isset($_POST['completeAddress']) ? $_POST['completeAddress'] : '';
        $businessNumber = isset($_POST['businessNumber']) ? $_POST['businessNumber'] : '';
        $businessEmail = isset($_POST['businessEmail']) ? $_POST['businessEmail'] : '';
        $businessCategory = isset($_POST['businessCategory']) ? $_POST['businessCategory'] : '';

        // Handle file upload for validId
        $validIdFileName = ''; // Define a variable to store the filename
        if (isset($_FILES['validId'])) {
            $validIdFileName = $_FILES['validId']['name'];
            $validIdTmpName = $_FILES['validId']['tmp_name'];
            $validIdUploadPath = 'uploaded_apply_files/' . $validIdFileName; // Set the upload path

            // Move the uploaded file to the destination
            move_uploaded_file($validIdTmpName, $validIdUploadPath);
        }

        // Insert data into the 'apply' table with is_approved set to 0 (not approved)
        $sql = "INSERT INTO apply (user_id, businessName, businessDescription, completeAddress, businessNumber, businessEmail, businessCategory, validId, is_approved)
                VALUES ('$user_id', '$businessName', '$businessDescription', '$completeAddress', '$businessNumber', '$businessEmail', '$businessCategory', '$validIdFileName', 0)";

        if ($conn->query($sql) === TRUE) {
            echo "Application submitted successfully. Waiting for approval.";
        } else {
            echo "Error submitting application: " . $conn->error;
        }
    }
}

// Check if the user has already applied for a business account
$existingApplicationQuery = "SELECT * FROM apply WHERE user_id = '$user_id'";
$existingApplicationResult = $conn->query($existingApplicationQuery);

if ($existingApplicationResult->num_rows > 0) {
    $applicationData = $existingApplicationResult->fetch_assoc();

    if ($applicationData['is_approved'] == 0) {
        // Application is pending approval, redirect to waiting_approval.php
        header("Location: waiting_approval.php");
        exit;
    } else {
        // Application is approved, display business information
        echo "Your store information: <br>";
        echo "Business Name: " . $applicationData['businessName'] . "<br>";
        echo "Business Description: " . $applicationData['businessDescription'] . "<br>";
        echo "Business Address: " . $applicationData['completeAddress'] . "<br>";
        echo "Business Category: " . $applicationData['businessCategory'] . "<br>";
        
      // Product submission form
        echo "<form action='apply.php' method='post' enctype='multipart/form-data'>";
        echo "<input type='file' name='product_img'>";
        echo "<input type='text' name='product_name' placeholder='Product Name'>";
        echo "<textarea name='product_description' placeholder='Product Description'></textarea>";
        echo "<input type='text' name='product_category' placeholder='Product Category'>";
        echo "<input type='number' name='product_quantity' placeholder='Product Quantity'>";
        echo "<input type='number' name='product_price' placeholder='Product Price'>";
        echo "<input type='submit' name='submit_product' class='btn btn-primary' value='Add Product'>";
        echo "</form>";

        // Handle product submission
        if (isset($_POST['submit_product'])) {
            $productImageFileName = ''; // Define a variable to store the filename

            // Handle file upload for product image
            if (isset($_FILES['product_img'])) {
                $productImageFileName = $_FILES['product_img']['name'];
                $productImageTmpName = $_FILES['product_img']['tmp_name'];
                $productImageUploadPath = 'uploaded_products/' . $productImageFileName; // Set the upload path

                // Move the uploaded file to the destination
                move_uploaded_file($productImageTmpName, $productImageUploadPath);
            }

            // Other form data
            $productName = isset($_POST['product_name']) ? $_POST['product_name'] : '';
            $productDescription = isset($_POST['product_description']) ? $_POST['product_description'] : '';
            $productCategory = isset($_POST['product_category']) ? $_POST['product_category'] : '';
            $productQuantity = isset($_POST['product_quantity']) ? $_POST['product_quantity'] : 0;
            $productPrice = isset($_POST['product_price']) ? $_POST['product_price'] : 0;

            // Insert data into the 'products' table
            $productSql = "INSERT INTO products (user_id, product_name, product_description, product_category, product_quantity, product_price, product_img)
                    VALUES ('$user_id', '$productName', '$productDescription', '$productCategory', $productQuantity, $productPrice, '$productImageFileName')";

            if ($conn->query($productSql) === TRUE) {
                echo "Product added successfully.";
            } else {
                echo "Error adding product: " . $conn->error;
            }
        }

        exit;
    }
}

// User has not applied or the application is not approved, display the application form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ... (rest of your application submission code)

    // Redirect to waiting approval page after successful submission
    header("Location: waiting_approval.php");
    exit;
}


$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
	body {

		height: 890px;
	
	}
        
    </style>
    <title>Business Application Form</title>
</head>
<body>

<div class="container mt-5">
    <div class="custom-box">
        <a href="home.php" class="btn btn-secondary mb-3">Back</a>
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <h2 class="mb-4 text-end">Business Application Form</h2>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
                    <!-- Upload Valid ID -->
                    <div class="mb-3">
                        <label for="validId" class="form-label">Upload Valid ID</label>
                        <input type="file" class="form-control" id="validId" name="validId" accept="image/*" required>
                    </div>

                    <!-- Business Name -->
                    <div class="mb-3">
                        <label for="businessName" class="form-label">Business Name</label>
                        <input type="text" class="form-control" id="businessName" name="businessName" required>
                    </div>

                    <!-- Business Description -->
                    <div class="mb-3">
                        <label for="businessDescription" class="form-label">Business Description</label>
                        <textarea class="form-control" id="businessDescription" name="businessDescription" rows="3" required></textarea>
                    </div>

                    <!-- Complete Address -->
                    <div class="mb-3">
                        <label for="completeAddress" class="form-label">Complete Address</label>
                        <input type="text" class="form-control" id="completeAddress" name="completeAddress" required>
                    </div>

                    <!-- Business Number -->
                    <div class="mb-3">
                        <label for="businessNumber" class="form-label">Business Number</label>
                        <input type="tel" class="form-control" id="businessNumber" name="businessNumber" required>
                    </div>

                    <!-- Business Email -->
                    <div class="mb-3">
                        <label for="businessEmail" class="form-label">Business Email</label>
                        <input type="email" class="form-control" id="businessEmail" name="businessEmail" required>
                    </div>

                    <!-- Business Category (Dropdown/Select) -->
                    <div class="mb-3">
                        <label for="businessCategory" class="form-label">Business Category</label>
                        <select class="form-select" id="businessCategory" name="businessCategory" required>
                            <option value="" disabled selected>Select Business Category</option>
                            <option value="Electronics">Electronics</option>
                            <option value="Gadgets">Gadgets</option>
                            <option value="E-Load">E-Load</option>
                            <option value="ForKids">For Kids</option>
                            <option value="AllItems">All Items</option>
                        </select>
                    </div>

                    <!-- Apply Button -->
                    <button type="submit" class="btn btn-primary">Apply</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS (optional) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
