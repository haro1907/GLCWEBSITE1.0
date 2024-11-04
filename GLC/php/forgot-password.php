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
    // Capture form fields
    $studentId = filter_var($_POST['student_id'], FILTER_SANITIZE_STRING);
    $dob = filter_var($_POST['dob'], FILTER_SANITIZE_STRING);

    // Query to check if the student ID and DOB match
    $sql = "SELECT * FROM glc_users WHERE id = :student_id AND dob = :dob"; // Update column names as necessary
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':student_id' => $studentId,
        ':dob' => $dob
    ]);

    // Fetch user data
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($userData) {
        // Generate a temporary password
        $tempPassword = bin2hex(random_bytes(4)); // 8-character temporary password
        $hashedTempPassword = password_hash($tempPassword, PASSWORD_DEFAULT);

        // Update the user's password in the database
        $updateSql = "UPDATE glc_users SET hashedPassword = :hashedPassword WHERE id = :id";
        $updateStmt = $pdo->prepare($updateSql);
        $updateStmt->execute([
            ':hashedPassword' => $hashedTempPassword,
            ':id' => $userData['id']
        ]);

        // Inform the user of the temporary password
        echo "<script>alert('Your temporary password is: $tempPassword. Please log in and change it immediately.'); window.location.href='../reset-password.html';</script>";
    } else {
        // Alert if student ID and DOB do not match records
        echo "<script>alert('Student ID and Date of Birth do not match our records.'); window.history.back();</script>";
    }
}
?>
