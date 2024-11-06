<!DOCTYPE html>
<html lang="en" class="html-iframes">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Golden Link College Prospectus</title>
  <link rel="stylesheet" href="../css/home.css?v=1.0">
  <style>
    body {
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .Prosp {
      margin-top: 80px;
      margin-bottom: 20px;
      padding-top: 20px;
      padding-bottom: 30px;
      border-radius: 50px;
      flex-direction: column;
      display: flex;
      justify-content: center;
      align-items: center;
      background-color: hsla(235, 37%, 7%, 0.60);
      width: 80%;
    }
    table, td {
      border: 0.5px solid #f5f5f56a;
      color: #f5f5f5;
      font-size: 120%;
    }
    table th {
      color: #fada5e;
    }
    table td:nth-child(3),
    td:nth-child(4) {
      text-align: center;
    }
    input[type="checkbox"] {
      transform: scale(1.5);
    }
    p {
      color: #f5f5f5;
    }
    h2 {
      color: #fada5e;
      font-size: 40px;
    }
    button {
      margin-top: 20px;
      padding: 10px 20px;
      font-size: 16px;
      cursor: pointer;
    }
  </style>
</head>

<body>
  <div class="Prosp">
    <h2>BSIT Prospectus</h2>
    <br>

    <form method="POST" action="save_prospectus.php">
      <table style="width: 80%">
        <tr>
          <th>Course Code</th>
          <th>Course Name</th>
          <th>Units</th>
          <th>Taken</th>
        </tr>
        <?php

        if (isset($_GET['message'])) {
    $message = htmlspecialchars($_GET['message']); // Sanitize the message to prevent XSS
    echo "<script>alert('$message');</script>";
}
        session_start(); // Ensure session is started
        // Include your database connection code
        $host = 'localhost';
        $db = 'webglc_database';
        $user = 'root'; // Default MySQL username in XAMPP
        $pass = '';     // Default MySQL password (usually empty in XAMPP)

        try {
            $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Could not connect to the database: " . $e->getMessage());
        }

        $userId = $_SESSION['id']; // Use the session ID to identify the user

        // Fetch the currently selected courses from the database for the logged-in user
        $query = $pdo->prepare("SELECT course_code FROM prospectus_status WHERE user_id = :user_id AND taken = 1");
        $query->execute([':user_id' => $userId]);
        $selectedCourses = $query->fetchAll(PDO::FETCH_COLUMN);

        // Define your courses in an array for easy iteration
        $courses = [
            ['code' => 'GE 6', 'name' => 'Art Appreciation', 'units' => 3],
            ['code' => 'Fil 1', 'name' => 'Komunikasyon sa Akademikong Pilipino', 'units' => 3],
            ['code' => 'Eng B', 'name' => 'Developmental Reading', 'units' => 3],
            ['code' => 'MTH A', 'name' => 'Mathematics Enhancement', 'units' => 3],
            ['code' => 'PE 1', 'name' => 'Physical Fitness/Gymnastics', 'units' => 2],
            ['code' => 'NSTP 1', 'name' => 'National Service Training Program 1', 'units' => 3],
            ['code' => 'GE 4', 'name' => 'Purposive Communication', 'units' => 3],
            ['code' => 'StudDev', 'name' => 'Student Development (Circle Time)', 'units' => 0],
            ['code' => 'Eng A', 'name' => 'Foundation English', 'units' => 3],
            ['code' => 'GE 1', 'name' => 'Understanding the Self', 'units' => 3],
            ['code' => 'CC 100', 'name' => 'Introduction to Computing', 'units' => 3],
            ['code' => 'Fil 3', 'name' => 'Panitikan ng Pilipinas', 'units' => 3],
            ['code' => 'GE 7', 'name' => 'Science, Technology, and Society', 'units' => 3],
            ['code' => 'Philo 3', 'name' => 'Comparative Religion', 'units' => 3],
            ['code' => 'GE 3', 'name' => 'Mathematics in the Modern World', 'units' => 3],
            ['code' => 'GE 5', 'name' => 'The Contemporary World', 'units' => 3],
            ['code' => 'PE 3', 'name' => 'Dual Sports', 'units' => 3],
            ['code' => 'CC102', 'name' => 'Computer Programming 2', 'units' => 3],
            ['code' => 'HC1101', 'name' => 'Introduction to Human Computer Interaction', 'units' => 3],
            ['code' => 'GE Elec 2', 'name' => 'The Entrepreneurial Mind', 'units' => 3],
            ['code' => 'IPT 101', 'name' => 'Integrative Programming and Technologies', 'units' => 3],
            ['code' => 'PF 101', 'name' => 'Object Oriented Programming', 'units' => 3],
            ['code' => 'SP 101', 'name' => 'Social and Professional Issues', 'units' => 3],
            ['code' => 'IM 101', 'name' => 'Advanced Database Systems', 'units' => 3],
            ['code' => 'MS 102', 'name' => 'Quantitative Methods; including Modelling and Simulation', 'units' => 3],
            ['code' => 'NET 101', 'name' => 'Networking 1', 'units' => 3],
            ['code' => 'SD 101', 'name' => 'Software Development', 'units' => 3],
            ['code' => 'PROJ 101', 'name' => 'Software Engineering', 'units' => 3],
            ['code' => 'IT 101', 'name' => 'Information Technology Fundamentals', 'units' => 3],
            ['code' => 'IT 102', 'name' => 'Web Development', 'units' => 3],
            ['code' => 'IT 201', 'name' => 'Data Structures and Algorithms', 'units' => 3],
            ['code' => 'IT 202', 'name' => 'Operating Systems', 'units' => 3],
            ['code' => 'IT 203', 'name' => 'Systems Analysis and Design', 'units' => 3],
            ['code' => 'IT 204', 'name' => 'Mobile Application Development', 'units' => 3],
        ];

        foreach ($courses as $course) {
            $isChecked = in_array($course['code'], $selectedCourses) ? 'checked' : '';
            echo '<tr>';
            echo '<td>' . htmlspecialchars($course['code']) . '</td>';
            echo '<td>' . htmlspecialchars($course['name']) . '</td>';
            echo '<td>' . htmlspecialchars($course['units']) . '</td>';
            echo '<td><input type="checkbox" name="course_code[' . htmlspecialchars($course['code']) . ']" ' . $isChecked . '></td>';
            echo '</tr>';
        }
        ?>
      </table>
      <button type="submit">Save Prospectus</button>
    </form>
  </div>
  <a href="student.php" class="back-button">Back</a>
</body>

</html>
