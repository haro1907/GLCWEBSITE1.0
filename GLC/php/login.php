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

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST['username_or_email']) || empty($_POST['password'])) {
        echo "<script>alert('Please fill in all required fields.'); window.history.back();</script>";
    } else {
        // Sanitize input
        $usernameOrEmail = filter_var($_POST['username_or_email'], FILTER_SANITIZE_STRING);
        $password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);

        // Prepare SQL query to check username/email, password
        $sql = "SELECT * FROM glc_users WHERE (username = :username_or_email OR email = :username_or_email)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':username_or_email' => $usernameOrEmail
        ]);

        // Fetch user data
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($userData && password_verify($password, $userData['hashedPassword'])) {
            // Store user data in session
            $_SESSION['id'] = $userData['id'];
            $_SESSION['username'] = $userData['username'];
            $_SESSION['firstname'] = $userData['firstname'];
            $_SESSION['lastname'] = $userData['lastname'];
            $_SESSION['middlename'] = $userData['middlename'];
            $_SESSION['email'] = $userData['email'];
            $_SESSION['dob'] = $userData['dob'];
            $_SESSION['contact'] = $userData['contact'];
            $_SESSION['role'] = $userData['role'];
            $_SESSION['program'] = $userData['program']; // Add the program to session data

            // Redirect based on user role
            if ($userData['role'] == 'admin') {
                header("Location: ../admin/admin.html");
            } elseif ($userData['role'] == 'student') {
                        header("Location: ../student/student.php"); 
                }
            exit();
        } else {
            echo "<script>
                if (confirm('The credentials do not match our records. Click OK to try again.')) {
                    window.history.back();
                }
            </script>";
        }
    }
}
?>
