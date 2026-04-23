<?php
session_start();

if (!isset($_SESSION['SystemRole']) || $_SESSION['SystemRole'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

include 'connect.php';
include 'prepared_statements.php';
include 'action_assessor.php';

// handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    if ($_POST['action'] === 'delete' && isset($_POST['user_id'])) {
        deleteAssessor($_POST['user_id']);
        header("Location: manage_assessor.php");
        exit();
    }
// handle edit
    if ($_POST['action'] === 'edit' && isset($_POST['user_id'])) {
        updateAssessor(
            $_POST['user_id'],
            $_POST['full_name'],
            $_POST['phone_number'],
            $_POST['email_address'],
            $_POST['organisation'],
            $_POST['assessor_type']
        );
        header("Location: manage_assessor.php");
        exit();
    }
// handle adding
    if ($_POST['action'] === 'add') {
        addAssessor(
            $_POST['username'],      
            $_POST['password'],      
            $_POST['full_name'],
            $_POST['phone_number'],
            $_POST['email_address'],
            $_POST['organisation'],
            $_POST['assessor_type']
        );
        header("Location: manage_assessor.php");
        exit();
    }
    
}


$sql = "
    SELECT 
        login.username      AS username,
        login.password      AS password,
        a.user_id           AS user_id, 
        a.full_name         AS full_name, 
        a.phone_number      AS phone_number, 
        a.email_address     AS email_address, 
        a.organisation      AS organisation, 
        a.assessor_type     AS assessor_type
    FROM assessor a
    JOIN user_login login  ON a.user_id    = login.user_id
    ORDER BY user_id ASC
";

$result = executePreparedStatement($sql, []);

$totalAssessor = $result->num_rows;
$totalLecturer = 0;
$totalSupervisor = 0;

$rows = $result->fetch_all(MYSQLI_ASSOC);

foreach ($rows as $row) {
    if ($row['assessor_type'] === 'Lecturer') {
        $totalLecturer++;
    } else {
        $totalSupervisor++;
    }
}

?>
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <title>Manage Assessor</title>
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
                <h1>Manage Assessors</h1>
                <p>All assessors enrolled in the current internship cycle.</p>
            </article>

            <article class="mainDash">
                <div class="totalAssessors">
                    <span class="StudentIcon"><i class="fa-solid fa-user-graduate"></i></span>
                    <span>
                        <h2><?php echo $totalAssessor; ?></h2>
                        <p>Total Assessors</p>
                    </span>
                </div>
                <div class="TotalLecturers">
                    <span class="marksIcon"><i class="fa-solid fa-chalkboard-user"></i></i></i></span>
                    <span>
                        <h2><?php echo $totalLecturer; ?></h2>
                        <p>Total Lecturers</p>
                    </span>
                </div>
                <div class="TotalSupervisors">
                    <span class="pendingIcon"><i class="fa-solid fa-user-tie"></i></i></span>
                    <span>
                        <h2><?php echo $totalSupervisor; ?></h2>
                        <p>Total Supervisors</p>
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
                <i class="fa-solid fa-user-plus"></i> Add Assessor
            </button>
        </section>

        <section class="data">
            <article class="realData">
                <table>
                    <thead>
                        <tr>
                            <th>Assessor ID</th>
                            <th>Name</th>
                            <th>Phone Number</th>
                            <th>Email</th>
                            <th>Organisation</th>
                            <th>AsessorType</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($rows)): ?>
                            <tr>
                                <td colspan="6" style="text-align:center;">No students found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($rows as $row): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['phone_number']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email_address']); ?></td>
                                    <td><?php echo htmlspecialchars($row['organisation']); ?></td>
                                    <td><?php echo htmlspecialchars($row['assessor_type']); ?></td>
                                    <td>
                                        <!-- edit button-->
                                        <button class="btn-edit" onclick="openEditForm(
                                            '<?php echo htmlspecialchars($row['user_id']); ?>',
                                            '<?php echo htmlspecialchars($row['full_name']); ?>',
                                            '<?php echo htmlspecialchars($row['phone_number']); ?>',
                                            '<?php echo htmlspecialchars($row['email_address']); ?>',
                                            '<?php echo htmlspecialchars($row['organisation']); ?>',
                                            '<?php echo htmlspecialchars($row['assessor_type']); ?>'
                                        )">
                                            <i class="fa-solid fa-pen"></i> Edit
                                        </button>

                                        <!-- delete button -->
                                        <form method="POST" class="delete-button" onsubmit="return confirm('Delete this student\'s internship record?')">
                                            <input type="hidden" name="action"    value="delete">
                                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($row['user_id']); ?>">
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

                <label for="add_username">Username</label>
                <input type="text" name="username" id="add_username" required>

                <label for="add_password">Password</label>
                <input type="password" name="password" id="add_password" required>

                <label for="add_full_name">Assessor Name</label>
                <input type="text" name="full_name" id="add_full_name" required>

                <label for="add_phone_number">Phone Number</label>
                <input type="text" name="phone_number" id="add_phone_number" required>

                <label for="add_email_address">Email</label>
                <input type="email" name="email_address" id="add_email_address" required>

                <label for="add_organisation">Organisation</label>
                <input type="text" name="organisation" id="add_organisation" required>

                <label for="add_assessor_type">Assessor Type</label>
                <select name="assessor_type" id="add_assessor_type">
                    <option value="Lecturer">Lecturer</option>
                    <option value="Supervisor">Supervisor</option>
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
            <h3><i class="fa-solid fa-pen"></i> Edit Assessor Record</h3>
            <form method="POST">
                <input type="hidden" name="action"   id="form_action"  value="edit">
                <input type="hidden" name="user_id"  id="form_user_id">

                <label for="form_full_name">Full Name</label>
                <input type="text" name="full_name" id="form_full_name" required>

                <label for="form_phone_number">Phone Number</label>
                <input type="text" name="phone_number" id="form_phone_number" required>

                <label for="form_email_address">Email</label>
                <input type="email" name="email_address" id="form_email_address" required>

                <label for="form_organisation">Organisation</label>
                <input type="text" name="organisation" id="form_organisation" required>

                <label for="form_assessor_type">Assessor Type</label>
                <select name="assessor_type" id="form_assessor_type">
                    <option value="Lecturer">Lecturer</option>
                    <option value="Supervisor">Supervisor</option>  <!-- was duplicated as Lecturer -->
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

<script src="javascript/Manage_assessor.js"></script>

</body>
</html>