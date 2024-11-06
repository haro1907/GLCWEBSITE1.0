<?php
session_start();
if ($_SESSION['role'] != 'student') {
    header("Location: ../login.html");
    exit();
}

// Check if the program is set in the session
if (!isset($_SESSION['program'])) {
    echo "Program not set in session.";
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

    // Query to fetch news and announcements
    $newsStmt = $pdo->query("SELECT * FROM news ORDER BY created_at DESC");
    $newsItems = $newsStmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News and Announcements</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/home.css">
</head>
<body>
    <div class="container">
        <h2>Announcements</h2>
        
        <ul id="newsList">
            <?php if (!empty($newsItems)) : ?>
                <?php foreach ($newsItems as $news) : ?>
                    <li>
                        <span><?php echo htmlspecialchars($news['content']); ?></span><br>
                        <em>Posted on: <?php echo date('F j, Y', strtotime($news['created_at'])); ?></em>
                    </li>
                <?php endforeach; ?>
            <?php else : ?>
                <li>No news items available at the moment.</li>
            <?php endif; ?>
        </ul>

        <!-- Back button to student.php -->
        <a href="../student/student.php" class="back-button">Back</a>
    </div>
</body>
</html>
