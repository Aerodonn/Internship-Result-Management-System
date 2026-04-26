<?php

session_start();

include 'connect.php';
include 'prepared_statements.php';
include 'action_marking.php';

$userID = $_SESSION['UserID']; // fallback to 2 for testing

if ($_SESSION['SystemRole'] == 'Admin') {
    header("Location: markingDashboard.php");
    exit();
};

// handle edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'edit' && isset($_POST['report_id'])) {
        updateReport(
            $_POST['task_score'],
            $_POST['safety_score'],
            $_POST['theory_score'],
            $_POST['present_score'],
            $_POST['clarity_score'],
            $_POST['learning_score'],
            $_POST['proj_mgmt_score'],
            $_POST['time_mgmt_score'],
            $_POST['comment'],
            $_POST['report_id']
        );
        updateStatus($_POST['report_status'], $_POST['intern_id']);
        header("Location: results.php");
        exit();
    }
}

$sql = "
    SELECT
        s.student_id            AS student_id,
        s.student_name          AS student_name,
        i.intern_id             AS intern_id,
        Ar.assessor_id          AS assessor_id,
        Ar.report_id            AS report_id,
        i.report_status         AS report_status,
        Ar.task_score           AS task_score,
        Ar.safety_score         AS safety_score,
        Ar.theory_score         AS theory_score,
        Ar.present_score        AS present_score,
        Ar.clarity_score        AS clarity_score,
        Ar.learning_score       AS learning_score,
        Ar.proj_mgmt_score      AS proj_mgmt_score,
        Ar.time_mgmt_score      AS time_mgmt_score,
        Ar.comment              AS comment
    FROM internship_report Ar
    JOIN internship i ON i.intern_id = Ar.intern_id
    JOIN student   s  ON s.student_id = i.student_id
    WHERE Ar.assessor_id = ?
    ORDER BY s.student_name ASC
    ";
$result = executePreparedStatement($sql, [$userID]);

// Calculations for summary cards
$totalStudents = $result->num_rows;
$TotalAssessors = 0;
$marksSubmitted = 0;
$pending = 0;

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

// //loop
// foreach ($rows as $row) {
//     $id = $row['intern_id'];

//     //if the intern_id is the first time then the intern_id get save in final
//     if (!isset($final[$id])) {
//         $final[$id] = 0;
//     }
//     //adds intern_id's total_scores together
//     $final[$id] += $row['total_score'];
// }
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
                <h1>Assigned Internships</h1>
                <p>All internships assigned to you, for marking.</p>
            </article>

            <article class="mainDash">
                <div class="totalStudents">
                    <span class="StudentIcon"><i class="fa-solid fa-user-graduate"></i></span>
                    <span> <!-- outputing total students nums -->
                        <h2><?php echo $totalStudents; ?></h2>
                        <p>Internships assigned</p>
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
                        <th>Task</th>
                        <th>Safety</th>
                        <th>Theory</th>
                        <th>Presentation</th>
                        <th>Clarity</th>
                        <th>Learning</th>
                        <th>Project Management</th>
                        <th>Time Management</th>
                        <th>Marks</th>
                        <th>Comments</th>
                        <th>Status</th>
                        <th></th>
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
                                    <td><?php echo htmlspecialchars($row['task_score']); ?></td>
                                    <td><?php echo htmlspecialchars($row['safety_score']); ?></td>
                                    <td><?php echo htmlspecialchars($row['theory_score']); ?></td>
                                    <td><?php echo htmlspecialchars($row['present_score']); ?></td>
                                    <td><?php echo htmlspecialchars($row['clarity_score']); ?></td>
                                    <td><?php echo htmlspecialchars($row['learning_score']); ?></td>
                                    <td><?php echo htmlspecialchars($row['proj_mgmt_score']); ?></td>
                                    <td><?php echo htmlspecialchars($row['time_mgmt_score']); ?></td>
                                    <td><?php 
                                    $weighted_score =
                                    $row['task_score']*0.1 + $row['safety_score']*0.1 + $row['theory_score']*0.1 + 
                                    $row['present_score']*0.15 + $row['clarity_score']*0.1 + $row['learning_score']*0.15 + 
                                    $row['proj_mgmt_score']*0.15 + $row['time_mgmt_score']*0.15;
                                    echo htmlspecialchars(number_format($weighted_score, 2));
                                    ?></td>
                                    <td><?php echo htmlspecialchars($row['comment'] ?? ''); ?></td>
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
                                    <td>
                                        <button class="btn-add" onclick="openEditForm(
                                            '<?php echo htmlspecialchars($row['report_id']); ?>',
                                            '<?php echo htmlspecialchars($row['intern_id']); ?>',
                                            '<?php echo htmlspecialchars($row['student_name']); ?>',
                                            '<?php echo htmlspecialchars($row['task_score']); ?>',
                                            '<?php echo htmlspecialchars($row['safety_score']); ?>',
                                            '<?php echo htmlspecialchars($row['theory_score']); ?>',
                                            '<?php echo htmlspecialchars($row['present_score']); ?>',
                                            '<?php echo htmlspecialchars($row['clarity_score']); ?>',
                                            '<?php echo htmlspecialchars($row['learning_score']); ?>',
                                            '<?php echo htmlspecialchars($row['proj_mgmt_score']); ?>',
                                            '<?php echo htmlspecialchars($row['time_mgmt_score']); ?>',
                                            '<?php echo htmlspecialchars($row['report_status']); ?>',
                                            '<?php echo htmlspecialchars($row['comment'] ?? ''); ?>',
                                        )">
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

    <!-- Edit Form -->
    <div class="form-overlay" id="editForm">
        <div class="form">
            <h3><i class="fa-solid fa-pen"></i> Mark</h3>
            <form method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="report_id" id="report_id">
                <input type="hidden" name="intern_id" id="intern_id">
                <input type="hidden" name="assessor_id" id="assessor_id">

                <label for="task_score">Task Score</label>
                <input type="number" name="task_score" id="task_score" required min="0" max="100">

                <label for="safety_score">Safety Score</label>
                <input type="number" name="safety_score" id="safety_score" required min="0" max="100">

                <label for="theory_score">Theory Score</label>
                <input type="number" name="theory_score" id="theory_score" required min="0" max="100">

                <label for="present_score">Presentation Score</label>
                <input type="number" name="present_score" id="present_score" required min="0" max="100">

                <label for="clarity_score">Clarity Score</label>
                <input type="number" name="clarity_score" id="clarity_score" required min="0" max="100">

                <label for="learning_score">Learning Score</label>
                <input type="number" name="learning_score" id="learning_score" required min="0" max="100">

                <label for="proj_mgmt_score">Project Management Score</label>
                <input type="number" name="proj_mgmt_score" id="proj_mgmt_score" required min="0" max="100">

                <label for="time_mgmt_score">Time Management Score</label>
                <input type="number" name="time_mgmt_score" id="time_mgmt_score" required min="0" max="100">

                <label for="comment">Comment</label>
                <input type="text" name="comment" id="comment">

                <label for="form_account_status">Account Status</label>
                <select name="report_status" id="report_status">
                    <option value="Drafting">Drafting</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Suspended">Suspended</option>
                    <option value="Finalisation">Finalisation</option>
                    <option value="Complete">Complete</option>
                </select>

                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeEditForm()">Cancel</button>
                    <button type="submit" class="btn-save"><i class="fa-solid fa-floppy-disk"></i> Update Profile</button>
                </div>
            </form>
        </div>
    </div>

    <footer>
        <section class="footer">
            <p>© 2026 University of Nottingham Malaysia — Internship Result Management System — Group 39</p>
        </section>
    </footer>
<script src="javascript/SearchMarks.js"></script>

</body>
</html>
