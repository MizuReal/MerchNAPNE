<?php include('layouts/header.php');?>

<?php
session_start();

include('server/connection.php');

if (isset($_SESSION['logged_in'])) {
    // Check if there's a redirect URL before going to account.php
    if (isset($_SESSION['redirect_after_login'])) {
        $redirect = $_SESSION['redirect_after_login'];
        unset($_SESSION['redirect_after_login']);
        header("Location: $redirect");
        exit();
    } else {
        header('location: account.php');
        exit();
    }
}

$emailErr = $passwordErr = '';
$email = $password = '';

if (isset($_POST['login_btn'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validate email format using regex
    if (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email)) {
        $emailErr = 'Invalid email format';
    }

    // Validate password (simple check, ensure at least 6 characters)
    if (strlen($password) < 6) {
        $passwordErr = 'Password must be at least 6 characters long';
    }

    // If there are no errors, proceed with login
    if (empty($emailErr) && empty($passwordErr)) {
        $password = md5($password);  // Hash the password

        // Check if email exists in the database
        $stmt = $conn->prepare("SELECT user_id, user_name, user_email, user_password, user_status FROM users WHERE user_email=? LIMIT 1");
        $stmt->bind_param('s', $email);

        if ($stmt->execute()) {
            $stmt->bind_result($user_id, $user_name, $user_email, $user_password, $user_status);
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                // If email exists, fetch user data
                $stmt->fetch();

                // Check if account is active
                if ($user_status == 0) {
                    header('location: login.php?error=Your account has been deactivated.');
                    exit();
                }

                // Verify the password using md5 (you can replace it with password_hash later)
                if ($user_password == $password) {
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['user_name'] = $user_name;
                    $_SESSION['user_email'] = $user_email;
                    $_SESSION['logged_in'] = true;

                    // Check for redirect URL after successful login
                    if (isset($_SESSION['redirect_after_login'])) {
                        $redirect = $_SESSION['redirect_after_login'];
                        unset($_SESSION['redirect_after_login']);
                        header("Location: $redirect");
                        exit();
                    } else {
                        header('location: account.php?message=Logged in successfully!');
                        exit();
                    }
                } else {
                    header('location: login.php?error=Password or email is incorrect.');
                    exit();
                }
            } else {
                // If email doesn't exist, show error
                header('location: login.php?error=Email not registered.');
                exit();
            }
        } else {
            header('location: login.php?error=Something went wrong.');
            exit();
        }
    }
}
?>

<!-- Login -->
<section class="my-5 py-5">
    <div class="container text-center mt-3 pt-5">
        <h2 class="font-weight-bold">Login</h2>
        <hr class="mx-auto">
    </div>
    <div class="container mx-auto">
        <form id="login-form" method="POST" action="login.php">
            <p style="color:red" class="text-center"><?php if (isset($_GET['error'])) { echo htmlspecialchars($_GET['error']); } ?></p>
            <p style="color:red" class="text-center"><?php if (isset($_GET['message'])) { echo htmlspecialchars($_GET['message']); } ?></p>
            <div class="form-group">
                <label>Email</label>
                <input type="text" class="form-control" id="login-email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>">
                <span style="color:red"><?php echo $emailErr; ?></span>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" class="form-control" id="login-password" name="password" placeholder="Password" value="<?php echo htmlspecialchars($password); ?>">
                <span style="color:red"><?php echo $passwordErr; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn" id="login-btn" name="login_btn" value="Login">
            </div>
            <div class="form-group">
                <a id="register-url" href="register.php" class="btn">Don't have an account? Register</a> 
            </div>
        </form>
    </div>
</section>

<?php include('layouts/footer.php');?>
