<?php
session_start();
// only allows if current user session's systemRole is admin else it will log you out.
if (!isset($_SESSION['SystemRole']) || $_SESSION['SystemRole'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

include 'connect.php';
include 'prepared_statements.php';

//selecting the table columns/attributes needed for this page.
$sql = "
    SELECT
        s.student_id         AS student_id,
        s.student_name       AS student_name,
        a1.full_name         AS lecturer_name,
        a2.full_name         AS supervisor_name,
        i.internship_company AS company,
        i.report_status      AS report_status,
        i.intern_id          AS intern_id,
        a1.assessor_type     AS assessor_type
    FROM internship i
    JOIN student  s  ON i.student_id  = s.student_id
    JOIN assessor a1 ON i.lecturer_id = a1.user_id
    JOIN assessor a2 ON i.supervisor_id = a2.user_id
    ORDER BY s.student_name ASC
";

$result = executePreparedStatement($sql, []);
$rows = $result->fetch_all(MYSQLI_ASSOC);

//initalizing 
$totalStudents = $result->num_rows;
$pending = 0;
$resultDone = 0;

//looping through the report_status column to find "Complete" so we can perform totaling on pending and resultDone variables
foreach ($rows as $row) {
    if ($row['report_status'] === 'Complete') {
        $resultDone++;
    } else {
        $pending++;
    }
}
//assessing the assessor table data to get the total count of assessor
$assessor_sql = "SELECT COUNT(*) AS total_count FROM assessor";
$assessor_result = executePreparedStatement($assessor_sql, []);
$assessor_row = $assessor_result->fetch_assoc();

$totalAssessor = $assessor_row['total_count'];

?>

<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <title>Page Title</title>
    <link rel='stylesheet' href='style\results.css'>
     <!-- i used Awesome Font website's fonts so i added a link to it to assess the font. -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">    
</head>
<body>
    <header>
        <section class="navimg_logo">
            <a href="#"><img src="assets\nottinghamLogoWhite.png"></a>
            <!-- <h2>Internship Portal</h2> -->
        </section>

        <nav class="headul">
            <ul>
                <?php if ($_SESSION['SystemRole'] === 'Admin'): ?> <!-- added an if statement so that admin pages are only visabl admin -->
                    <li class="list"><a href="dashboard.php"><i class="fa-solid fa-house"></i> Dashboard</a></li>
                <?php endif; ?>
                <?php if ($_SESSION['SystemRole'] === 'Assessor'): ?><!-- added an if statement so that assessor pages are only visabl assesor-->
                    <li class="list"><a href="myStudents.php"><i class="fa-solid fa-chalkboard-user"></i> Assessor</a></li>
                <?php endif; ?>
                <li class="list"><a href="results.php"><i class="fa-solid fa-chart-bar"></i> Result</a></li>
            </ul>
        </nav>
        <section class="navbar_loginUser">
            <article>   <!-- This show the username and admin/assessor on the navbar -->
                <p><?php echo htmlspecialchars($_SESSION['Username'] ?? 'admin'); ?></p>
                <p><?php echo htmlspecialchars($_SESSION['SystemRole'] ?? 'admin'); ?></p>
            </article>
            <a href="logout.php">Logout</a>
        </section>
    </header>
    <main>
        <section>
            <article class="Dashboard_msg">
                <h1>Dashboard</h1>      <!-- This show the username in the paragraph section on top of the page.-->
                <p>Welcome back Mr <span><?php echo htmlspecialchars($_SESSION['Username'] ?? 'admin'); ?></span></p>
            </article>
            <article class="mainDash">
                <div class="totalStudents">
                    <span class="StudentIcon">
                        <i class="fa-solid fa-user-graduate"></i>
                    </span>
                    <span>  <!-- outputing totalStudent nums -->
                        <h2><?php echo $totalStudents; ?></h2>
                        <p>Total Students</p>
                    </span>
                </div>
                <div class="totalAssessors">
                    <span class="AssessorIcon">
                        <i class="fa-solid fa-user-tie"></i>
                    </span>
                    <span>   <!-- outputing totalAssessor nums -->
                        <h2><?php echo $totalAssessor; ?></h2>
                        <p>Total Assessors</p>
                    </span>
                </div>
                <div class="pending">
                    <span class="pendingIcon">
                        <i class="fa-regular fa-hourglass-half"></i>
                    </span>
                    <span>  <!-- outputing total pending nums -->
                        <h2><?php echo $pending; ?></h2>
                        <p>Pending marks</p>
                    </span>
                </div>
                <div class="result">
                    <span class="resultIcon">
                        <i class="fa-solid fa-square-poll-horizontal"></i>  
                    </span>
                    <span>  <!-- outputing total result done -->
                        <h2><?php echo $resultDone; ?></h2>
                        <p>Result release</p>
                    </span>
                </div>
            </article>
        </section>
        <section class="SearchbarDash">
            <div>
                <input type="search" class="search" placeholder="🔍 Search students…" id="searchStudent">
                <select class="statusSearch" id="statusFilter">
                    <option value>All Status</option>
                    <option value="Drafting">Drafting</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Suspended">Suspended</option>
                    <option value="Finalisation">Finalisation</option>
                    <option value="Complete">Complete</option>
                </select>
            </div>
            <div>
                <article class="sideBar">
                <div class="quickActionGrid">
                    <a href="manage_student.php"><i class="fa-solid fa-user-graduate"></i> Manage Students</a>
                    <a href="manage_assessor.php"><i class="fa-solid fa-user-tie"></i></i>Manage Assessor</a>
                    <a href="assign_internship.php"><i class="fa-solid fa-user-shield"></i> Assign Internship</a>
                </div>
            </article>
            </div>
        </section>
        <section class="data">
            <article class="realData">
                <table>
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Lecturer</th>
                            <th>Supervisor</th>
                            <th>Company</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>  <!-- If the row are empty, then output message -->
                        <?php if (empty($rows)): ?>
                            <tr>
                                <td colspan="6" style="text-align:center;">No students assigned to you yet.</td>
                            </tr>
                        <?php else: ?> <!-- if not, loop through each rows of data and output each attribute values -->
                            <?php foreach ($rows as $row): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['student_id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['lecturer_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['supervisor_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['company']); ?></td>
                                    <td> <!-- Changing the class depending on the status so the program CSS changes accordingly -->
                                        <?php
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
<script src="javascript/SearchBarDashboard.js"></script></body>
</html> 


