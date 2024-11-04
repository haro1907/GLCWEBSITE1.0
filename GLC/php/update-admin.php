<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: ../login.html");
    exit();
}

$host = 'localhost';
$db = 'webglc_database';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get updated data from the form
    $username = $_POST['username'];
    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'];
    $lastname = $_POST['lastname'];
    $dob = $_POST['dob'];
    $contact = $_POST['contact'];
    $newPassword = $_POST['password'];

    // If a new password was provided, hash it
    $hashedPassword = null;
    if (!empty($newPassword)) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    }

    // Update user information in the database (without updating email)
    $sql = "UPDATE glc_users SET username = :username, firstname = :firstname, 
            middlename = :middlename, lastname = :lastname, dob = :dob, contact = :contact";
    
    if ($hashedPassword) {
        $sql .= ", hashedPassword = :hashedPassword";
    }
    
    $sql .= " WHERE id = :id";

    $stmt = $pdo->prepare($sql);
    $params = [
        ':username' => $username,
        ':firstname' => $firstname,
        ':middlename' => $middlename,
        ':lastname' => $lastname,
        ':dob' => $dob,
        ':contact' => $contact,
        ':id' => $_SESSION['id']
    ];

    if ($hashedPassword) {
        $params[':hashedPassword'] = $hashedPassword;
    }

    $stmt->execute($params);

    echo "<script>alert('Information updated successfully!'); window.location.href='admin-profile.php';</script>";
    exit();

} catch (PDOException $e) {
    die("Error updating information: " . $e->getMessage());
}
?>
