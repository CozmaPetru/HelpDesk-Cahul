<?php
require 'db.php';
$pass = password_hash("1234", PASSWORD_BCRYPT);
$stmt = $pdo->prepare("UPDATE users SET password = ?");
$stmt->execute([$pass]);
echo "Parolele au fost resetate local la '1234' cu hash-ul corect generat de acest sistem.";
?>