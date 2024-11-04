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

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture all form fields and trim whitespace
    $id = trim($_POST['id'] ?? '');
    $dob = trim($_POST['dob'] ?? '');
    $temp_password = trim($_POST['temp_password'] ?? '');
    $new_password = trim($_POST['new_password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    // Validate that all required fields are filled
    if (empty($id) || empty($dob) || empty($temp_password) || empty($new_password) || empty($confirm_password)) {
        echo "<script>alert('All fields are required.'); window.history.back();</script>";
        exit();
    }

    // Check if new password and confirm password match
    if ($new_password !== $confirm_password) {
        echo "<script>alert('New password and confirm password do not match.'); window.history.back();</script>";
        exit();
    }

    // Prepare statement to check for existing user
    $stmt = $pdo->prepare("SELECT hashedPassword FROM glc_users WHERE id = :id AND dob = :dob");
    $stmt->execute([':id' => $id, ':dob' => $dob]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Verify the temporary password
        if (password_verify($temp_password, $user['hashedPassword'])) {
            // Hash the new password for security
            $hashedNewPassword = password_hash($new_password, PASSWORD_DEFAULT);

            // Prepare statement to update the password in the database
            $update_stmt = $pdo->prepare("UPDATE glc_users SET hashedPassword = :hashedPassword WHERE id = :id");
            if ($update_stmt->execute([':hashedPassword' => $hashedNewPassword, ':id' => $id])) {
                // Redirect to the login page after successful password reset
                header("Location: ../login.html");
                exit();
            } else {
                echo "<script>alert('Failed to reset password. Please try again.'); window.history.back();</script>";
                exit();
            }
        } else {
            echo "<script>alert('Temporary password is incorrect.'); window.history.back();</script>";
            exit();
        }
    } else {
        echo "<script>alert('No user found with the provided Student ID and Date of Birth.'); window.history.back();</script>";
        exit();
    }
}
?>
