<?php
require 'db.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];

// Logica de preluare date
if ($role == 'admin') {
    $stmt = $pdo->query("SELECT tickets.*, users.full_name FROM tickets JOIN users ON tickets.user_id = users.id ORDER BY created_at DESC");
} else {
    $stmt = $pdo->prepare("SELECT * FROM tickets WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
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

        .futuristic-font { font-family: 'Audiowide', cursive; }

        .glass {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* FIX PENTRU BUG-UL DE SUPRAPUNERE (Z-INDEX) */
        .ticket-card {
            position: relative;
            z-index: 10;
            transition: all 0.3s ease;
        }

        /* Când un card este sub mouse sau are un meniu deschis, trece deasupra celorlalte */
        .ticket-card:hover, 
        .ticket-card:focus-within {
            z-index: 50;
        }

        .modal-border {
            border: 2px solid #2563eb;
            box-shadow: 0 0 25px rgba(37, 99, 235, 0.3);
        }
    </style>
</head>

<body class="text-slate-200 p-4 md:p-8 min-w-384">

    <nav class="flex justify-between items-center mb-10 glass p-4 rounded-2xl">
        <h1 class="futuristic-font text-2xl bg-gradient-to-r from-blue-400 to-purple-500 bg-clip-text text-transparent">
            HELPDESK CAHUL
        </h1>
        <a href="logout.php" class="text-sm bg-red-500/20 hover:bg-red-500/40 text-red-400 px-4 py-2 rounded-lg transition">Logout</a>
    </nav>

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl futuristic-font italic">Management Tichete</h2>
        <?php if ($role == 'user'): ?>
            <a href="create_ticket.php" class="bg-blue-600 hover:bg-blue-500 text-white px-6 py-2 rounded-full shadow-lg shadow-blue-500/20 transition-all">+ NEW TICKET</a>
        <?php endif; ?>
    </div>

    <div class="space-y-4">
        <?php foreach ($tickets as $t): ?>
            <div class="glass p-5 rounded-2xl ticket-card">
                <div class="flex justify-between items-start mb-3">
                    <span class="text-xs px-3 py-1 rounded-full bg-slate-700 text-slate-300"><?= $t['category'] ?></span>
                    <span class="font-bold <?= $t['priority'] == 'Critical' ? 'text-red-400' : 'text-yellow-400' ?>"><?= $t['priority'] ?></span>
                </div>
                
                <h3 class="text-lg font-bold mb-1"><?= htmlspecialchars($t['title']) ?></h3>
                
                <div class="flex justify-between items-center border-t border-white/10 pt-4 mt-4">
                    <span class="text-xs text-slate-500 italic">
                        <?= date('d.m.Y H:i', strtotime($t['created_at'])) ?>
                    </span>
                    
                    <div class="flex items-center gap-3">
                        <button onclick='openTicketModal(<?= json_encode($t) ?>)' 
                                class="text-[10px] bg-blue-600/20 hover:bg-blue-600 text-blue-400 hover:text-white px-3 py-1 rounded-md transition-all uppercase font-bold tracking-widest">
                            Detalii
                        </button>

                        <span class="px-4 py-1 rounded-lg text-xs font-bold <?= $t['status'] == 'Open' ? 'bg-green-500/20 text-green-400' : ($t['status'] == 'In Progress' ? 'bg-yellow-500/20 text-yellow-400' : 'bg-slate-500/20 text-slate-400') ?>">
                            <?= $t['status'] ?>
                        </span>
                    </div>
                </div>

                <?php if ($role === 'admin'): ?>
                    <div class="mt-4 pt-4 border-t border-white/5 flex gap-2 flex-wrap">
                        <form action="update_status.php" method="POST" class="flex items-center gap-2">
                            <input type="hidden" name="ticket_id" value="<?= $t['id'] ?>">
                            <div class="relative">
                                <button type="button" data-dropdown-button class="flex items-center justify-between bg-slate-800 text-white text-xs rounded-lg pl-3 pr-3 py-1 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    <span><?= htmlspecialchars($t['status']) ?></span>
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                                <ul data-dropdown-menu class="absolute left-0 mt-2 min-w-full bg-slate-900 border border-white/10 rounded-lg shadow-2xl z-[100] hidden overflow-hidden">
                                    <li data-value="Open" class="px-3 py-2 hover:bg-blue-600 cursor-pointer text-xs">Open</li>
                                    <li data-value="In Progress" class="px-3 py-2 hover:bg-blue-600 cursor-pointer text-xs">In Progress</li>
                                    <li data-value="Closed" class="px-3 py-2 hover:bg-blue-600 cursor-pointer text-xs">Closed</li>
                                </ul>
                                <input type="hidden" name="status" value="<?= $t['status'] ?>">
                            </div>
                            <button type="submit" class="text-[10px] bg-purple-600/20 hover:bg-purple-600 text-purple-400 hover:text-white px-3 py-1 rounded-md transition-all uppercase font-bold tracking-widest">Actualizează</button>
                        </form>

                        <form action="delete_ticket.php" method="POST" onsubmit="return confirm('Sigur ștergi tichetul?');">
                            <input type="hidden" name="ticket_id" value="<?= $t['id'] ?>">
                            <button type="submit" class="text-[10px] bg-red-600/20 hover:bg-red-600 text-red-400 hover:text-white px-3 py-1 rounded-md transition-all uppercase font-bold tracking-widest">Șterge</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div id="ticketModal" class="fixed inset-0 z-[200] flex items-center justify-center bg-black/80 backdrop-blur-md hidden p-4">
        <div class="glass max-w-xl w-full p-8 rounded-3xl modal-border relative animate-in fade-in zoom-in duration-300">
            <button onclick="closeModal()" class="absolute top-4 right-6 text-slate-400 hover:text-white text-2xl">&times;</button>
            
            <h2 id="modalTitle" class="futuristic-font text-lg mb-6 text-blue-400 leading-tight"></h2>
            
            <div class="bg-white/5 p-6 rounded-2xl mb-6 border border-white/10">
                <p id="modalDesc" class="text-slate-300 text-sm leading-relaxed"></p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="p-4 bg-white/5 rounded-xl border border-white/5 text-center">
                    <p class="text-[10px] uppercase text-slate-500 mb-1">Categorie</p>
                    <p id="modalCat" class="text-base font-bold text-slate-200"></p>
                </div>
                <div class="p-4 bg-white/5 rounded-xl border border-white/5 text-center">
                    <p class="text-[10px] uppercase text-slate-500 mb-1">Prioritate</p>
                    <p id="modalPrio" class="text-base font-bold"></p>
                </div>
            </div>
            
            <button onclick="closeModal()" class="mt-8 w-full py-3 bg-blue-600/20 hover:bg-blue-600 text-white rounded-xl transition-all futuristic-font text-[15px] tracking-[.25em] border border-blue-500/30">
                ÎNCHIDE
            </button>
        </div>
    </div>

    <script>
        // Logica Modal
        function openTicketModal(ticket) {
            document.getElementById('modalTitle').innerText = ticket.title;
            document.getElementById('modalDesc').innerText = ticket.description;
            document.getElementById('modalCat').innerText = ticket.category;
            
            const prio = document.getElementById('modalPrio');
            prio.innerText = ticket.priority;
            prio.className = "text-sm font-bold " + (ticket.priority === 'Critical' ? 'text-red-400' : 'text-yellow-400');
            
            document.getElementById('ticketModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('ticketModal').classList.add('hidden');
        }

        // Inchidere modal la click pe fundal
        window.onclick = function(event) {
            const modal = document.getElementById('ticketModal');
            if (event.target == modal) closeModal();
        }

        // Logica Dropdown
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('[data-dropdown-button]');
            if (btn) {
                const menu = btn.nextElementSibling;
                document.querySelectorAll('[data-dropdown-menu]').forEach(m => { if (m !== menu) m.classList.add('hidden'); });
                menu.classList.toggle('hidden');
                return;
            }

            const li = e.target.closest('[data-dropdown-menu] li');
            if (li) {
                const menu = li.closest('[data-dropdown-menu]');
                const container = menu.parentElement;
                container.querySelector('input[type="hidden"][name="status"]').value = li.getAttribute('data-value');
                container.querySelector('[data-dropdown-button] span').textContent = li.textContent.trim();
                menu.classList.add('hidden');
            } else {
                document.querySelectorAll('[data-dropdown-menu]').forEach(m => m.classList.add('hidden'));
            }
        });
    </script>
</body>
</html>