<?php
session_start();
require_once 'Database.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['user'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bindValue(1, $email);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "User not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <!-- Bootstrap CSS (Offline) -->
    <link rel="stylesheet" href="bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#userTable').DataTable();
        });
    </script>
    <style>
        .collapse{
            justify-content: flex-end;
        }
        .collapse a{
            color: red;
        }
        .collapse span{
            color: white;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: teal;">
        <a class="navbar-brand" href="#">Dashboard</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
        <span class="navbar-text">Welcome, <?php echo htmlspecialchars($user['email']); ?></span>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="container mt-5">
        
    <table id="userTable" class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Email</th>
                <th>Activation Code</th>
                <th>Is Active</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = $pdo->prepare("SELECT id, email,  activation_code, is_active, created_at FROM users");
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                ?>
                <tr>
                    <td><?php echo $row['id'] ?></td>
                    <td><?php echo $row['email'] ?></td>
                    <td><?php echo $row['activation_code'] ?></td>
                    <td><?php echo $row['is_active'] == 1 ? 'Is verified' : 'Pending to verify'; ?></td>
                    <td><?php echo $row['created_at'] ?></td>
                </tr>
        <?php } ?>
        </tbody>
    </table>
    </div> 
    <!-- Bootstrap JS (Offline) -->
    <script src="bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>