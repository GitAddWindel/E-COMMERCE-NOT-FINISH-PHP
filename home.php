<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Extract user information from the session
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Database connection
$conn = new mysqli('localhost', 'root', '', 'paste_code');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//File upload handling
if (isset($_FILES["profilePicture"])) {
    $target_dir = "uploads/";
    $target_file = $target_dir . $user_id . "_" . basename($_FILES["profilePicture"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is a valid image
    $check = getimagesize($_FILES["profilePicture"]["tmp_name"]);
    if ($check === false) {
        echo "File is not a valid image.";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["profilePicture"]["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    $allowed_extensions = array("jpg", "jpeg", "png", "gif");
    if (!in_array($imageFileType, $allowed_extensions)) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        // If everything is ok, try to upload file
        if (move_uploaded_file($_FILES["profilePicture"]["tmp_name"], $target_file)) {
            // Update the user's profile picture in the database
            $sql = "INSERT INTO profiles (user_id, profile_picture) VALUES ('$user_id', '$target_file')";

            if ($conn->query($sql) === TRUE) {
                echo "Profile picture updated successfully.";
		header("Location: home.php");
		exit;
            } else {
                echo "Error updating profile picture: " . $conn->error;
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
} else {
    
}

// Retrieve profile picture from the database
$sql = "SELECT profile_picture FROM profiles WHERE user_id = '$user_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $profile_picture = $row['profile_picture'];

}

// Retrieve profile information from the database
$sql = "SELECT profiles.profile_picture, users.fullname, users.username, users.email, users.contact
        FROM profiles
        INNER JOIN users ON profiles.user_id = users.user_id
WHERE profiles.user_id = '$user_id'";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
$row = $result->fetch_assoc();

// Extract the retrieved information
$profile_picture = $row['profile_picture'];
$fullname = $row['fullname'];
$username = $row['username'];
$email = $row['email'];
$contact = $row['contact'];
} else {
// Handle the case where no profile is found
echo "Profile not found.";
// You might want to redirect the user to a different page or display an error message
// header("Location: error.php");
// exit;
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $updateFullname = $_POST['updateFullname'];
    $updateUsername = $_POST['updateUsername'];
    $updateEmail = $_POST['updateEmail'];
    $updateContact = $_POST['updateContact'];
    $updatePassword = $_POST['updatePassword'];

    // Update user information in the database
    $sql = "UPDATE users SET
            fullname = '$updateFullname',
            username = '$updateUsername',
            email = '$updateEmail',
            contact = '$updateContact',
            password = '$updatePassword'
            WHERE user_id = '$user_id'";

    if ($conn->query($sql) === TRUE) {
        echo "Profile updated successfully.";
	header("Location: home.php");
	exit;
    } else {
        echo "Error updating profile: " . $conn->error;
    }

    // Handle profile picture update separately
    if ($_FILES['updateProfilePicture']['error'] == UPLOAD_ERR_OK) {
        // File upload handling
        $target_dir = "uploads/";
        $target_file = $target_dir . $user_id . "_" . basename($_FILES["updateProfilePicture"]["name"]);

        // Move the uploaded file to the target location
        if (move_uploaded_file($_FILES["updateProfilePicture"]["tmp_name"], $target_file)) {
            // Update profile picture in the database
            $sqlPicture = "UPDATE profiles SET profile_picture = '$target_file' WHERE user_id = '$user_id'";

            if ($conn->query($sqlPicture) === TRUE) {
                header("Location: home.php");
	exit;
            } else {
                echo "Error updating profile picture: " . $conn->error;
            }
        } else {
            echo "Sorry, there was an error uploading your profile picture.";
        }
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Bootstrap 5 Navbar with Logo and Pen Edit Icon</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }



        #welcomeDiv {
            background-color: #fff;
            color: #fff;
            padding: 20px;
            border-radius: 8px;
            position: relative;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
	    width: 20%;
	    margin-top: 20px;
 	    margin-left: 20px;
        }

    

       small {
            margin-bottom: 16px;
            color: #000;
        }

    
 	.fly-in-left-to-right {
            opacity: 0;
            transform: translateX(-100%);
            animation: flyInLeftToRight 1s forwards;
        }
   	@keyframes flyInLeftToRight {
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
#horizontal-line-loading {
    width: 100%;
    height: 6px;
    background-color: red;
    overflow: hidden;
}

#horizontal-line-progress {
    width: 100%;
    height: 100%;
    background-color: #fff;
    transform-origin: left center; /* Change from right to left */
    animation: loadLine 3s linear forwards;
}

@keyframes loadLine {
    to {
        transform: scaleX(0);
    }
}

  .products-container-mt5 {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
	    margin-top: 30px;
        }

        .product-card-mt5 {
            border: 1px solid transparent;
            padding: 10px;
            text-align: center;
            width: 320px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
	    background-color: #fff;
	    border-radius: 5px;
	   
        }

        .product-image {
            max-width: 100%;
            height: auto;
            margin-bottom: 10px;
        }

        .button-container {
            margin-top: auto;
        }   
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="#">
            <img src="your-logo-image.png" width="30" height="30" class="d-inline-block align-top" loading="lazy">
        </a>

        <!-- Left side of the navbar -->
        <div class="navbar-nav">
            <a href="#" class="nav-link text-white">Home</a>
            <a href="#" class="nav-link text-white">Marketplace</a>
             <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a href="#" class="nav-link text-white position-relative">
                        Orders
                        <span class="badge bg-success position-absolute top-0 start-80 translate-middle">
                            5 <!-- Change this number based on your notification count -->
                        </span>
                    </a>
                </li>
            </ul>
        </div>
	    <li class="nav-item">
                    <a href="#" class="nav-link text-white position-relative">
                        Cart
                        <span class="badge bg-danger position-absolute top-0 start-100 translate-middle">
                            3 <!-- Change this number based on your notification count -->
                        </span>
                    </a>
                </li>
		<a href="apply.php" class="nav-link text-white">Business Account</a>
        </div>

        <!-- Right side of the navbar -->
        <div class="navbar-nav ms-auto">
            <!-- Profile image with dropdown -->
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle text-white" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="<?php echo $profile_picture; ?>" alt="profile" class="rounded-circle" width="32" height="32">
			<!-- Modify the pen icon code -->
			<i class="fas fa-pen text-white ml-2" data-bs-toggle="modal" data-bs-target="#uploadModal"></i>

                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
	        <!-- Update your "Profile" link -->
			<li>
    			<a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#profileModal"
       			data-fullname="<?php echo $fullname; ?>"
       			data-username="<?php echo $username; ?>"
       			data-email="<?php echo $email; ?>"
       			data-contact="<?php echo $contact; ?>"
       			data-profile-picture="<?php echo $profile_picture; ?>">Profile</a>
			</li>


                    <li><a href="business_products_show.php" class="dropdown-item">Selling Products</a></li>
                    <li><a href="logout.php" class="dropdown-item text-danger">Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>


<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="#">E - Commerce Website</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <form class="d-flex ms-auto">
                <input class="form-control me-2" type="search" placeholder="Search products" aria-label="Search">
                <button class="btn btn-outline-success" type="submit">Search</button>
            </form>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="electronicsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Electronics <i class="bi bi-chevron-right"></i>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="electronicsDropdown">
                        <li><a class="dropdown-item" href="#">Item 1</a></li>
                        <li><a class="dropdown-item" href="#">Item 2</a></li>
                        <!-- Add more items as needed -->
                    </ul>
                </li>

		 <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="gadgetsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        E-Load <i class="bi bi-chevron-right"></i>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="gadgetsDropdown">
                        <li><a class="dropdown-item" href="#">Item 1</a></li>
                        <li><a class="dropdown-item" href="#">Item 2</a></li>
                        <!-- Add more items as needed -->
                    </ul>
                </li>


                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="gadgetsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Gadgets <i class="bi bi-chevron-right"></i>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="gadgetsDropdown">
                        <li><a class="dropdown-item" href="#">Item 1</a></li>
                        <li><a class="dropdown-item" href="#">Item 2</a></li>
                        <!-- Add more items as needed -->
                    </ul>
                </li>

		 <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="gadgetsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        For Kids <i class="bi bi-chevron-right"></i>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="gadgetsDropdown">
                        <li><a class="dropdown-item" href="#">Item 1</a></li>
                        <li><a class="dropdown-item" href="#">Item 2</a></li>
                        <!-- Add more items as needed -->
                    </ul>
                </li>
                
		 <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="gadgetsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        All Items <i class="bi bi-chevron-right"></i>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="gadgetsDropdown">
                        <li><a class="dropdown-item" href="#">Item 1</a></li>
                        <li><a class="dropdown-item" href="#">Item 2</a></li>
                        <!-- Add more items as needed -->
                    </ul>
                </li>


            </ul>
        </div>
    </div>
</nav>

<div class="section-first-main">

<!-- Add this before the closing </body> tag -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">Upload Profile Picture</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Your form for profile picture upload goes here -->
                <form action="home.php" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="profilePicture" class="form-label">Choose a file:</label>
                        <input type="file" name="profilePicture" id="profilePicture" accept="image/*" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- Add this at the end of your HTML body -->
<div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="profileModalLabel">User Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
    <!-- Display user profile information here -->
    <img src="" alt="profile" id="modalProfilePicture" class="rounded-circle" width="100" height="100">
    <p><strong>Fullname:</strong> <span id="modalFullname"></span></p>
    <p><strong>Username:</strong> <span id="modalUsername"></span></p>
    <p><strong>Email:</strong> <span id="modalEmail"></span></p>
    <p><strong>Contact:</strong> <span id="modalContact"></span></p>

    <!-- Form for updating profile information -->
    <form action="home.php" method="post" enctype="multipart/form-data" class="row g-3">
    <!-- Left Column -->
    <div class="col-md-6">
        <!-- Input field for profile picture -->
        <div class="mb-3">
            <label for="updateProfilePicture" class="form-label">Update Profile Picture:</label>
            <input type="file" class="form-control" id="updateProfilePicture" name="updateProfilePicture" accept="image/*">
        </div>

        <!-- Other input fields for updating information -->
        <div class="mb-3">
            <label for="updateFullname" class="form-label">Fullname:</label>
            <input type="text" class="form-control" id="updateFullname" name="updateFullname">
        </div>

        <div class="mb-3">
            <label for="updateUsername" class="form-label">Username:</label>
            <input type="text" class="form-control" id="updateUsername" name="updateUsername">
        </div>
    </div>

    <!-- Right Column -->
    <div class="col-md-6">
        <div class="mb-3">
            <label for="updateEmail" class="form-label">Email:</label>
            <input type="email" class="form-control" id="updateEmail" name="updateEmail">
        </div>

        <div class="mb-3">
            <label for="updateContact" class="form-label">Contact:</label>
            <input type="text" class="form-control" id="updateContact" name="updateContact">
        </div>

        <div class="mb-3">
            <label for="updatePassword" class="form-label">Password:</label>
            <input type="password" class="form-control" id="updatePassword" name="updatePassword">
        </div>

        <!-- Submit button for the form -->
        <button type="submit" class="btn btn-primary float-end">Update Profile</button>
    </div>
</form>

</div>

        </div>
    </div>
</div>


    <div id="welcomeDiv" class="fly-in-left-to-right">
        <small class="fly-in-left-to-right">Welcome, <?php echo $username; ?>!</small>
	 <div id="horizontal-line-loading">
        <div id="horizontal-line-progress"></div>
    </div>
</div>


<!-- Unique Container for Products using Bootstrap 5 -->
<div id="products-container-mt5" class="container mt-5">
    <h1 class="mt-3 mb-3">My Products</h1>

    <div class="products-container-mt5">
 <?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'paste_code';

// Create a new mysqli instance
$conn = new mysqli($host, $username, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Extract user information from the session
$user_id = $_SESSION['user_id'];

// Fetch products for the logged-in user
$productQuery = "SELECT * FROM products WHERE user_id = '$user_id'";
$productResult = $conn->query($productQuery);

        // Display products
        while ($productData = $productResult->fetch_assoc()) {
            echo '<div class="product-card-mt5">';
            echo '<img src="uploaded_products/' . $productData['product_img'] . '" class="product-image" alt="Product Image">';
            echo '<h3>' . $productData['product_name'] . '</h3>';

            // Fetch business name from apply table based on user_id
            $businessNameQuery = "SELECT businessName FROM apply WHERE user_id = '" . $productData['user_id'] . "'";
            $businessNameResult = $conn->query($businessNameQuery);

            // Check if the query executed successfully
            if (!$businessNameResult) {
                echo 'Error fetching business name: ' . $conn->error;
            } else {
                // Check if any rows were returned
                if ($businessNameResult->num_rows > 0) {
                    $businessNameData = $businessNameResult->fetch_assoc();
                    echo '<p>Shop: <a href="profile.php?user_id=' . $productData['user_id'] . '"> ' . $businessNameData['businessName'] . '</a></p>';
                } else {
                    echo '<p>Business Name not found</p>';
                }
            }

            echo '<p>' . $productData['product_description'] . '</p>';
            echo '<p>Category: ' . $productData['product_category'] . '</p>';
            echo '<p>Quantity: ' . $productData['product_quantity'] . '</p>';
            echo '<p>Price: ₱' . $productData['product_price'] . '</p>';
            echo '<div class="button-container">';
            echo '<button class="btn btn-primary" onclick="addToCart(' . $productData['id'] . ')">Add to Cart</button>&nbsp;';
            echo '<button class="btn btn-success" onclick="buyNow(' . $productData['id'] . ')">Buy Now</button>';
            echo '</div>';
            echo '</div>';
        }
        ?>
    </div>
</div>

<?php
$conn->close();
?>

















    <script>
       // Get the reference to the div
        var welcomeDiv = document.getElementById("welcomeDiv");
        var lineLoading = document.getElementById("horizontal-line-loading");

        // Show the div after 3 seconds
        setTimeout(function() {
            welcomeDiv.style.display = "block";
        }, 100);

        // Hide the div after 6 seconds (3 seconds delay + 3 seconds display time)
        setTimeout(function() {
            welcomeDiv.style.display = "none";
        }, 3000);

 // Add this script at the end of your HTML body
    document.addEventListener('DOMContentLoaded', function () {
        // Event listener for the "Profile" link
        document.querySelector('.dropdown-item[data-bs-target="#profileModal"]').addEventListener('click', function () {
            // Get user information from data attributes
            const fullname = this.getAttribute('data-fullname');
            const username = this.getAttribute('data-username');
            const email = this.getAttribute('data-email');
            const contact = this.getAttribute('data-contact');
            const profilePicture = this.getAttribute('data-profile-picture');

            // Update modal content with user information
            document.getElementById('modalProfilePicture').src = profilePicture;
            document.getElementById('modalFullname').innerText = fullname;
            document.getElementById('modalUsername').innerText = username;
            document.getElementById('modalEmail').innerText = email;
            document.getElementById('modalContact').innerText = contact;
        });
    });

 

    </script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
</body>
</html>

