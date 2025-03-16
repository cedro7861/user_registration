<?php
session_start();
require_once 'Database.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
}

$email = $_SESSION['user'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bindValue(1, $email);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "<script>alert('User not found.')</script>";
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_email = $_POST['current_email'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($current_email !== $user['email']) {
        echo "<script>alert('Invalid email.');</script>";
    } elseif ($new_password !== $confirm_password) {
        echo "<script>alert('Passwords do not match.');</script>";
    }
    elseif($new_password == $confirm_password) {
        if (strlen($new_password) < 6 || !preg_match('/[A-Z]/', $new_password) || !preg_match('/[a-z]/', $new_password) || !preg_match('/\d/', $new_password)) {
            echo "<script>alert('Password must be at least 6 characters long and include at least one uppercase letter, one lowercase letter, and one number.');</script>";
        } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->execute([$hashed_password, $email]);
        echo "<script>alert('Password changed successfully.');</script>";
    }
}
}
?>
<!DOCTYPE html>
<html lang="en">    
<head>
    <meta charset="UTF-8">
    <title>Change Password</title>
    <!-- Bootstrap CSS (Offline) -->
    <link rel="stylesheet" href="bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <style>
        .collapse{
            justify-content: flex-end;
        }
        
        .collapse span{
            color: white;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: teal;">
    <i class="fas fa-house-user"></i> <a class="navbar-brand" href="#">Dashboard</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-link"><span class="navbar-text">Welcome, <?php echo htmlspecialchars($user['email']); ?></span></li>
            <li class="nav-item">
                <a class="nav-link" href="change.php"><i class="fas fa-key"></i> Change Password</a>
            </li>
            <li class="nav-item">
                <a class="nav-link link-danger" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </li>
        </ul>
    </div>
</nav>
    <div class="container">
        <h1>Change Password</h1>
        <form method="post">
            <label for="current_password" class="form-label">Current Email</label>
            <input type="email" name="current_email" id="current_email" class="form-control" required value="<?php echo $user['email']; ?>">
            
            <label for="new_password" class="form-label mt-3">New Password</label>
            <input type="password" name="new_password" id="new_password" class="form-control" required>
            
            <label for="confirm_password" class="form-label mt-3">Confirm New Password</label>
            <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
            
            <button type="submit" name="change_password" class="btn btn-primary mt-3">Change Password</button>
        </form>
    </div>
</body>
</html>