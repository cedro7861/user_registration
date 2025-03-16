<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
include_once 'Database.php';
try{
if (isset($_POST['register'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        echo "Passwords do not match.";
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $activation_code = md5(uniqid(rand(), true));

    $stmt = $pdo->prepare("INSERT INTO users (email, password, activation_code) VALUES (?, ?, ?)");
    $stmt->bindValue(1, $email);
    $stmt->bindValue(2, $hashed_password);
    $stmt->bindValue(3, $activation_code);

    if ($stmt->execute()) {
        // Send activation email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'niyifashacedric8@gmail.com'; //your Gmail
            $mail->Password = 'mvbg yfij paun tnuz'; // Generate App Password from Google
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('niyifashacedric8@gmail.com', 'Cedric');
            $mail->addAddress($email);
            $mail->Subject = "Activate Your Account";
            $mail->Body = "Click the link to activate your account: http://localhost/user_registration/activate.php?code=$activation_code";

            $mail->send();
            echo "Check your email to activate your account.";
        } catch (Exception $e) {
            echo "Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "Registration failed.";
    }
}
    if (isset($_POST['login'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $stmt = $pdo->prepare("SELECT password, is_active FROM users WHERE email = ?");
        $stmt->bindValue(1, $email);
        $stmt->bindValue(1, $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($user) {
            $hashed_password = $user['password'];
            $is_active = $user['is_active'];
            if ($is_active == 0) {
                echo "Your account is not activated. Check your email.";
            } elseif (password_verify($password, $hashed_password)) {
                $_SESSION['user'] = $email;
                $_SESSION['user_id'] = $user['id'];
                header ("Location: dashboard.php");
            } else {
                echo "Invalid password.";
            }
        } else {
            echo "No account found with that email.";
        }
    }
}
catch (PDOException $e) {
    echo "failed: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration and Login</title>
    <!-- Bootstrap CSS (Offline) -->
    <link rel="stylesheet" href="bootstrap-5.3.3-dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <!-- Login Form -->
        <form method="POST" class="mb-3 col-5">
            <h2>Login</h2>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" name="login" class="btn btn-primary">Login</button>
        </form>

        <!-- Registration Modal Button -->
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#registerModal">
            Register
        </button>
        <!-- Registration Modal -->
        <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="registerModalLabel">Register</h5>
                        <button type="button" class="btn-close" data-bs-close="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm Password</label>
                                <input type="password" name="confirm_password" class="form-control" required>
                            </div>
                            <button type="submit" name="register" class="btn btn-primary">Register</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS (Offline) -->
    <script src="bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>