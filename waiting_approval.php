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

// Check if the user has a pending application
$pendingApplicationQuery = "SELECT * FROM apply WHERE user_id = '$user_id' AND is_approved = 0";
$pendingApplicationResult = $conn->query($pendingApplicationQuery);

if ($pendingApplicationResult->num_rows > 0) {
    // Display a message indicating that the application is pending approval
    echo "Your business application is pending approval. Please wait for confirmation.";
} else {
    // Redirect to apply.php if the user doesn't have a pending application
    header("Location: apply.php");
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Waiting for Approval</title>
</head>
<body>

<!-- Your HTML content for the waiting_approval.php page goes here -->

<!-- JavaScript code to redirect after 3 seconds -->
<script>
    setTimeout(function() {
        window.location.href = 'home.php';
    }, 3000); // 3000 milliseconds = 3 seconds
</script>

</body>
</html>
