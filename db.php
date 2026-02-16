<?php
session_start();

$host = 'localhost';
$db   = 'helpdesk_cahul';
$user = 'root';
$pass = ''; // Default XAMPP
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO(dsn: $dsn, username: $user, password: $pass, options: $options);
} catch (\PDOException $e) {
     throw new \PDOException(message: $e->getMessage(), code: (int)$e->getCode());
}

// Funcție pentru simulare email
function logEmail($to, $subject, $message) {
    $log = "[" . date('Y-m-d H:i:s') . "] Email către $to | Subiect: $subject | Mesaj: $message" . PHP_EOL;
    file_put_contents('email_log.txt', $log, FILE_APPEND);
}
?>

