<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Load PHPMailer
require_once 'Database.php';

if (isset($_POST['forgot_password'])) {
    $email = trim($_POST['email']);

    // Check if the email exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bindValue(1, $email, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Generate a secure random password (5 to 20 characters)
        $new_password = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*'), 0, rand(5, 20));
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update the password in the database
        $updateStmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
        $updateStmt->bindValue(1, $hashed_password, PDO::PARAM_STR);
        $updateStmt->bindValue(2, $email, PDO::PARAM_STR);

        if ($updateStmt->execute()) {
            // Send email with the new password
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'niyifashacedric8@gmail.com'; //your Gmail
                $mail->Password = 'mvbg yfij paun tnuz'; // Generate App Password from Google
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('niyifashacedric@gmail.com', 'Cedric');
                $mail->addAddress($email);
                $mail->Subject = "Your New Password";
                $mail->Body = "Hello, your new password is: $new_password\n\nPlease change it after logging in.";

                $mail->send();
                echo "<p style='color:forestgreen;font-size:2rem;margin-top:6%;margin-left:4%;'>Password reset successful. Check your email for the new password.</p>";
            } catch (Exception $e) {
                echo "Mailer Error: " . $mail->ErrorInfo;
            }
        } else {
            echo "<script>alert('Failed to reset password.')</script>";
        }
    } else {
        echo "<p style='color:red;text-align:center;margin-top:5%;font-size:2rem;'>Email not found.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">    
<head>
    <meta charset="UTF-8">
    <title>Forget Password</title>
    <!-- Bootstrap CSS (Offline) -->
    <link rel="stylesheet" href="bootstrap-5.3.3-dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5 col-5">
        <h1>Forget Password</h1>
        <form method="post">
        <label for="email" class="form-label">Email</label>
        <input type="Email" name="email" id="email" class="form-control" required>
        <button type="submit" name="forgot_password" class="btn btn-primary mt-3">Reset Password</button>
        </form>
        <button class="btn btn-success mt-3" onclick="window.location.href='index.php'">Back</button>
    </div>
</body>
</html>