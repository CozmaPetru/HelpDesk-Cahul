<?php
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$filter_priority = $_GET['priority'] ?? '';
$filter_status = $_GET['status'] ?? '';
$filter_today = isset($_GET['today']);

// Folosim alias-ul 't' pentru tickets și 'u' pentru users
$query = "SELECT t.*, u.full_name 
          FROM tickets t 
          JOIN users u ON t.user_id = u.id 
          WHERE 1=1";
$params = [];

if ($filter_priority) {
    $query .= " AND t.priority = ?";
    $params[] = $filter_priority;
}
if ($filter_status) {
    $query .= " AND t.status = ?";
    $params[] = $filter_status;
}

// Filtrare pentru data curentă (AZI)
if ($filter_today) {
    $query .= " AND DATE(t.created_at) = CURDATE()";
}

$query .= " ORDER BY t.created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$tickets = $stmt->fetchAll();

// Statistici globale (folosind CURDATE() pentru a identifica tichetele de azi)
$stats_today = $pdo->query("SELECT COUNT(*) FROM tickets WHERE DATE(created_at) = CURDATE()")->fetchColumn();
$stats_critical = $pdo->query("SELECT COUNT(*) FROM tickets WHERE priority = 'Critical' AND status != 'Closed'")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="ro" class="dark">
<head>
    <meta charset="UTF-8">
    <title>Control Panel Admin | HelpDesk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background: #0f172a; color: #e2e8f0; font-family: sans-serif; }
        .glass { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1); }
    </style>
</head>
<body class="p-6 md:p-12">
    <div class="max-w-6xl mx-auto">
        
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-white">Rapoarte Avansate</h1>
                <p class="text-slate-400">Filtrează și analizează performanța tichetelelor</p>
            </div>
            <a href="dashboard.php" class="text-sm bg-slate-800 hover:bg-slate-700 px-4 py-2 rounded-lg border border-slate-600 transition-all">
                ← Înapoi la Dashboard
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="glass p-6 rounded-2xl border-2 border-blue-500">
                <span class="text-slate-400 text-sm font-bold uppercase">Deschise Azi</span>
                <h2 class="text-4xl font-bold mt-1"><?= $stats_today ?></h2>
            </div>
            <div class="glass p-6 rounded-2xl border-2 border-red-500">
                <span class="text-slate-400 text-sm font-bold uppercase">Critice Nerezolvate</span>
                <h2 class="text-4xl font-bold mt-1 text-red-500"><?= $stats_critical ?></h2>
            </div>
        </div>

        <div class="glass p-6 rounded-2xl mb-8 relative z-10">
    <form method="GET" id="filterForm" class="flex flex-wrap items-end gap-6">
        
        <div class="flex-1 min-w-[220px] relative">
            <label class="block text-[10px] font-bold text-slate-500 mb-2 uppercase tracking-widest">Prioritate</label>
            <div class="relative custom-dropdown">
                <button type="button" data-dropdown-button class="w-full bg-slate-900 border border-white/10 p-3 rounded-lg text-white text-sm flex justify-between items-center hover:border-blue-500/50 transition-all">
                    <span><?= $filter_priority ?: 'Toate Prioritățile' ?></span>
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <input type="hidden" name="priority" value="<?= htmlspecialchars($filter_priority) ?>">
                <ul data-dropdown-menu class="absolute left-0 mt-2 min-w-full bg-slate-900 border border-white/10 rounded-lg shadow-2xl z-[9999] hidden overflow-hidden">
                    <li data-value="Low" class="px-3 py-2 hover:bg-blue-600 cursor-pointer text-sm">Low</li>
                    <li data-value="High" class="px-3 py-2 hover:bg-blue-600 cursor-pointer text-sm">High</li>
                    <li data-value="Critical" class="px-3 py-2 hover:bg-blue-600 cursor-pointer text-sm">Critical</li>
                </ul>
            </div>
        </div>

        <div class="flex-1 min-w-[220px] relative">
            <label class="block text-[10px] font-bold text-slate-500 mb-2 uppercase tracking-widest">Status</label>
            <div class="relative custom-dropdown">
                <button type="button" data-dropdown-button class="w-full bg-slate-900 border border-white/10 p-3 rounded-lg text-white text-sm flex justify-between items-center hover:border-blue-500/50 transition-all">
                    <span><?= $filter_status ?: 'Toate Statusurile' ?></span>
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <input type="hidden" name="status" value="<?= htmlspecialchars($filter_status) ?>">
                <ul data-dropdown-menu class="absolute left-0 mt-2 min-w-full bg-slate-900 border border-white/10 rounded-lg shadow-2xl z-[9999] hidden overflow-hidden">
                    <li data-value="Open" class="px-3 py-2 hover:bg-blue-600 cursor-pointer text-sm">Open</li>
                    <li data-value="In Progress" class="px-3 py-2 hover:bg-blue-600 cursor-pointer text-sm">In Progress</li>
                    <li data-value="Closed" class="px-3 py-2 hover:bg-blue-600 cursor-pointer text-sm">Closed</li>
                </ul>
            </div>
        </div>

        <div class="flex items-center gap-3 h-[42px] px-2">
            <input type="checkbox" name="today" id="today" <?= $filter_today ? 'checked' : '' ?> 
                class="w-4 h-4 rounded border-white/10 bg-slate-900 text-blue-600 focus:ring-0 focus:ring-offset-0 transition-all cursor-pointer">
            <label for="today" class="text-xs font-bold text-slate-400 uppercase tracking-tighter cursor-pointer select-none">Doar azi</label>
        </div>

        <div class="flex items-center gap-4 mb-1">
            <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white px-8 py-3 rounded-lg text-xs font-bold uppercase tracking-widest transition-all shadow-lg shadow-blue-500/20">
                Filtrează
            </button>
            <a href="admin_stats.php" class="text-[10px] font-bold text-slate-500 hover:text-white uppercase tracking-widest transition-all">
                Resetează
            </a>
        </div>
    </form>
</div>

        <div class="glass rounded-2xl overflow-hidden shadow-2xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-800/50">
                            <th class="p-4 text-xs font-bold text-slate-400 uppercase">Utilizator</th>
                            <th class="p-4 text-xs font-bold text-slate-400 uppercase">Tichet</th>
                            <th class="p-4 text-xs font-bold text-slate-400 uppercase">Prioritate</th>
                            <th class="p-4 text-xs font-bold text-slate-400 uppercase">Status</th>
                            <th class="p-4 text-xs font-bold text-slate-400 uppercase text-right">Dată Creare</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700">
                        <?php foreach ($tickets as $t): ?>
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="p-4">
                                <span class="font-bold text-blue-400"><?= htmlspecialchars($t['full_name']) ?></span>
                            </td>
                            <td class="p-4">
                                <div class="text-white font-medium"><?= htmlspecialchars($t['title']) ?></div>
                                <div class="text-xs text-slate-500 truncate max-w-[200px]"><?= htmlspecialchars($t['description']) ?></div>
                            </td>
                            <td class="p-4">
                                <span class="px-2 py-1 rounded-md text-[10px] font-bold uppercase <?= $t['priority'] == 'Critical' ? 'bg-red-500/20 text-red-500 border border-red-500/50' : 'bg-slate-700 text-slate-300' ?>">
                                    <?= $t['priority'] ?>
                                </span>
                            </td>
                            <td class="p-4">
                                <span class="text-sm"><?= $t['status'] ?></span>
                            </td>
                            <td class="p-4 text-right text-xs text-slate-500">
                                <?= date('d M Y, H:i', strtotime($t['created_at'])) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php if (empty($tickets)): ?>
                <div class="p-12 text-center text-slate-500 italic">
                    Niciun tichet nu corespunde filtrelor selectate.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
    document.addEventListener('click', function (e) {
        // Deschiderea/Închiderea dropdown-ului la click pe buton
        const btn = e.target.closest('[data-dropdown-button]');
        if (btn) {
            const menu = btn.nextElementSibling.nextElementSibling; // peste input-ul hidden
            document.querySelectorAll('[data-dropdown-menu]').forEach(m => { 
                if (m !== menu) m.classList.add('hidden'); 
            });
            menu.classList.toggle('hidden');
            return;
        }

        // Selectarea unei valori din listă
        const li = e.target.closest('[data-dropdown-menu] li');
        if (li) {
            const menu = li.closest('[data-dropdown-menu]');
            const container = menu.parentElement;
            const input = container.querySelector('input[type="hidden"]');
            const span = container.querySelector('[data-dropdown-button] span');
            
            input.value = li.getAttribute('data-value');
            span.textContent = li.textContent.trim();
            menu.classList.add('hidden');
        } else {
            // Închide toate dropdown-urile dacă dai click în altă parte
            document.querySelectorAll('[data-dropdown-menu]').forEach(m => m.classList.add('hidden'));
        }
    });
</script>
</body>
</html>