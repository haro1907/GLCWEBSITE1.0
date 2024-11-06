<?php
session_start();
$host = 'localhost';
$db = 'webglc_database';  // Ensure this matches the database name you created
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Debugging output
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";

    // Loop through each set of inputs
    $totalEntries = count($_POST['id']);
    for ($i = 0; $i < $totalEntries; $i++) {
        $id = $_POST['id'][$i];
        $email = $_POST['email'][$i];
        $role = $_POST['role'][$i];
        $program = $_POST['program'][$i];

        // Set default values
        $firstname = " ";
        $lastname = " ";
        $middlename = " ";
        $username = " ";
        $contact = "00000000000"; // Placeholder contact number
        $dob = " "; // Placeholder date of birth
        $defaultPassword = "GLC123";

        // Hash the default password
        $hashedPassword = password_hash($defaultPassword, PASSWORD_DEFAULT);

        // Check if ID already exists
        $id_check = $pdo->prepare("SELECT COUNT(*) FROM glc_users WHERE id = :id");
        $id_check->execute([':id' => $id]);
        if ($id_check->fetchColumn() > 0) {
            echo "<script>alert('ID $id already exists.'); window.history.back();</script>";
            exit();
        }

        // Check if email matches domain requirement
        if (!preg_match("/@goldenlink\.ph$/", $email)) {
            echo "<script>alert('Email must end with @goldenlink.ph'); window.history.back();</script>";
            exit();
        }

        // Insert into database with default values for certain fields
        $sql = "INSERT INTO glc_users (id, username, email, hashedPassword, lastname, firstname, middlename, dob, contact, role, program) 
                VALUES (:id, :username, :email, :password, :lastname, :firstname, :middlename, :dob, :contact, :role, :program)";
        $stmt = $pdo->prepare($sql);

        // Debugging line to check the program value
        echo "Registering user with Program: $program\n"; 

        if (!$stmt->execute([
            ':id' => $id,
            ':username' => $username,
            ':email' => $email,
            ':password' => $hashedPassword,
            ':lastname' => $lastname,
            ':firstname' => $firstname,
            ':middlename' => $middlename,
            ':dob' => $dob,
            ':contact' => $contact,
            ':role' => $role,
            ':program' => $program
        ])) {
            $errorInfo = $stmt->errorInfo();
            echo "<script>alert('Registration failed for ID $id. Error: {$errorInfo[2]}'); window.history.back();</script>";
            exit();
        }
    }
    header("Location: ../register.html");
    exit();
}
?>
