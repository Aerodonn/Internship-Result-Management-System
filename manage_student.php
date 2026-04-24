<?php



session_start();
// only allows if current user session's systemRole is admin else it will log you out.
if (!isset($_SESSION['SystemRole']) || $_SESSION['SystemRole'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

include 'connect.php';
include 'prepared_statements.php';
include 'action_students.php';

// handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    if ($_POST['action'] === 'delete' && isset($_POST['student_id'])) {
        deleteStudent($_POST['student_id']);
        header("Location: manage_student.php");
        exit();
    }
// handle edit
    if ($_POST['action'] === 'edit' && isset($_POST['student_id'])) {
        updateStudent(
            $_POST['student_id'],
            $_POST['student_reg_number'],
            $_POST['student_name'],
            $_POST['email_address'],
            $_POST['programme'],
            $_POST['enrollment_date'],
            $_POST['account_status']
        );
        header("Location: manage_student.php");
        exit();
    }
// handle adding
    if ($_POST['action'] === 'add') {
        addStudent(
            $_POST['student_reg_number'],
            $_POST['student_name'],
            $_POST['email_address'],
            $_POST['programme'],
            $_POST['enrollment_date'],
            $_POST['account_status']
        );
        header("Location: manage_student.php");
        exit();
    }
    
}

//selecting the table columns/attributes needed for this page.
$sql = "
    SELECT  student_id,
            student_reg_number,
            student_name,
            email_address,
            programme,
            enrollment_date,
            account_status 
    FROM student
    ORDER BY student_id ASC
";

$report_status = "
    SELECT report_status
    FROM internship";

$result = executePreparedStatement($sql, []);   
$report_result = executePreparedStatement($report_status, []);   

$totalStudents = $result->num_rows;
$marksSubmitted = 0;
$pending = 0;

$rows = $result->fetch_all(MYSQLI_ASSOC);
$report_rows = $report_result->fetch_all(MYSQLI_ASSOC);
//looping through the report_status column to find "Complete" so we can perform totaling on pending and resultDone variables
foreach ($report_rows as $row) {
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
                            <th>Student Registeration Number</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Programme</th>
                            <th>Enrollment Date</th>
                            <th>Account Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody> <!-- If the row are empty, then output message -->
                        <?php if (empty($rows)): ?>
                            <tr>
                                <td colspan="7" style="text-align:center;">No students found.</td>
                            </tr>
                        <?php else: ?> <!-- if not, loop through each rows of data and output each attribute values -->
                            <?php foreach ($rows as $row): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['student_id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['student_reg_number']); ?></td>
                                    <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email_address']); ?></td>
                                    <td><?php echo htmlspecialchars($row['programme']); ?></td>
                                    <td><?php echo htmlspecialchars($row['enrollment_date']); ?></td>
                                    <td><?php echo htmlspecialchars($row['account_status']); ?></td>
                                    <td>
                                        <!-- edit button-->
                                        <button class="btn-edit" onclick="openEditForm(  //this put PHP values into javascript function
                                            '<?php echo htmlspecialchars($row['student_id']); ?>',
                                            '<?php echo htmlspecialchars($row['student_reg_number']); ?>',
                                            '<?php echo htmlspecialchars($row['student_name']); ?>',
                                            '<?php echo htmlspecialchars($row['email_address']); ?>',
                                            '<?php echo htmlspecialchars($row['programme']); ?>',
                                            '<?php echo htmlspecialchars($row['enrollment_date']); ?>',
                                            '<?php echo htmlspecialchars($row['account_status']); ?>'
                                        )">
                                            <i class="fa-solid fa-pen"></i> Edit
                                        </button>

                                        <!-- delete button -->
                                        <form method="POST" class="delete-button" onsubmit="return confirm('Delete this student\'s internship record?')">
                                            <input type="hidden" name="action"    value="delete">
                                            <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($row['student_id']); ?>">
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

                <label for="add_student_reg_number">Student Registration Number</label>
                <input type="text" name="student_reg_number" id="add_student_reg_number" placeholder="e.g. 20708501" maxlength="8" required>

                <label for="add_student_name">Student Name</label>
                <input type="text" name="student_name" id="add_student_name" maxlength="70" required>

                <label for="add_email_address">Student Email</label>
                <input type="email" name="email_address" id="add_email_address" required>

                <label for="add_programme">Programme</label>
                <input type="text" name="programme" id="add_programme" maxlength="50" required>

                <label for="add_enrollment_date">Enrollment Date</label>
                <input type="date" name="enrollment_date" id="add_enrollment_date" required>

                <label for="add_account_status">Account Status</label>
                <select name="account_status" id="add_account_status">
                    <option value="Active">Active</option>
                    <option value="Graduated">Graduated</option>
                    <option value="On-leave">On-leave</option>
                    <option value="Suspended">Suspended</option>
                </select>

                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeAddForm()">Cancel</button>
                    <button type="submit" class="btn-save"><i class="fa-solid fa-floppy-disk"></i> Save Student</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="form-overlay" id="editForm">
        <div class="form">
            <h3><i class="fa-solid fa-pen"></i> Edit Student Profile</h3>
            <form method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="student_id" id="form_student_id">

                <label for="form_reg_number">Registration Number</label>
                <input type="text" name="student_reg_number" id="form_reg_number" required>

                <label for="form_student_name">Full Name</label>
                <input type="text" name="student_name" id="form_student_name" required>

                <label for="form_email">Email Address</label>
                <input type="email" name="email_address" id="form_email" required>

                <label for="form_programme">Programme</label>
                <input type="text" name="programme" id="form_programme" required>

                <label for="form_enrollment_date">Enrollment Date</label>
                <input type="date" name="enrollment_date" id="form_enrollment_date" required>

                <label for="form_account_status">Account Status</label>
                <select name="account_status" id="form_account_status">
                    <option value="Active">Active</option>
                    <option value="Graduated">Graduated</option>
                    <option value="On-leave">On-leave</option>
                    <option value="Suspended">Suspended</option>
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

<script src="javascript/Manage_student.js"></script>

</body>
</html>