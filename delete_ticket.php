<?php
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Verifică dacă utilizatorul este admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ticket_id'])) {
    $ticket_id = intval($_POST['ticket_id']);
    
    try {
        // Șterge tichetu din baza de date
        $stmt = $pdo->prepare("DELETE FROM tickets WHERE id = ?");
        $stmt->execute([$ticket_id]);
        
        // Redirecționează înapoi la dashboard
        header("Location: dashboard.php?deleted=1");
        exit;
    } catch (Exception $e) {
        // În caz de eroare, redirecționează cu mesaj de eroare
        header("Location: dashboard.php?error=1");
        exit;
    }
} else {
    // Acces neautorizat, redirecționează la dashboard
    header("Location: dashboard.php");
    exit;
}
?>
