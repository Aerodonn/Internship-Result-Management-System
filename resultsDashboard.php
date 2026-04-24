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
        Ar.assessor_id          AS assessor_id,
        a.full_name             AS assessor_name,
        i.report_status         AS report_status,
        Ar.report_id            AS report_id,
        Ar.task_score           AS task_score,
        Ar.safety_score         AS safety_score,
        Ar.theory_score         AS theory_score,
        Ar.present_score        AS present_score,
        Ar.clarity_score        AS clarity_score,
        Ar.learning_score       AS learning_score,
        Ar.proj_mgmt_score      AS proj_mgmt_score,
        Ar.time_mgmt_score      AS time_mgmt_score,
        Ar.comment              AS comment,
        Ar.total_marks          AS total_marks
    FROM internship_report Ar
    JOIN internship i ON i.intern_id = Ar.intern_id
    JOIN student   s  ON s.student_id = i.student_id  
    JOIN assessor a ON a.user_id = Ar.assessor_id
    ORDER BY s.student_name ASC
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
    <title>View Results</title>
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
                <li class="list"><a href="results.php"><i class="fa-solid fa-chart-bar"></i> Result</a></li>
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
                <h1>Assigned Students</h1>
                <p>All students assigned to you.</p>
            </article>

            <article class="mainDash">
                <div class="totalStudents">
                    <span class="StudentIcon"><i class="fa-solid fa-user-graduate"></i></span>
                    <span> <!-- outputing total students nums -->
                        <h2><?php echo $totalStudents; ?></h2>
                        <p>Students assigned</p>
                    </span>
                </div>
                <div class="marks_submitted">
                    <span class="marksIcon"><i class="fa-solid fa-circle-check"></i></span>
                    <span><!-- outputing total marks submmited -->
                        <h2><?php echo $marksSubmitted; ?></h2>
                        <p>Marks Submitted</p>
                    </span>
                </div>
                <div class="pending">
                    <span class="pendingIcon"><i class="fa-regular fa-hourglass-half"></i></span>
                    <span> <!-- outputing total pending nums -->
                        <h2><?php echo $pending; ?></h2>
                        <p>Pending</p>
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
        </section>

    <section class="data">
        <article class="realData">

            <table>
                <thead>
                    <tr>
                        <th>Report ID</th>
                        <th>Intern ID</th>
                        <th>Intern Name</th>
                        <th>Assessor ID</th>
                        <th>Assessor Name</th>
                        <th>Marks</th>
                        <th>Comments</th>
                        <th>Mark</th>
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
                                    <td><?php echo htmlspecialchars($row['report_id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['intern_id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['assessor_id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['assessor_name']); ?></td>
                                    <td>
                                        <?php //this put PHP values into javascript function
                                        $statusClass = match($row['report_status']) {
                                            'Complete'     => 'status-complete',
                                            'In Progress'  => 'status-inprogress',
                                            'Drafting'     => 'status-drafting',
                                            'Suspended'    => 'status-suspended',
                                            'Finalisation' => 'status-finalisation',
                                            default        => ''
                                        };
                                        ?>
                                    </td>
                                    <td><?php 
                                    $weighted_score =
                                    $row['task_score']*0.1 + $row['safety_score']*0.1 + $row['theory_score']*0.1 + 
                                    $row['present_score']*0.15 + $row['clarity_score']*0.1 + $row['learning_score']*0.15 + 
                                    $row['proj_mgmt_score']*0.15 + $row['time_mgmt_score']*0.15;
                                    echo htmlspecialchars(number_format($weighted_score, 2));
                                    ?></td>
                                    <td><?php echo htmlspecialchars($row['comment']); ?></td>
                                    <td>
                                        <!-- mark button -->
                                        <button class="btn-add" onclick="openAddForm()">
                                            <i class="fa-solid fa-marker"></i> Mark
                                        </button>
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
        <p>© 2026 University of Nottingham Malaysia — Internship System</p>
    </section>
</footer>
<script src="javascript/SearchResults.js"></script></body>

</body>
</html>
