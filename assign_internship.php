<?php



session_start();
// only allows if current user session's systemRole is admin else it will log you out.
if (!isset($_SESSION['SystemRole']) || $_SESSION['SystemRole'] !== 'Admin') {
    header("Location: login.php");
    exit();
}


include 'connect.php';
include 'prepared_statements.php';
include 'action_internship.php';

// handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    if ($_POST['action'] === 'delete' && isset($_POST['intern_id'])) {
        deleteInternship($_POST['intern_id']);
        header("Location: assign_internship.php");
        exit();
    }
// handle edit
    if ($_POST['action'] === 'edit' && isset($_POST['intern_id'])) {
        updateInternship(
            $_POST['lecturer_id'],
            $_POST['supervisor_id'],
            $_POST['company'],
            $_POST['start_date'],
            $_POST['end_date'],
            $_POST['report_status'],
            $_POST['intern_id']
        );
        header("Location: assign_internship.php");
        exit();
    }
// handle adding
    if ($_POST['action'] === 'add') {
        addInternship(
            $_POST['lecturer_id'],
            $_POST['supervisor_id'],
            $_POST['student_id'],
            $_POST['company'],
            $_POST['start_date'],
            $_POST['end_date'],
            $_POST['report_status']
        );
        header("Location: assign_internship.php"); 
        exit();
    }
    
}

//selecting the table columns/attributes needed for this page.
$sql = "
    SELECT
        s.student_id            AS student_id,
        s.student_reg_number    AS student_regnum,
        s.student_name          AS student_name,
        a1.user_id              AS lecturer_id,
        a1.full_name            AS lecturer_name,
        a2.user_id              AS supervisor_id,
        a2.full_name            AS supervisor_name,
        i.internship_company    AS company,
        i.start_date            AS start_date,
        i.end_date              AS end_date,
        i.report_status         AS report_status,
        i.intern_id             AS intern_id
    FROM internship i
    JOIN student   s  ON i.student_id    = s.student_id
    JOIN assessor  a1 ON i.lecturer_id   = a1.user_id
    JOIN assessor  a2 ON i.supervisor_id = a2.user_id
    JOIN internship_report ir ON i.intern_id = ir.intern_id
    GROUP BY i.intern_id
    ORDER BY i.intern_id ASC
";

$result = executePreparedStatement($sql, []);

$totalStudents = $result->num_rows;
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

// Fetching all Lecturers
$sql_lecturers = "SELECT user_id, full_name FROM assessor WHERE assessor_type = 'Lecturer'";
$lecturers_result = executePreparedStatement($sql_lecturers, []);
$lecturers = $lecturers_result->fetch_all(MYSQLI_ASSOC);

// Fetching all Supervisors
$sql_supervisors = "SELECT user_id, full_name FROM assessor WHERE assessor_type = 'Supervisor'";
$supervisors_result = executePreparedStatement($sql_supervisors, []);
$supervisors = $supervisors_result->fetch_all(MYSQLI_ASSOC);

?>
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <title>Manage Student</title>
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
                <?php if ($_SESSION['SystemRole'] === 'Admin'): ?>
                    <li class="list"><a href="dashboard.php"><i class="fa-solid fa-house"></i> Dashboard</a></li>
                <?php endif; ?>
                <?php if ($_SESSION['SystemRole'] === 'Assessor'): ?>
                    <li class="list"><a href="myStudents.php"><i class="fa-solid fa-chalkboard-user"></i> Assessor</a></li>
                <?php endif; ?>
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
        <section class="assign_internBlock">
            <div class="intern_form_container">
                <form class="intern_form" method="POST" action="assign_internship.php">
                    <h2>Assign Internship</h2>
                    <input type="hidden" name="action" value="add">

                    <label for="add_student_id">Student ID</label>
                    <input type="text" name="student_id" id="add_student_id" required>

                    <label for="add_lecturer_id">Lecturer</label>
                    <select name="lecturer_id" id="add_lecturer_id" required>
                        <option value="">Select Lecturer</option>
                        <?php foreach ($lecturers as $lecturer): ?>
                            <option value="<?php echo $lecturer['user_id']; ?>">
                                <?php echo htmlspecialchars($lecturer['full_name']); ?> (ID: <?php echo $lecturer['user_id']; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label for="add_supervisor_id">Supervisor</label>
                    <select name="supervisor_id" id="add_supervisor_id" required>
                        <option value="">Select Supervisor</option>
                        <?php foreach ($supervisors as $supervisor): ?>
                            <option value="<?php echo $supervisor['user_id']; ?>">
                                <?php echo htmlspecialchars($supervisor['full_name']); ?> (ID: <?php echo $supervisor['user_id']; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    
                    <label for="add_company">Company</label>
                    <input type="text" name="company" id="add_company" required>

                    <label for="add_start_date">Start Date</label>
                    <input type="date" name="start_date" id="add_start_date" required>

                    <label for="end_start_date">End Date</label>
                    <input type="date" name="end_date" id="end_start_date" required>

                    <label for="add_report_status">Status</label>
                    <select name="report_status" class="add_report_status">
                        <option value="Drafting">Drafting</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Suspended">Suspended</option>
                        <option value="Finalisation">Finalisation</option>
                        <option value="Complete">Complete</option>
                    </select>
                    <div class="form-actions">
                        <button type="reset" class="btn-cancel">Reset</button>
                        <button type="submit" class="btn-save"><i class="fa-solid fa-floppy-disk"></i> Save Internship</button>
                    </div>

                </form>
            </div>

            <div class="intern_table_wrapper">
                <table class="intern_table">
                    <thead>
                        <tr>
                            <th>Intern ID</th>
                            <th>Student ID</th>
                            <th>Student Name</th>
                            <th>Lecturer Name</th>
                            <th>Supervisor Name</th>
                            <th>Company</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                            <th>actions</th>
                        </tr>
                    </thead>
                    <tbody> <!-- If the row are empty, then output message -->
                        <?php if (empty($rows)): ?>
                            <tr>
                                <td colspan="9" style="text-align:center;">No students found.</td>
                            </tr>
                        <?php else: ?> <!-- if not, loop through each rows of data and output each attribute values -->
                            <?php foreach ($rows as $row): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['intern_id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['student_id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['lecturer_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['supervisor_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['company']); ?></td>
                                    <td><?php echo htmlspecialchars($row['start_date']); ?></td>
                                    <td><?php echo htmlspecialchars($row['end_date']); ?></td>
                                    <td><?php echo htmlspecialchars($row['report_status']); ?></td>
                                    <td>
                                        <!-- edit button and this put PHP values into javascript function -->
                                        <button class="btn-edit-intern" onclick="openEditForm(  
                                            '<?php echo htmlspecialchars($row['lecturer_id']); ?>',
                                            '<?php echo htmlspecialchars($row['supervisor_id']); ?>',
                                            '<?php echo htmlspecialchars($row['company']); ?>',
                                            '<?php echo htmlspecialchars($row['start_date']); ?>',
                                            '<?php echo htmlspecialchars($row['end_date']); ?>',
                                            '<?php echo htmlspecialchars($row['report_status']); ?>',
                                            '<?php echo htmlspecialchars($row['intern_id']); ?>'
                                        )">
                                            <i class="fa-solid fa-pen"></i> Edit
                                        </button>

                                        <!-- delete button -->
                                        <form method="POST" action="assign_internship.php" class="delete-button-" onsubmit="return confirm('Delete this student\'s internship record?')">
                                            <input type="hidden" name="action"    value="delete">
                                            <input type="hidden" name="intern_id" value="<?php echo htmlspecialchars($row['intern_id']); ?>">
                                            <button type="submit" class="btn-delete-intern">
                                                <i class="fa-solid fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    //edit button
    <div class="form-overlay" id="editForm">
        <div class="form">
            <h3><i class="fa-solid fa-pen"></i> Edit Internship</h3>
            <form method="POST" action="assign_internship.php">
                <input type="hidden" name="action"    value="edit">
                <input type="hidden" name="intern_id" id="form_intern_id">

                <label for="form_supervisor_id">Supervisor ID</label>
                <select name="lecturer_id" id="form_lecturer_id" required>
                    <option value="">Select Lecturer</option>
                    <?php foreach ($lecturers as $lecturer): ?>
                        <option value="<?php echo $lecturer['user_id']; ?>">
                            <?php echo htmlspecialchars($lecturer['full_name']); ?> (ID: <?php echo $lecturer['user_id']; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="add_supervisor_id">Supervisor</label>
                <select name="supervisor_id" id="form_supervisor_id" required>
                    <option value="">Select Supervisor</option>
                    <?php foreach ($supervisors as $supervisor): ?>
                        <option value="<?php echo $supervisor['user_id']; ?>">
                            <?php echo htmlspecialchars($supervisor['full_name']); ?> (ID: <?php echo $supervisor['user_id']; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="form_company">Company</label>
                <input type="text" name="company" id="form_company" required>

                <label for="form_start_date">Start Date</label>
                <input type="date" name="start_date" id="form_start_date" required>

                <label for="form_end_date">End Date</label>
                <input type="date" name="end_date" id="form_end_date" required>

                <label for="form_report_status">Status</label>
                <select name="report_status" id="form_report_status">
                    <option value="Drafting">Drafting</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Suspended">Suspended</option>
                    <option value="Finalisation">Finalisation</option>
                    <option value="Complete">Complete</option>
                </select>

                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeEditForm()">Cancel</button>
                    <button type="submit" class="btn-save"><i class="fa-solid fa-floppy-disk"></i> Update Internship</button>
                </div>
            </form>
        </div>
    </div>
    
    
    <footer>
        <section class="footer">
            <p>© 2026 University of Nottingham Malaysia — Internship Result Management System — Group 39</p>
        </section>
    </footer>

<script src="javascript/internship_assignment.js"></script>

</body>
</html>