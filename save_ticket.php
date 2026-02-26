<?php
require 'db.php';
require 'notify.php';

// Verificăm dacă utilizatorul este logat
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $category = $_POST['category'] ?? '';
    $priority = $_POST['priority'] ?? '';

    // Inserăm în baza de date
    $stmt = $pdo->prepare("
        INSERT INTO tickets (user_id, title, description, category, priority, status)
        VALUES (?, ?, ?, ?, ?, 'Open')
    ");

    if ($stmt->execute([$user_id, $title, $description, $category, $priority])) {

        // ✅ Notificare admin (email fix)
        $adminEmail = "helpdesk.cahuk@gmail.com";

        $subject = "Ticket nou în HelpDesk";
        $body = "
            <h3>Ticket nou creat</h3>
            <p><b>Titlu:</b> " . htmlspecialchars($title) . "</p>
            <p><b>Creat de:</b> " . htmlspecialchars($_SESSION['name'] ?? 'User') . "</p>
            <p>Intră în dashboard pentru detalii.</p>
        ";

        sendMailSimple($adminEmail, $subject, $body);

        // Redirect
        header("Location: dashboard.php?status=success");
        exit;

    } else {
        echo "A apărut o eroare la salvarea tichetului.";
    }
}