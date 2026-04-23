<?php
session_start();

include 'connect.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputUsername = trim($_POST['username'] ?? '');
    $inputPassword = trim($_POST['password'] ?? '');

    if ($inputUsername === '' || $inputPassword === '') {
        $error = "Please enter both username and password.";
    } else {
        $stmt = $conn->prepare("SELECT user_id, username, password, system_role FROM user_login WHERE username = ?");
        $stmt->bind_param("s", $inputUsername);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            $passwordMatch = ($inputPassword === $user['password']);

            if ($passwordMatch) {
                $_SESSION['UserID']     = $user['user_id'];
                $_SESSION['Username']   = $user['username'];
                $_SESSION['SystemRole'] = $user['system_role'];

                if ($user['system_role'] === 'Admin') {
                    header("Location: manage_student.php");
                } else {
                    header("Location: myStudents.php");
                }
                exit();
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "No account found with that username.";
        }

        $stmt->close();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <title>Login — Internship Portal</title>
    <link rel='stylesheet' href='style/login.css'>
</head>
<body>

    <section>
        <article class="formBox">
            <form method="post" class="login">
                <img src="assets/nottinghamLogo.png" alt="Nottingham Logo">
                <h2>Internship Portal</h2>

                <?php if ($error !== ''): ?>
                    <p class="login-error"><?php echo htmlspecialchars($error); ?></p>
                <?php endif; ?>

                <section id="dataRetrival">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" class="loginInput"
                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"><br>

                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" class="loginInput"><br>
                    <a id="forgotPass" href="#">Forgot password?</a><br>
                </section>

                <section id="submitANDreset">
                    <input type="submit" value="Login" class="loginDone">
                </section>
            </form>
        </article>
    </section>

    <footer></footer>
</body>
</html>