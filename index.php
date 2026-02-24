<?php
include 'db.php';
if (isset($_SESSION['user_id'])) header(header: "Location: dashboard.php");

$error = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $pass = $_POST['password'];

    $stmt = $pdo->prepare(query: "SELECT * FROM users WHERE email = ?");
    $stmt->execute(params: [$email]);
    $user = $stmt->fetch();

    if ($user && password_verify(password: $pass, hash: $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['full_name'];
        header(header: "Location: dashboard.php");
    } else {
        $error = "Date de logare incorecte!";
    }
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Audiowide&family=Space+Mono&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Helvetica', sans-serif; background: radial-gradient(circle at top left, #1e293b, #0f172a); }
        .font-futuristic { font-family: 'Audiowide', cursive; }
        .glass { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); }
        /* Prevent layout from shrinking below Tailwind 'sm' breakpoint (640px) */
        html, body { min-width: 640px; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="glass p-8 rounded-2xl w-full max-w-md shadow-2xl">
        <h1 class="font-futuristic text-3xl text-blue-400 text-center mb-8 tracking-wider">HELPDESK CAHUL</h1>
        <?php if($error): ?>
            <div class="bg-red-500/20 text-red-300 p-3 rounded mb-4 text-sm text-center border border-red-500/50"><?= $error ?></div>
        <?php endif; ?>
        <form method="POST" class="space-y-6">
            <div>
                <label class="block text-gray-400 text-sm mb-2">Email Access</label>
                <input type="email" name="email" required class="w-full bg-slate-800/50 border border-slate-700 p-3 rounded-lg text-white focus:outline-none focus:border-blue-500 transition-all">
            </div>
            <div>
                <label class="block text-gray-400 text-sm mb-2">Password</label>
                <input type="password" name="password" required class="w-full bg-slate-800/50 border border-slate-700 p-3 rounded-lg text-white focus:outline-none focus:border-blue-500 transition-all">
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 rounded-lg shadow-lg shadow-blue-500/20 transition-all transform hover:scale-105 uppercase tracking-widest">Authorize</button>
        </form>
    </div>
</body>
</html>