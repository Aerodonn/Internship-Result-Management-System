<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <title>Page Title</title>
    <link rel='stylesheet' href='style\results.css'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <header>
        <section class="navimg_logo">
            <a href="#"><img alt="nottingham logo" src="assets\nottinghamLogoWhite.png"></a>
            <!-- <h2>Internship Portal</h2> -->
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
                <p>Username</p>
                <p>admin</p>
            </article>
            <a>Logout</a>
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
                        <h2>20</h2>
                        <p>Students assigned</p>
                    </span>
                </div>
                <div class="marks_submitted">
                    <span class="marksIcon">
                        <i class="fa-solid fa-circle-check"></i>
                    </span>
                    <span>
                        <h2>20</h2>
                        <p>Marks Submitted</p>
                    </span>
                </div>
                <div class="pending">
                    <span class="pendingIcon">
                        <i class="fa-regular fa-hourglass-half"></i>
                    </span>
                    <span>
                        <h2>20</h2>
                        <p>Pending</p>
                    </span>
                </div>
            </article>
        </section>
        <section class="data">
            <article class="realData">
                <table>
                    <tr>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Program</th>
                        <th>Company</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Report Status</th>
                        <th>Mark Status</th>
                        <th>Action</th>
                    </tr>
                    <tr>
                        <td>20710858</td>
                        <td>MinPyaePhyo</td>
                        <td>Computer Science</td>
                        <td>Google</td>
                        <td>2-Sept</td>
                        <td>10-December</td>
                        <td>submitted</td>
                        <td>marking</td>
                    </tr>
                    <tr>
                        <td>20710858</td>
                        <td>MinPyaePhyo</td>
                        <td>Computer Science</td>
                        <td>Google</td>
                        <td>2-Sept</td>
                        <td>10-December</td>
                        <td>submitted</td>
                        <td>marking</td>
                    </tr>
                    <tr>
                        <td>20710858</td>
                        <td>MinPyaePhyo</td>
                        <td>Computer Science</td>
                        <td>Google</td>
                        <td>2-Sept</td>
                        <td>10-December</td>
                        <td>submitted</td>
                        <td>marking</td>
                    </tr>
                    <tr>
                        <td>20710858</td>
                        <td>MinPyaePhyo</td>
                        <td>Computer Science</td>
                        <td>Google</td>
                        <td>2-Sept</td>
                        <td>10-December</td>
                        <td>submitted</td>
                        <td>marking</td>
                    </tr>
                    <tr>
                        <td>20710858</td>
                        <td>MinPyaePhyo</td>
                        <td>Computer Science</td>
                        <td>Google</td>
                        <td>2-Sept</td>
                        <td>10-December</td>
                        <td>submitted</td>
                        <td>marking</td>
                    </tr>
                    <tr>
                        <td>20710858</td>
                        <td>MinPyaePhyo</td>
                        <td>Computer Science</td>
                        <td>Google</td>
                        <td>2-Sept</td>
                        <td>10-December</td>
                        <td>submitted</td>
                        <td>marking</td>
                    </tr>
                    <tr>
                        <td>20710858</td>
                        <td>MinPyaePhyo</td>
                        <td>Computer Science</td>
                        <td>Google</td>
                        <td>2-Sept</td>
                        <td>10-December</td>
                        <td>submitted</td>
                        <td>marking</td>
                    </tr>
                    <tr>
                        <td>20710858</td>
                        <td>MinPyaePhyo</td>
                        <td>Computer Science</td>
                        <td>Google</td>
                        <td>2-Sept</td>
                        <td>10-December</td>
                        <td>submitted</td>
                        <td>marking</td>
                    </tr>
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