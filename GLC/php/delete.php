<?php
session_start();

// Database connection details
$host = 'localhost';
$db = 'webglc_database';
$user = 'root'; // Default MySQL username in XAMPP
$pass = '';     // Default MySQL password (usually empty in XAMPP)

try {
    // Create a new PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}

// Check if user is logged in
if (isset($_SESSION['id'])) {
    // Get the logged-in user's ID
    $userId = $_SESSION['id'];

    // Prepare the SQL statement to delete the user
    $sql = "DELETE FROM glc_users WHERE id = :id";
    $stmt = $pdo->prepare($sql);

    // Execute the query
    if ($stmt->execute([':id' => $userId])) {
        // Destroy the session and redirect to login page
        session_destroy();
        header("Location: ../login.html");
        exit();
    } else {
        echo "Error: Could not delete the user.";
    }
} else {
    echo "Error: User not logged in.";
}
?>
