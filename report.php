<?php

session_start();

include 'connect.php';
include 'prepared_statements.php';

$userID = $_SESSION['UserID']; // fallback to 2 for testing

if ($_SESSION['SystemRole'] !== 'Admin') {
    header("Location: login.php");
    exit();
};

// Fixed: all column names changed to snake_case to match DB schema

$sql = "
    SELECT
        s.student_id            AS student_id,
        s.student_name          AS student_name,
        i.intern_id             AS intern_id,
        i.supervisor_id         AS supervisor_id,
        i.lecturer_id           AS lecturer_id,
        a1.full_name            AS lecturer_name,
        a2.full_name            AS supervisor_name,
        i.report_status         AS report_status,
        Ar1.report_id           AS lecturer_report_id,
        Ar1.task_score          AS lecturer_task_score,
        Ar1.safety_score        AS lecturer_safety_score,
        Ar1.theory_score        AS lecturer_theory_score,
        Ar1.present_score       AS lecturer_present_score,
        Ar1.clarity_score       AS lecturer_clarity_score,
        Ar1.learning_score      AS lecturer_learning_score,
        Ar1.proj_mgmt_score     AS lecturer_proj_mgmt_score,
        Ar1.time_mgmt_score     AS lecturer_time_mgmt_score,
        Ar1.comment             AS lecturer_comment,
        Ar2.report_id           AS supervisor_report_id,
        Ar2.task_score          AS supervisor_task_score,
        Ar2.safety_score        AS supervisor_safety_score,
        Ar2.theory_score        AS supervisor_theory_score,
        Ar2.present_score       AS supervisor_present_score,
        Ar2.clarity_score       AS supervisor_clarity_score,
        Ar2.learning_score      AS supervisor_learning_score,
        Ar2.proj_mgmt_score     AS supervisor_proj_mgmt_score,
        Ar2.time_mgmt_score     AS supervisor_time_mgmt_score,
        Ar2.comment             AS supervisor_comment
    FROM internship i
    JOIN internship_report Ar1 
        ON i.intern_id = Ar1.intern_id
        AND i.lecturer_id = Ar1.assessor_id
    JOIN internship_report Ar2 
        ON i.intern_id = Ar2.intern_id
        AND i.supervisor_id = Ar2.assessor_id
    JOIN student   s  ON s.student_id = i.student_id  
    JOIN assessor a1 ON i.lecturer_id = a1.user_id
    JOIN assessor a2 ON i.supervisor_id = a2.user_id
    ORDER BY i.intern_id ASC
";
$result = executePreparedStatement($sql, []);

// Calculations for summary cards
$totalStudents = $result->num_rows;
$TotalAssessors = 0;
$pending = 0;
$marksSubmitted = 0;

$rows = $result->fetch_all(MYSQLI_ASSOC);
//looping through the report_status column to find "Complete" so we can perform totaling on pending and resultDone variables
foreach ($rows as $row) {
    if ($row['report_status'] === 'Complete') {
        $marksSubmitted++;
    } else {
        $pending++;
    }
}
$final = [];

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report</title>
    <link rel="stylesheet" href="style/results.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<!-- NAVBAR -->
    <header>
        <section class="navimg_logo">
            <a href="#"><img src="assets/nottinghamLogoWhite.png" alt="Nottingham Logo"></a>
        </section>
        <nav class="headul">
            <ul>
                <?php if ($_SESSION['SystemRole'] === 'Admin'): ?><!-- added an if statement so that admin pages are only visabl admin -->
                    <li class="list"><a href="dashboard.php"><i class="fa-solid fa-house"></i> Dashboard</a></li>
                <?php endif; ?>
                <?php if ($_SESSION['SystemRole'] === 'Assessor'): ?><!-- added an if statement so that assessor pages are only visabl assesor-->
                    <li class="list"><a href="myStudents.php"><i class="fa-solid fa-chalkboard-user"></i> Assessor</a></li>
                <?php endif; ?>
                <li class="list"><a href="marking.php"><i class="fa-solid fa-chart-bar"></i> Marking</a></li>
                <?php if ($_SESSION['SystemRole'] === 'Admin'): ?>
                    <li class="list"><a href="report.php"><i class="fa-solid fa-chalkboard-user"></i> Report</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <section class="navbar_loginUser">
            <article> <!-- This show the username and admin/assessor on the navbar -->
                <p><?php echo htmlspecialchars($_SESSION['Username'] ?? 'admin'); ?></p>
                <p><?php echo htmlspecialchars($_SESSION['SystemRole'] ?? 'admin'); ?></p>
            </article>
            <a href="logout.php">Logout</a>
        </section>
    </header>

<main>

    <section>
            <article class="Dashboard_msg">
                <h1>Internship Reports</h1>
                <p>All internship reports in the database, sorted by internship</p>
            </article>

            <article class="mainDash">
                <div class="totalStudents">
                    <span class="StudentIcon"><i class="fa-solid fa-user-graduate"></i></span>
                    <span> <!-- outputing total students nums -->
                        <h2><?php echo $totalStudents; ?></h2>
                        <p>Internships</p>
                    </span>
                </div>
                <div class="marks_submitted">
                    <span class="marksIcon"><i class="fa-solid fa-circle-check"></i></span>
                    <span><!-- outputing total marks submmited -->
                        <h2><?php echo $marksSubmitted; ?></h2>
                        <p>Reports Finalised</p>
                    </span>
                </div>
                <div class="pending">
                    <span class="pendingIcon"><i class="fa-regular fa-hourglass-half"></i></span>
                    <span> <!-- outputing total pending nums -->
                        <h2><?php echo $pending; ?></h2>
                        <p>Reports In Progress</p>
                    </span>
                </div>
            </article>
        </section>

    <section class="SearchbarDash">
        <div>
            <select class="searchBy" id="searchType">
                <option value="intern">Search by Intern ID</option>
                <option value="lecturer">Search by Lecturer ID</option>
                <option value="supervisor">Search by Supervisor ID</option>
                <option value="attributes">Search by Attributes</option>
            </select>

            <input type="search" class="search" placeholder="🔍 Search..." id="searchStudent">
            
            <select class="statusSearch" id="statusFilter">
                <option value="">All Status</option>
                <option value="Drafting">Drafting</option>
                <option value="In Progress">In Progress</option>
                <option value="Suspended">Suspended</option>
                <option value="Finalisation">Finalisation</option>
                <option value="Complete">Complete</option>
            </select>
        </div>
    </section>

    <section class="data">
        <article class="realData">

            <table>
                <thead>
                    <tr>
                        <th>Intern ID</th>
                        <th>Intern Name</th>
                        <th>Lecturer ID</th>
                        <th>Lecturer Name</th>
                        <th>Lecturer Marks</th>
                        <th>Lecturer Comments</th>
                        <th>Supervisor ID</th>
                        <th>Supervisor Name</th>
                        <th>Supervisor Marks</th>
                        <th>Supervisor Comments</th>
                        <th>Avg Mark</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rows)): ?>
                            <tr> <!-- If the row are empty, then output message -->
                                <td colspan="7" style="text-align:center;">No students found.</td>
                            </tr>
                        <?php else: ?> <!-- if not, loop through each rows of data and output each attribute values -->
                            <?php foreach ($rows as $row): ?>
                                <tr data-status="<?php echo htmlspecialchars($row['report_status'] ?? ''); ?>">
                                    <td><?php echo htmlspecialchars($row['intern_id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['lecturer_id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['lecturer_name']); ?></td>
                                    <td><?php 
                                    $weighted_score =
                                    $row['lecturer_task_score']*0.1 + $row['lecturer_safety_score']*0.1 + 
                                    $row['lecturer_theory_score']*0.1 + $row['lecturer_present_score']*0.15 + 
                                    $row['lecturer_clarity_score']*0.1 + $row['lecturer_learning_score']*0.15 + 
                                    $row['lecturer_proj_mgmt_score']*0.15 + $row['lecturer_time_mgmt_score']*0.15;
                                    echo htmlspecialchars(number_format($weighted_score, 2));
                                    ?></td>
                                    <td><?php echo htmlspecialchars($row['lecturer_comment'] ?? ''); ?></td>

                                    <td><?php echo htmlspecialchars($row['supervisor_id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['supervisor_name']); ?></td>
                                    <td><?php 
                                    $weighted_score =
                                    $row['supervisor_task_score']*0.1 + $row['supervisor_safety_score']*0.1 + 
                                    $row['supervisor_theory_score']*0.1 + $row['supervisor_present_score']*0.15 + 
                                    $row['supervisor_clarity_score']*0.1 + $row['supervisor_learning_score']*0.15 + 
                                    $row['supervisor_proj_mgmt_score']*0.15 + $row['supervisor_time_mgmt_score']*0.15;
                                    echo htmlspecialchars(number_format($weighted_score, 2));
                                    ?></td>
                                    <td><?php echo htmlspecialchars($row['supervisor_comment'] ?? ''); ?></td>
                                    <td><?php 
                                    $weighted_score =
                                    $row['lecturer_task_score']*0.1 + $row['lecturer_safety_score']*0.1 + 
                                    $row['lecturer_theory_score']*0.1 + $row['lecturer_present_score']*0.15 + 
                                    $row['lecturer_clarity_score']*0.1 + $row['lecturer_learning_score']*0.15 + 
                                    $row['lecturer_proj_mgmt_score']*0.15 + $row['lecturer_time_mgmt_score']*0.15 + 
                                    $row['supervisor_task_score']*0.1 + $row['supervisor_safety_score']*0.1 + 
                                    $row['supervisor_theory_score']*0.1 + $row['supervisor_present_score']*0.15 + 
                                    $row['supervisor_clarity_score']*0.1 + $row['supervisor_learning_score']*0.15 + 
                                    $row['supervisor_proj_mgmt_score']*0.15 + $row['supervisor_time_mgmt_score']*0.15;
                                    $weighted_score /= 2;
                                    echo htmlspecialchars(number_format($weighted_score, 2));
                                    ?></td>
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
<script src="javascript/SearchReport.js"></script>

</body>
</html>
