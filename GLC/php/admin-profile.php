<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: ../login.html");
    exit();
}

// Database connection details
$host = 'localhost';
$db = 'webglc_database';
$user = 'root';
$pass = '';

try {
    // Create a new PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query to fetch user data by ID
    $stmt = $pdo->prepare("SELECT * FROM glc_users WHERE id = ?");
    $stmt->execute([$_SESSION['id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "User not found.";
        exit();
    }

    // Calculate user's age from date of birth
    $dob = new DateTime($user['dob']);
    $today = new DateTime();
    $age = $today->diff($dob)->y;
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
    <div class="container">
        <div class="login-box">
            <h2>Admin Dashboard</h2>
            
            <!-- Display User Information in Form -->
            <form action="update-admin.php" method="post">
                <p><strong>Identification Number:</strong> <?php echo htmlspecialchars($user['id']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
     
                <div class="input-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" >
                </div>

                <div class="input-group">
                    <label for="firstname">First Name</label>
                    <input type="text" name="firstname" value="<?php echo htmlspecialchars($user['firstname']); ?>" >
                </div>

                <div class="input-group">
                    <label for="middlename">Middle Name</label>
                    <input type="text" name="middlename" value="<?php echo htmlspecialchars($user['middlename']); ?>">
                </div>

                <div class="input-group">
                    <label for="lastname">Last Name</label>
                    <input type="text" name="lastname" value="<?php echo htmlspecialchars($user['lastname']); ?>" >
                </div>

                <div class="input-group">
                    <label for="dob">Date of Birth</label>
                    <input type="date" name="dob" value="<?php echo htmlspecialchars($user['dob']); ?>" >
                </div>

                <div class="input-group">
                    <label for="contact">Contact Number</label>
                    <input type="tel" name="contact" value="<?php echo htmlspecialchars($user['contact']); ?>" pattern="[0-9]{11}" maxlength="11" >
                </div>

                <div class="input-group">
                    <label for="password">New Password</label>
                    <input type="password" name="password" placeholder="Enter new password (leave blank to keep current)">
                </div>

                <!-- Update Information Button -->
                <input type="submit" value="Update Information" class="submit-button">
            </form>
            
            <!-- Delete Account Option -->
            <form action="delete.php" method="post" onsubmit="return confirm('Are you sure you want to delete your account?');">
                <input type="submit" value="Delete Account" class="delete-button">
            </form>
            <br>
            <a href="../admin/admin.html" class="back-button">Back</a>
        </div>
    </div>
</body>
</html>
