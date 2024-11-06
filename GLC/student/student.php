<?php
// Start the session at the beginning of the script
session_start();

// Check if the necessary session variables are set
if (isset($_SESSION['role']) && isset($_SESSION['program'])) {
    $userRole = $_SESSION['role'];
    $userProgram = $_SESSION['program'];

    // Determine the prospectus link based on role and program
    $prospectusUrl = "../student/default.html"; // Default fallback link

    if ($userRole == 'admin') {
        $prospectusUrl = "../admin/admin.html";
    } elseif ($userRole == 'student') {
        switch ($userProgram) {
            case 'BSPSYCH':
                $prospectusUrl = "../student/BSPSYCH.html"; 
                break;
            case 'BSE-ENG':
                $prospectusUrl = "../student/BSE-ENG.html"; 
                break;
            case 'BSE-MATH':
                $prospectusUrl = "../student/BSE-MATH.html";
                break;
            case 'BSE-SCI':
                $prospectusUrl = "../student/BSE-SCI.html"; 
                break;
            case 'BEED':
                $prospectusUrl = "../student/BEED.html"; 
                break;
            case 'BSBA':
                $prospectusUrl = "../student/BSBA.html"; 
                break;
            case 'BSAIS':
                $prospectusUrl = "../student/BSAIS.html"; 
                break;
            case 'BSIT':
                $prospectusUrl = "../student/BSIT.php"; 
                break;
            default:
                $prospectusUrl = "../student/default.html"; 
                break;
        }
    }
} else {
    header("Location: ../login.php");
    exit();
}

// Handle success message if it exists
$message = "";
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']); // Clear the message after displaying
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Page</title>
    <link rel="stylesheet" href="../css/home.css">
    <style>
        #contentDiv {
            margin-top: 80px; /* Match this to the height of the navigation bar */
            padding: 20px; /* Optional padding for better content spacing */
        }
        .success-message {
            color: green;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <nav class="menu-bar">
        <div class="GLC_Logo">
        </div>
        <br><br>
        <a href="../student/news-and-announcements.php" class="menu-button">Announcements</a>
        <a href="../php/student-profile.php" class="menu-button">Profile</a>
        <a href="<?php echo $prospectusUrl; ?>" class="menu-button">Prospectus</a>
        <a href="../php/logout.php" class="menu-button">Logout</a>
        
        <p>Waling-waling Street, Barangay 177, Camarin, Caloocan City.<br>Tel : 961-5836<br>Email: <a href="mailto:goldenlink2002@gmail.com" class="email-link">goldenlink2002@gmail.com</a></p>
    </nav>

    <div id="contentDiv">
        <?php if ($message): ?>
            <div class="success-message"><?php echo $message; ?></div>
        <?php endif; ?>
    </div>

    <script>
        // Load the default page on window load
        window.onload = () => {
            loadPage('news-and-announcements.php');
        };
    </script>
</body>
</html>
