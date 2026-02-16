<?php
require 'db.php';

// Verificăm dacă utilizatorul este logat
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $priority = $_POST['priority'];

    // Inserăm în baza de date
    $stmt = $pdo->prepare("INSERT INTO tickets (user_id, title, description, category, priority, status) VALUES (?, ?, ?, ?, ?, 'Open')");
    
    if ($stmt->execute([$user_id, $title, $description, $category, $priority])) {
        // Redirecționăm la dashboard cu un mesaj de succes
        header("Location: dashboard.php?status=success");
        exit;
    } else {
        echo "A apărut o eroare la salvarea tichetului.";
    }
}