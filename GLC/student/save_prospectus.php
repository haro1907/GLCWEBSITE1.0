<?php
session_start();

// Include your database connection code
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
    // Check if any courses were selected
    if (empty($_POST['course_code'])) {
        // Redirect to student.php if no courses were selected
        header("Location: ../student/student.php");
        exit();
    }

    // Retrieve the checked courses from the form
    $courses = array_keys($_POST['course_code']); // Get the course codes from the array
    $userId = $_SESSION['id']; // Use the session ID to identify the user

    // Prepare the SQL query to insert the selected courses
    $sql = "INSERT INTO prospectus_status (user_id, course_code, taken) VALUES (:user_id, :course_code, 1)
            ON DUPLICATE KEY UPDATE taken = 1"; // Set taken to 1 if the entry already exists

    $stmt = $pdo->prepare($sql);

    // Execute the query for each selected course
    foreach ($courses as $course) {
        try {
            $stmt->execute([
                ':user_id' => $userId,
                ':course_code' => $course
            ]);
        } catch (PDOException $e) {
            // Handle any errors during insertion
            header("Location: ../student/student.php");
            exit();
        }
    }

    // Redirect based on the user's program with a success message
    $userProgram = $_SESSION['program']; // Assuming you store the user's program in the session
    $successMessage = urlencode("Save successful"); // URL-encode the message for use in URL

    switch ($userProgram) {
        case 'BSPSYCH':
            header("Location: BSPSYCH.php?message=$successMessage");
            break;
        case 'BSE-ENG':
            header("Location: BSE-ENG.php?message=$successMessage");
            break;
        case 'BSE-MATH':
            header("Location: BSE-MATH.php?message=$successMessage");
            break;
        case 'BSE-SCI':
            header("Location: BSE-SCI.php?message=$successMessage");
            break;
        case 'BEED':
            header("Location: BEED.php?message=$successMessage");
            break;
        case 'BSBA':
            header("Location: BSBA.php?message=$successMessage");
            break;
        case 'BSAIS':
            header("Location: BSAIS.php?message=$successMessage");
            break;
        case 'BSIT':
        default: // Default case if none match
            header("Location: BSIT.php?message=$successMessage");
            break;
    }
    exit();
} else {
    // Redirect if not a POST request
    header("Location: ../student/student.php");
    exit();
}
?>
