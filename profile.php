<?php
session_start();

// Your database credentials
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

// Extract user_id from the query string
$profileUserId = isset($_GET['user_id']) ? $_GET['user_id'] : null;

if (!$profileUserId) {
    // Redirect to a default page if no user_id is provided
    header("Location: home.php"); 
    exit;
}

// Fetch user profile data from the apply table
$profileQuery = "SELECT * FROM apply WHERE user_id = '$profileUserId'";
$profileResult = $conn->query($profileQuery);

// Check if the profile user exists
if ($profileResult->num_rows > 0) {
    $profileData = $profileResult->fetch_assoc();
} else {
    // Redirect to a default page if the profile user doesn't exist
    header("Location: home.php");
    exit;
}

// Handle the form submission for ratings and reviews
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $rating = isset($_POST['rating']) ? $_POST['rating'] : null;
    $reviewComment = isset($_POST['review_comment']) ? $_POST['review_comment'] : null;

    // Insert the ratings and review into your database table (replace 'ratings_table' with your actual table name)
    $insertReviewQuery = "INSERT INTO ratings (user_id, rating, review_comment) 
                          VALUES ('$profileUserId', '$rating', '$reviewComment')";

    if ($conn->query($insertReviewQuery) === TRUE) {
        echo "Review submitted successfully.";
        // You can redirect the user or display a success message as needed
    } else {
        echo "Error: " . $conn->error;
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop Profile</title>
    <link rel="stylesheet" href="your-bootstrap-stylesheet.css">
    <!-- Add any other stylesheets or meta tags your page needs -->
</head>
<body>

<!-- Display apply information -->
<h1>Shop Owner</h1>
<p>Business Name: <?php echo $profileData['businessName']; ?></p>
<p>Business Description: <?php echo $profileData['businessDescription']; ?></p>
<p>Business Address: <?php echo $profileData['completeAddress']; ?></p>
<p>Business Contact: <?php echo $profileData['businessNumber']; ?></p>
<p>Business Email: <?php echo $profileData['businessEmail']; ?></p>
<p>Business Category: <?php echo $profileData['businessCategory']; ?></p>

<?php
if (isset($_SESSION['user_id'])) {
    // Ratings and Reviews Form
    echo '<form method="post" action="">';
    echo '<label for="rating">Rating:</label>';
    echo '<select name="rating" id="rating" required>';
    echo '<option value="1">1 Star</option>';
    echo '<option value="2">2 Stars</option>';
    echo '<option value="3">3 Stars</option>';
    echo '<option value="4">4 Stars</option>';
    echo '<option value="5">5 Stars</option>';
    echo '</select>';

    echo '<label for="review_comment">Review Comment:</label>';
    echo '<textarea name="review_comment" id="review_comment" rows="4" required></textarea>';

    echo '<button type="submit">Submit Review</button>';
    echo '</form>';
}
?>

<!-- Display Existing Reviews -->
<h2>Customer Reviews:</h2>
<?php
// Fetch reviews for the profile user from your database (replace 'ratings_table' with your actual table name)
$fetchReviewsQuery = "SELECT * FROM ratings WHERE user_id = '$profileUserId'";
$reviewsResult = $conn->query($fetchReviewsQuery);

if (!$reviewsResult) {
    // Handle query error
    echo "Error: " . $conn->error;
} else {
    if ($reviewsResult->num_rows > 0) {
        while ($reviewData = $reviewsResult->fetch_assoc()) {
            echo '<div>';
            echo '<p>Rating: ' . $reviewData['rating'] . ' Stars</p>';
            echo '<p>Review Comment: ' . $reviewData['review_comment'] . '</p>';
            echo '</div>';
        }
    } else {
        echo '<p>No reviews yet.</p>';
    }
}
?>

</body>
</html>
