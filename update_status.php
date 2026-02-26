<?php
require 'db.php';
require 'notify.php';

// Doar admin
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: dashboard.php");
    exit;
}

$ticket_id   = $_POST['ticket_id'] ?? null;
$new_status  = $_POST['status'] ?? null;

if (!$ticket_id || !$new_status) {
    header("Location: dashboard.php?status=missing");
    exit;
}

// 1) Update status
$upd = $pdo->prepare("UPDATE tickets SET status = ? WHERE id = ?");
if (!$upd->execute([$new_status, $ticket_id])) {
    header("Location: dashboard.php?status=update_failed");
    exit;
}

// 2) Luăm datele userului (email) + titlu ticket
$info = $pdo->prepare("
    SELECT u.email, u.full_name, t.title, t.status
    FROM tickets t
    JOIN users u ON u.id = t.user_id
    WHERE t.id = ?
");
$info->execute([$ticket_id]);
$row = $info->fetch(PDO::FETCH_ASSOC);

// 3) Trimitem email doar dacă am găsit ticket + user
if ($row && !empty($row['email'])) {

    $subject = "Status actualizat: " . $row['title'];

    $body = "
        <h3>Status ticket actualizat</h3>
        <p>Salut, <b>" . htmlspecialchars($row['full_name'] ?? 'User') . "</b>!</p>
        <p><b>Ticket:</b> " . htmlspecialchars($row['title']) . "</p>
        <p><b>Status nou:</b> " . htmlspecialchars($row['status']) . "</p>
        <p>Intră în cont pentru detalii.</p>
    ";

    // email către user (ex petru2511@gmail.com din DB)
    sendMailSimple($row['email'], $subject, $body);
}

// 4) Redirect
header("Location: dashboard.php?status=updated");
exit;