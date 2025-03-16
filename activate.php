<?php
require_once 'Database.php';

if (isset($_GET['code'])) {
    $code = $_GET['code'];

    $stmt = $pdo->prepare("UPDATE users SET is_active = 1 WHERE activation_code = ?");
    $stmt->bindValue(1, $code, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo "Account activated successfully! You can now login.";
    } else {
        echo "Invalid activation link.";
    }
}
?>
