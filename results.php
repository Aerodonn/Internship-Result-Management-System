<?php

session_start();

$host     = "localhost";
$dbname   = "internship_management_system";
$username = "root";
$password = "root";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$lecturerID = $_SESSION['UserID'] ?? 2; // fallback to 2 for testing

// Fixed: all column names changed to snake_case to match DB schema
$sql = "
    SELECT
        s.student_id            AS student_id,
        s.student_name          AS student_name,
        a1.full_name            AS lecturer_name,
        a2.full_name            AS supervisor_name,
        i.report_status         AS report_status,
        i.intern_id             AS intern_id,
        Ar.task_score           AS task_score,
        Ar.safety_score         AS safety_score,
        Ar.theory_score         AS theory_score,
        Ar.present_score        AS present_score,
        Ar.clarity_score        AS clarity_score,
        Ar.learning_score       AS learning_score,
        Ar.proj_mgmt_score      AS proj_mgmt_score,
        Ar.time_mgmt_score      AS time_mgmt_score,
        Ar.comment              AS comment
    FROM internship i
    JOIN student   s  ON i.student_id    = s.student_id
    JOIN assessor  a1 ON i.lecturer_id   = a1.user_id
    JOIN assessor  a2 ON i.supervisor_id = a2.user_id
    JOIN internship_report Ar ON i.intern_id = Ar.intern_id
    WHERE i.lecturer_id = ?
    ORDER BY s.student_name ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $lecturerID);
$stmt->execute();
$result = $stmt->get_result();

// Calculations for summary cards
$totalStudents = $result->num_rows;
$TotalAssessors = 0;
$pending = 0;
$resultDone = 0;

$total_LecturerScore = 0;
$total_SupervisorScore = 0;
$total_AllScore = 0;

// $weights = [
//     'task_score' => 10,
//     'safety_score' => 10,
//     'theory_score' => 10,
//     'present_score' => 15,
//     'clarity_score' => 10,
//     'learning_score' => 15,
//     'proj_mgmt_score' => 15,
//     'time_mgmt_score' => 15,
// ];
//initalizing
$final = [];

//loop
foreach ($rows as $row) {
    $id = $row['intern_id'];

    //if the intern_id is the first time then the intern_id get save in final
    if (!isset($final[$id])) {
        $final[$id] = 0;
    }
    //adds intern_id's total_scores together
    $final[$id] += $row['total_score'];
}






$rows = $result->fetch_all(MYSQLI_ASSOC);

foreach ($rows as $row) {
    if ($row['report_status'] === 'Complete') {
        $resultDone++;
    } else {
        $pending++;
    }
}

$stmt->close();
$conn->close();
?>



<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <title>Page Title</title>
    <link rel='stylesheet' href='style\results.css'>
</head>
<body>
    <header>
        <section class="navimg_logo">
            <a href="#"><img src="assets\nottinghamLogoWhite.png"></a>
        </section>

        <nav class="headul">
            <ul>
                <li class="list"><a href="dashboard.php"><i class="fa-solid fa-house"></i> Dashboard</a></li>
                <li class="list"><a href="#"><i class="fa-solid fa-user-shield"></i> Admin</a></li>
                <li class="list"><a href="myStudents.php"><i class="fa-solid fa-chalkboard-user"></i> Assessor</a></li>
                <li class="list"><a href="#"><i class="fa-solid fa-chart-bar"></i> Result</a></li>
            </ul>
        </nav>
        <section class="navbar_loginUser">
            <article>
                <p>Username</p>
                <p>admin</p>
            </article>
            <a>Logout</a>
        </section>
    </header>
    <main>
        <section>
            <article class="Dashboard_msg">
                <h1>Internship results</h1>
                <p>Final grades for all students</p>
            </article>
            <article>
                <form class="resultSearchBar">
                    <p>Input stuff</p>
                    <p>Drop down box</p>
                    <p>Status drop down box</p>
                </form>
            </article>
        </section>
        <section class="data">
            <article class="realData">
                <table>
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Lecturer</th>
                            <th>Lecturer Score</th>
                            <th>Supervisor</th>
                            <th>Supervisor Score</th>
                            <th>Total</th>
                            <th>Grade</th>
                            <th>Comments</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($rows)): ?>
                            <tr>
                                <td colspan="7" style="text-align:center;">No students assigned to you yet.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($rows as $row): ?>
                                <tr> <!-- This basically makes a loop where it iterate through each data and output them  -->
                                    <td><?php echo htmlspecialchars($row['student_id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['lecturer_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['supervisor_name']); ?></td>
                                    
                                    
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </article>
            <!-- <article class="graph">Test</article> -->
        </section>
    </main>
    <footer>
        <section class="footer">
            <p>© 2026 University of Nottingham Malaysia — Internship Result Management System — Group 39</p>
        </section>
    </footer>
    
</body>
</html>