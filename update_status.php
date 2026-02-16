<?php
require 'db.php';

// 1. Securitate: Verificăm dacă utilizatorul este logat și dacă este ADMIN
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Acces neautorizat!");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticket_id = $_POST['ticket_id'];
    $new_status = $_POST['status'];

    // 2. Actualizăm statusul în baza de date
    $stmt = $pdo->prepare("UPDATE tickets SET status = ? WHERE id = ?");
    
    if ($stmt->execute([$new_status, $ticket_id])) {
        
        // 3. Preluăm datele utilizatorului pentru "notificarea prin email"
        $userStmt = $pdo->prepare("
            SELECT u.email, u.full_name, t.title 
            FROM tickets t 
            JOIN users u ON t.user_id = u.id 
            WHERE t.id = ?
        ");
        $userStmt->execute([$ticket_id]);
        $data = $userStmt->fetch();

        // 4. Simulăm trimiterea email-ului în email_log.txt
        $message = "Salut {$data['full_name']}, statusul tichetului tău ('{$data['title']}') a fost actualizat în: {$new_status}.";
        logEmail($data['email'], "Actualizare Tichet #$ticket_id", $message);

        // 5. Redirecționare înapoi la dashboard cu succes
        header("Location: dashboard.php?msg=updated");
        exit;
    }
}