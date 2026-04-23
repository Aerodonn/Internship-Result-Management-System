<?php



session_start();

if (!isset($_SESSION['SystemRole']) || $_SESSION['SystemRole'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

include 'connect.php';
include 'prepared_statements.php';
include 'edit_delete.php';

// handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    if ($_POST['action'] === 'delete' && isset($_POST['intern_id'])) {
        deleteStudent($_POST['intern_id']);
        header("Location: manage_student.php");
        exit();
    }
// handle edit
    if ($_POST['action'] === 'edit' && isset($_POST['intern_id'])) {
        updateStudent(
            $_POST['intern_id'],
            $_POST['company'],
            $_POST['start_date'],
            $_POST['end_date'],
            $_POST['report_status']
        );
        header("Location: manage_student.php");
        exit();
    }
// handle adding
    if ($_POST['action'] === 'add') {
        addStudent(
            $_POST['student_regnum'],
            $_POST['student_name'],
            $_POST['student_email'],
            $_POST['student_programme'],
            $_POST['student_enrollment'],
            $_POST['student_status'],
            $_POST['company'],
            $_POST['start_date'],
            $_POST['end_date'],
            $_POST['lecturer_id'],
            $_POST['supervisor_id'],
            $_POST['report_status']
        );
        header("Location: manage_student.php");
        exit();
    }
    
}


$sql = "
    SELECT
        s.student_id            AS student_id,
        s.student_reg_number    AS student_regnum,
        s.student_name          AS student_name,
        s.email_address         AS student_email,
        s.programme             AS student_programme,
        s.enrollment_date       AS student_enrollment,
        s.account_status        AS student_status,
        a1.full_name            AS lecturer_name,
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
    ORDER BY s.student_name ASC
";

$result = executePreparedStatement($sql, []);

$totalStudents = $result->num_rows;
$marksSubmitted = 0;
$pending = 0;

$rows = $result->fetch_all(MYSQLI_ASSOC);

foreach ($rows as $row) {
    if ($row['report_status'] === 'Complete') {
        $marksSubmitted++;
    } else {
        $pending++;
    }
}
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
                <h1>Manage Students</h1>
                <p>All students enrolled in the current internship cycle.</p>
            </article>

            <article class="mainDash">
                <div class="totalStudents">
                    <span class="StudentIcon"><i class="fa-solid fa-user-graduate"></i></span>
                    <span>
                        <h2><?php echo $totalStudents; ?></h2>
                        <p>Students assigned</p>
                    </span>
                </div>
                <div class="marks_submitted">
                    <span class="marksIcon"><i class="fa-solid fa-circle-check"></i></span>
                    <span>
                        <h2><?php echo $marksSubmitted; ?></h2>
                        <p>Marks Submitted</p>
                    </span>
                </div>
                <div class="pending">
                    <span class="pendingIcon"><i class="fa-regular fa-hourglass-half"></i></span>
                    <span>
                        <h2><?php echo $pending; ?></h2>
                        <p>Pending</p>
                    </span>
                </div>
            </article>
        </section>

        <section class="Searchbar">
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
            <button class="btn-add" onclick="openAddForm()">
                <i class="fa-solid fa-user-plus"></i> Add Student
            </button>
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
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($rows)): ?>
                            <tr>
                                <td colspan="7" style="text-align:center;">No students found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($rows as $row): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['student_id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['student_programme']); ?></td>
                                    <td><?php echo htmlspecialchars($row['supervisor_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['company']); ?></td>
                                    <td>
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
                                        <!-- edit button-->
                                        <button class="btn-edit" onclick="openEditForm(
                                            '<?php echo htmlspecialchars($row['intern_id']); ?>',
                                            '<?php echo htmlspecialchars($row['company']); ?>',
                                            '<?php echo htmlspecialchars($row['start_date']); ?>',
                                            '<?php echo htmlspecialchars($row['end_date']); ?>',
                                            '<?php echo htmlspecialchars($row['report_status']); ?>'
                                        )">
                                            <i class="fa-solid fa-pen"></i> Edit
                                        </button>

                                        <!-- delete button -->
                                        <form method="POST" class="delete-button" onsubmit="return confirm('Delete this student\'s internship record?')">
                                            <input type="hidden" name="action"    value="delete">
                                            <input type="hidden" name="intern_id" value="<?php echo htmlspecialchars($row['intern_id']); ?>">
                                            <button type="submit" class="btn-delete">
                                                <i class="fa-solid fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </article>
        </section>
    </main>
    
    <!-- Add Student Form -->
    <div class="form-overlay" id="addForm">
        <div class="form">
            <h3><i class="fa-solid fa-user-plus" id="addStudentIcon"></i> Add Student</h3>
            <form method="POST">
                <input type="hidden" name="action" value="add">

                <label for="add_student_regnum">Student Registraction Number</label>
                <input type="text" name="student_regnum" id="add_student_regnum" required>

                <label for="add_student_name">Student Name</label>
                <input type="text" name="student_name" id="add_student_name" required>

                <label for="add_student_email">Student email</label>
                <input type="email" name="student_email" id="add_student_email" required>

                <label for="add_student_programme">Programme</label>
                <input type="text" name="student_programme" id="add_student_programme" required>

                <label for="add_student_enrollment">Programme</label>
                <input type="date" name="student_enrollment" id="add_student_enrollment" required>

                <label for="add_student_status">Student Status</label>
                <select name="student_status" id="add_student_status">
                    <option value="Active">Active</option>
                    <option value="Graduated">Graduated</option>
                    <option value="On-leave">On-leave</option>
                    <option value="Suspended">Suspended</option>
                </select>

                <label for="add_company">Company</label>
                <input type="text" name="company" id="add_company" required>

                <label for="add_start_date">Start Date</label>
                <input type="date" name="start_date" id="add_start_date" required>

                <label for="add_end_date">End Date</label>
                <input type="date" name="end_date" id="add_end_date" required>

                <label for="add_lecturer_id">Lecturer ID</label>
                <input type="text" name="lecturer_id" id="add_lecturer_id" required>

                <label for="add_supervisor_id">Supervisor ID</label>
                <input type="text" name="supervisor_id" id="add_supervisor_id" required>

                <label for="add_report_status">Status</label>
                <select name="report_status" id="add_report_status">
                    <option value="Drafting">Drafting</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Suspended">Suspended</option>
                    <option value="Finalisation">Finalisation</option>
                    <option value="Complete">Complete</option>
                </select>

                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeAddForm()">Cancel</button>
                    <button type="submit" class="btn-save"><i class="fa-solid fa-floppy-disk"></i> Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="form-overlay" id="editForm">
        <div class="form">
            <h3><i class="fa-solid fa-pen"></i> Edit Internship Record</h3>
            <form method="POST">
                <input type="hidden" name="action"    id="form_action"    value="edit">
                <input type="hidden" name="intern_id" id="form_intern_id">

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
                    <button type="submit" class="btn-save"><i class="fa-solid fa-floppy-disk"></i> Save</button>
                </div>
            </form>
        </div>
    </div>

    <footer>
        <section class="footer">
            <p>© 2026 University of Nottingham Malaysia — Internship Result Management System — Group 39</p>
        </section>
    </footer>

<script src="javascript.js"></script>

</body>
</html>