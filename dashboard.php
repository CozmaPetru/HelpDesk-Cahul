<?php
require 'db.php';
if (!isset($_SESSION['user_id'])) {
    header(header: "Location: index.php");
    exit;
}

$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];

// Logica de preluare date (Admin vs User)
if ($role == 'admin') {
    $stmt = $pdo->query(query: "SELECT tickets.*, users.full_name FROM tickets JOIN users ON tickets.user_id = users.id ORDER BY created_at DESC");
} else {
    $stmt = $pdo->prepare(query: "SELECT * FROM tickets WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute(params: [$user_id]);
}
$tickets = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ro" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Audiowide&family=Space+Mono&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: Helvetica, sans-serif;
            background: radial-gradient(circle at top left, #1e293b, #0f172a);
            letter-spacing: 0.08rem;
        }

        .futuristic-font {
            font-family: 'Audiowide', cursive;
        }

        .glass {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>

<body class="text-slate-200 min-h-screen p-4 md:p-8">

    <nav class="flex justify-between items-center mb-10 glass p-4 rounded-2xl">
        <h1 class="futuristic-font text-2xl bg-gradient-to-r from-blue-400 to-purple-500 bg-clip-text text-transparent">
            HELPDESK CAHUL
        </h1>
        <a href="logout.php"
            class="text-sm bg-red-500/20 hover:bg-red-500/40 text-red-400 px-4 py-2 rounded-lg transition">Logout</a>
    </nav>

    <?php if ($role == 'admin'): ?>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div class="glass p-6 rounded-2xl border-2 border-blue-500">
                <p class="text-sm text-slate-400">Tichete Totale</p>
                <h2 class="text-3xl font-bold"><?= count($tickets) ?></h2>
            </div>
        </div>
    <?php endif; ?>

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl futuristic-font italic">Management Tichete</h2>
        <?php if ($role == 'user'): ?>
            <a href="create_ticket.php"
                class="bg-blue-600 hover:bg-blue-500 text-white px-6 py-2 rounded-full shadow-lg shadow-blue-500/20 transition-all">+
                NEW TICKET</a>
        <?php endif; ?>
    </div>
    </div>

        <script>
            // Custom dropdown: open/close and selection handling
            document.addEventListener('click', function (e) {
                const btn = e.target.closest('[data-dropdown-button]');
                if (btn) {
                    const menu = btn.nextElementSibling;
                    // small gap already provided by mt-2; toggle
                    document.querySelectorAll('[data-dropdown-menu]').forEach(m => { if (m !== menu) m.classList.add('hidden'); });
                    menu.classList.toggle('hidden');
                    return;
                }

                const li = e.target.closest('[data-dropdown-menu] li');
                if (li) {
                    const menu = li.closest('[data-dropdown-menu]');
                    const container = menu.parentElement;
                    const input = container.querySelector('input[type="hidden"][name="status"]');
                    const label = container.querySelector('[data-dropdown-button] span');
                    input.value = li.getAttribute('data-value');
                    label.textContent = li.textContent.trim();
                    menu.classList.add('hidden');
                    return;
                }

                // Click outside -> close all
                document.querySelectorAll('[data-dropdown-menu]').forEach(m => m.classList.add('hidden'));
            });
        </script>
    <?php if (isset($_GET['deleted'])): ?>
        <div class="mb-6 p-4 bg-green-500/20 border border-green-500/50 rounded-lg text-green-400 text-sm">
            Tichetul a fost șters cu succes!
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="mb-6 p-4 bg-red-500/20 border border-red-500/50 rounded-lg text-red-400 text-sm">
            Eroare la ștergerea tichetului!
        </div>
    <?php endif; ?>

    <div class="space-y-4">
        <?php foreach ($tickets as $t): ?>
            <div class="glass p-5 rounded-2xl hover:scale-[1.01] transition-transform duration-300">
                <div class="flex justify-between items-start mb-3">
                    <span class="text-xs px-3 py-1 rounded-full bg-slate-700 text-slate-300"><?= $t['category'] ?></span>
                    <span
                        class="font-bold <?= $t['priority'] == 'Critical' ? 'text-red-400' : 'text-yellow-400' ?>"><?= $t['priority'] ?></span>
                </div>
                <h3 class="text-lg font-bold mb-1"><?= htmlspecialchars(string: $t['title']) ?></h3>
                <p class="text-sm text-slate-400 mb-4">
                    <?= substr(string: htmlspecialchars(string: $t['description']), offset: 0, length: 100) ?>...</p>

                <div class="flex justify-between items-center border-t border-white/10 pt-4">
                    <span
                        class="text-xs text-slate-500"><?= date(format: 'd.m.Y', timestamp: strtotime(datetime: $t['created_at'])) ?></span>
                    <span
                        class="px-4 py-1 rounded-lg text-xs font-bold 
                    <?= $t['status'] == 'Open' ? 'bg-green-500/20 text-green-400' : ($t['status'] == 'In Progress' ? 'bg-blue-500/20 text-blue-400' : 'bg-slate-500/20 text-slate-400') ?>">
                        <?= $t['status'] ?>
                    </span>
                </div>
            </div>

            <?php if ($_SESSION['role'] === 'admin'): ?>
                <div class="mt-4 pt-4 border-t border-white/5">
                    <div class="flex gap-2 flex-wrap">
                        <form action="update_status.php" method="POST" class="flex items-center gap-2">
                            <input type="hidden" name="ticket_id" value="<?= $t['id'] ?>">

                            <div class="relative">
                                <button type="button" data-dropdown-button
                                    class="flex items-center justify-between bg-slate-800 text-white text-xs rounded-lg pl-2 pr-3 py-1 gap-2 leading-tight focus:outline-none focus:ring-2 focus:ring-purple-500"
                                    aria-expanded="false">
                                    <span class="truncate"><?= htmlspecialchars(string: $t['status']) ?></span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <ul data-dropdown-menu
                                    class="absolute right-0 mt-2 min-w-full bg-slate-800 rounded-lg shadow-lg z-50 hidden"
                                    style="border: none; margin-top:0.25rem;">
                                    <li data-value="Open" class="px-3 py-2 hover:bg-slate-700 cursor-pointer text-sm">Open</li>
                                    <li data-value="In Progress" class="px-3 py-2 hover:bg-slate-700 cursor-pointer text-sm">In Progress</li>
                                    <li data-value="Closed" class="px-3 py-2 hover:bg-slate-700 cursor-pointer text-sm">Closed</li>
                                </ul>

                                <input type="hidden" name="status" value="<?= htmlspecialchars(string: $t['status']) ?>">
                            </div>

                            <button type="submit"
                                class="text-[10px] bg-purple-600/20 hover:bg-purple-600 text-purple-400 hover:text-white px-3 py-1 rounded-md transition-all uppercase font-bold tracking-widest">
                                Actualizează
                            </button>
                        </form>

                        <form action="delete_ticket.php" method="POST" class="flex gap-2" onsubmit="return confirm('Sigur dorești să ștergi acest tichet? Acțiunea nu poate fi anulată!');">
                            <input type="hidden" name="ticket_id" value="<?= $t['id'] ?>">
                            <button type="submit"
                                class="text-[10px] bg-red-600/20 hover:bg-red-600 text-red-400 hover:text-white px-3 py-1 rounded-md transition-all uppercase font-bold tracking-widest">
                                Șterge
                            </button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

</body>

</html>