<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: ../login.html");
    exit();
}

// Database connection details
$host = 'localhost';
$db = 'webglc_database';
$user = 'root'; // Default MySQL username in XAMPP
$pass = '';     // Default MySQL password (usually empty in XAMPP)

try {
    // Create a new PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch all users from the database, including program information
    $stmt = $pdo->query("SELECT id, username, email, firstname, middlename, lastname, dob, contact, program FROM glc_users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}

// Handle updates for email, ID, and program
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Update user details
    if (isset($_POST['update_user'])) {
        $userId = $_POST['user_id'];
        $newEmail = $_POST['new_email'];
        $newId = $_POST['new_id'];
        $newProgram = $_POST['new_program'];

        // Debugging output
        echo "<pre>";
        print_r($_POST);
        echo "</pre>";

        // Validate email format
        if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            echo "<script>alert('Invalid email format.'); window.history.back();</script>";
            exit();
        }

        // Update ID, email, and program in the database
        $updateStmt = $pdo->prepare("UPDATE glc_users SET id = :id, email = :email, program = :program WHERE id = :user_id");
        if ($updateStmt->execute([':id' => $newId, ':email' => $newEmail, ':program' => $newProgram, ':user_id' => $userId])) {
            echo "<script>alert('User details updated successfully.'); window.location.href = window.location.href;</script>";
        } else {
            echo "<script>alert('Failed to update user details.'); window.history.back();</script>";
        }
    }

    // Delete user
    if (isset($_POST['delete_user'])) {
        $userId = $_POST['user_id'];

        // Now delete the user from glc_users; related prospectus_status will be deleted automatically
        $deleteStmt = $pdo->prepare("DELETE FROM glc_users WHERE id = :user_id");
        if ($deleteStmt->execute([':user_id' => $userId])) {
            echo "<script>alert('User deleted successfully.'); window.location.href = window.location.href;</script>";
        } else {
            echo "<script>alert('Failed to delete user.'); window.history.back();</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Accounts</title>
    <link rel="stylesheet" href="../css/manage.css">
</head>
<body>
    <div class="container">
        <div class="login-box">
            <a href="admin.html" class="back-button">Back</a>

            <!-- Display users from MySQL database -->
            <h2>All Users</h2>
            <table border="1">
                <thead>
                    <tr>
                        <th>Identification Number</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>First Name</th>
                        <th>Middle Name</th>
                        <th>Last Name</th>
                        <th>Date of Birth</th>
                        <th>Age</th>
                        <th>Contact Number</th>
                        <th>Program</th>
                        <th>Action</th> <!-- Added Action Column -->
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['id']); ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['firstname']); ?></td>
                            <td><?php echo htmlspecialchars($user['middlename']); ?></td>
                            <td><?php echo htmlspecialchars($user['lastname']); ?></td>
                            <td><?php echo htmlspecialchars($user['dob']); ?></td>
                            <td>
                                <?php 
                                    // Calculate age from the date of birth
                                    $dob = new DateTime($user['dob']);
                                    $today = new DateTime();
                                    $age = $today->diff($dob)->y;
                                    echo $age; // Display age
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($user['contact']); ?></td>
                            <td><?php echo htmlspecialchars($user['program']); ?></td>
                            <td>
                                <!-- Edit Form for ID, Email, and Program -->
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id']); ?>">
                                    <input type="text" name="new_id" placeholder="New ID" value="<?php echo htmlspecialchars($user['id']); ?>" required>
                                    <input type="email" name="new_email" placeholder="New Email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                    <select name="new_program" required>
                                        <option value="BSIT" <?php echo $user['program'] == 'BSIT' ? 'selected' : ''; ?>>BSIT</option>
                                        <option value="BSBA" <?php echo $user['program'] == 'BSBA' ? 'selected' : ''; ?>>BSBA</option>
                                        <option value="BSAIS" <?php echo $user['program'] == 'BSAIS' ? 'selected' : ''; ?>>BSAIS</option>
                                        <option value="BSPSYCH" <?php echo $user['program'] == 'BSPSYCH' ? 'selected' : ''; ?>>BSPSYCH</option>
                                        <option value="BEED" <?php echo $user['program'] == 'BEED' ? 'selected' : ''; ?>>BEED</option>
                                        <option value="BSE-ENG" <?php echo $user['program'] == 'BSE-ENG' ? 'selected' : ''; ?>>BSE-ENG</option>
                                        <option value="BSE-SCI" <?php echo $user['program'] == 'BSE-SCI' ? 'selected' : ''; ?>>BSE-SCI</option>
                                        <option value="BSE-MATH" <?php echo $user['program'] == 'BSE-MATH' ? 'selected' : ''; ?>>BSE-MATH</option>
                                        <option value="STAFF" <?php echo $user['program'] == 'STAFF' ? 'selected' : ''; ?>>STAFF</option>
                                    </select>
                                    <button type="submit" name="update_user">Update</button>
                                </form>
                                <!-- Delete Form -->
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id']); ?>">
                                    <button type="submit" name="delete_user" onclick="return confirm('Are you sure you want to delete this user?');">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
