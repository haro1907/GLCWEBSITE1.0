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

    // Check if form was submitted for posting or editing news
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['newsContent'])) {
        $newsContent = trim($_POST['newsContent']);

        if ($newsContent) {
            if (isset($_POST['edit_id']) && !empty($_POST['edit_id'])) {
                // Update existing news item
                $editId = intval($_POST['edit_id']);
                $stmt = $pdo->prepare("UPDATE news SET content = ? WHERE id = ?");
                $stmt->execute([$newsContent, $editId]);
            } else {
                // Insert new news item
                $stmt = $pdo->prepare("INSERT INTO news (content) VALUES (?)");
                $stmt->execute([$newsContent]);
            }
        }
        header("Location: post-news.php"); // Redirect to clear the form after submission
        exit();
    }

    // Handle news deletion if requested
    if (isset($_GET['delete_id'])) {
        $deleteId = intval($_GET['delete_id']);
        $stmt = $pdo->prepare("DELETE FROM news WHERE id = ?");
        $stmt->execute([$deleteId]);
        header("Location: post-news.php"); // Refresh the page after deletion
        exit();
    }

    // Check if we’re editing an existing news item
    $editingNews = null;
    if (isset($_GET['edit_id'])) {
        $editId = intval($_GET['edit_id']);
        $stmt = $pdo->prepare("SELECT * FROM news WHERE id = ?");
        $stmt->execute([$editId]);
        $editingNews = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Fetch all news from the database
    $stmt = $pdo->query("SELECT * FROM news ORDER BY created_at DESC");
    $newsItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News Management</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/home.css">
</head>
<body>
    <div class="container">
        <h2><?php echo $editingNews ? "Edit News" : "Post News"; ?></h2>
        <form id="newsForm" action="post-news.php" method="POST">
            <textarea name="newsContent" id="newsContent" placeholder="Write your news here..." required><?php echo $editingNews ? htmlspecialchars($editingNews['content']) : ''; ?></textarea>
            <br>
            <?php if ($editingNews): ?>
                <!-- Hidden field to store the ID of the news item being edited -->
                <input type="hidden" name="edit_id" value="<?php echo $editingNews['id']; ?>">
            <?php endif; ?>
            <button type="submit"><?php echo $editingNews ? "Update" : "Post"; ?></button>
        </form>

        <h2>Your News Posts</h2>
        <ul id="newsList">
            <?php if (!empty($newsItems)) : ?>
                <?php foreach ($newsItems as $news) : ?>
                    <li class="news-item">
                        <span class="news-text"><?php echo htmlspecialchars($news['content']); ?></span>
                        <div class="button-group">
                            <a href="post-news.php?edit_id=<?php echo $news['id']; ?>">Edit</a>
                            <a href="post-news.php?delete_id=<?php echo $news['id']; ?>" onclick="return confirm('Are you sure you want to delete this post?');">Delete</a>
                        </div>
                    </li>
                <?php endforeach; ?>
            <?php else : ?>
                <li>No news posts available at the moment.</li>
            <?php endif; ?>
        </ul>

        <a href="admin.html" class="back-button">Back</a>
    </div>
</body>
</html>
