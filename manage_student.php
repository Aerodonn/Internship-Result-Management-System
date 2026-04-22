<?php

$host     = "localhost";
$dbname   = "internship_management_system";
$username = "root";
$password = "root";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();
$lecturerID = $_SESSION['UserID'] ?? 2; // fallback to 1 for testing

//fetching all the data needed for the table
$sql = "
    SELECT
        s.StudentID      AS student_id,
        s.StudentName           AS student_name,
        s.Programme             AS programme,
        a1.FullName             AS lecturer_name,
        a2.FullName             AS supervisor_name,
        i.InternshipCompany     AS company,
        i.StartDate             AS start_date,
        i.EndDate               AS end_date,
        i.ReportStatus          AS report_status,
        i.InternID              AS intern_id
    FROM internship i
    JOIN student   s  ON i.StudentID    = s.StudentID
    JOIN assessor  a1 ON i.LecturerID   = a1.UserID
    JOIN assessor  a2 ON i.SupervisorID = a2.UserID
    WHERE i.LecturerID = ?
    ORDER BY s.StudentName ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $lecturerID);
$stmt->execute();
$result = $stmt->get_result();

//doing some calculations to get totalStudent, marksSubmmited and pending amounts.
$totalStudents   = $result->num_rows;
$marksSubmitted  = 0;
$pending         = 0;

$rows = $result->fetch_all(MYSQLI_ASSOC); // buffer all rows

foreach ($rows as $row) {
    if ($row['report_status'] === 'Complete') {
        $marksSubmitted++;
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
    <title>My Students</title>
    <link rel='stylesheet' href='style/results.css'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

    <header>
        <section class="navimg_logo">
            <a href="#"><img src="assets/nottinghamLogoWhite.png" alt="Nottingham Logo"></a>
        </section>
        <nav class="headul">
            <ul>
                <li class="list"><a href="dashboard.php"><i class="fa-solid fa-house"></i> Dashboard</a></li>
                <li class="list"><a href="#"><i class="fa-solid fa-user-shield"></i> Admin</a></li>
                <li class="list"><a href="#"><i class="fa-solid fa-chalkboard-user"></i> Assessor</a></li>
                <li class="list"><a href="results.php"><i class="fa-solid fa-chart-bar"></i> Result</a></li>
            </ul>
        </nav>
        <section class="navbar_loginUser">
            <article>
                <p><?php echo htmlspecialchars($_SESSION['Username'] ?? 'admin'); ?></p>
                <p><?php echo htmlspecialchars($_SESSION['SystemRole'] ?? 'admin'); ?></p>
            </article>
            <a href="logout.php">Logout</a>
        </section>
    </header>

    <main>
        <section>
            <article class="Dashboard_msg">
                <h1>My Students</h1>
                <p>Students assigned to you for this internship cycle.</p>
            </article>

            <article class="mainDash">
                <div class="totalStudents">
                    <span class="StudentIcon">
                        <i class="fa-solid fa-user-graduate"></i>
                    </span>
                    <span>
                        <h2><?php echo $totalStudents; ?></h2>
                        <p>Students assigned</p>
                    </span>
                </div>
                <div class="marks_submitted">
                    <span class="marksIcon">
                        <i class="fa-solid fa-circle-check"></i>
                    </span>
                    <span>
                        <h2><?php echo $marksSubmitted; ?></h2>
                        <p>Marks Submitted</p>
                    </span>
                </div>
                <div class="pending">
                    <span class="pendingIcon">
                        <i class="fa-regular fa-hourglass-half"></i>
                    </span>
                    <span>
                        <h2><?php echo $pending; ?></h2>
                        <p>Pending</p>
                    </span>
                </div>
            </article>
        </section>

        <section class="data">
            <article class="realData">
                <table>
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Programme</th>
                            <th>Assessor</th>
                            <th>Company</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($rows)): ?>
                            <tr>
                                <td colspan="7" style="text-align:center;">No students assigned to you yet.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($rows as $row): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['student_id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['programme']); ?></td>
                                    <!-- Shows the Industry Supervisor assigned to this student -->
                                    <td><?php echo htmlspecialchars($row['supervisor_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['company']); ?></td>
                                    <td>
                                        <?php
                                        // Map ReportStatus enum to a CSS class for badge styling
                                        $statusClass = match($row['report_status']) {
                                            'Complete'     => 'status-complete',
                                            'In Progress'  => 'status-inprogress',
                                            'Drafting'     => 'status-drafting',
                                            'Suspended'    => 'status-suspended',
                                            'Finalisation' => 'status-finalisation',
                                            default        => ''
                                        };
                                        ?>
                                        <span class="status-badge <?php echo $statusClass; ?>">
                                            <?php echo htmlspecialchars($row['report_status'] ?? 'N/A'); ?>
                                        </span>
                                    </td>
                    
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </article>
        </section>
    </main>

    <footer>
        <section class="footer">
            <p>© 2026 University of Nottingham Malaysia — Internship Result Management System — Group 39</p>
        </section>
    </footer>

</body>
</html>