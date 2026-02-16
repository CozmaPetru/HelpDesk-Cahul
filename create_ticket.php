<?php
require 'db.php';
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }
?>
<!DOCTYPE html>
<html lang="ro" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Ticket | HelpDesk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Audiowide&family=Space+Mono&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Space Mono', monospace; background: #0f172a; }
        .futuristic { font-family: 'Audiowide', cursive; }
        .glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); }
    </style>
</head>
<body class="text-slate-300 min-h-screen flex items-center justify-center p-4">

    <div class="glass p-8 rounded-3xl w-full max-w-2xl shadow-2xl border-t-2 border-blue-500/50">
        <div class="mb-8">
            <h1 class="futuristic text-2xl text-white bg-gradient-to-r from-blue-400 to-cyan-400 bg-clip-text text-transparent">
                ADAUGĂ TICHTET NOU
            </h1>
            <p class="text-xs text-slate-500 mt-2 uppercase tracking-widest text-center md:text-left">Completează detaliile incidentului IT</p>
        </div>

        <form action="save_ticket.php" method="POST" class="space-y-6">
            <div>
                <label class="block text-xs font-bold text-blue-400 mb-2 uppercase">Titlu Problemă</label>
                <input type="text" name="title" required placeholder="ex: Nu funcționează imprimanta..." 
                    class="w-full bg-slate-900/50 border border-white/10 rounded-xl px-4 py-3 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-blue-400 mb-2 uppercase">Categorie</label>
                    <select name="category" class="w-full bg-slate-900/50 border border-white/10 rounded-xl px-4 py-3 focus:outline-none focus:border-blue-500">
                        <option value="Hardware">Hardware</option>
                        <option value="Software">Software</option>
                        <option value="Rețea">Rețea</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-blue-400 mb-2 uppercase">Prioritate</label>
                    <select name="priority" class="w-full bg-slate-900/50 border border-white/10 rounded-xl px-4 py-3 focus:outline-none focus:border-blue-500">
                        <option value="Low">Low (Scăzută)</option>
                        <option value="High">High (Ridicată)</option>
                        <option value="Critical">Critical (Urgent!)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-blue-400 mb-2 uppercase">Descriere Detaliată</label>
                <textarea name="description" rows="4" required placeholder="Descrie aici problema tehnică..."
                    class="w-full bg-slate-900/50 border border-white/10 rounded-xl px-4 py-3 focus:outline-none focus:border-blue-500 transition"></textarea>
            </div>

            <div class="flex flex-col md:flex-row gap-4 pt-4">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-500 text-white font-bold py-4 rounded-xl shadow-lg shadow-blue-500/20 transition-all uppercase tracking-widest">
                    Trimite Tichet
                </button>
                <a href="dashboard.php" class="flex-1 bg-slate-800 hover:bg-slate-700 text-center py-4 rounded-xl font-bold uppercase tracking-widest transition">
                    Anulează
                </a>
            </div>
        </form>
    </div>

</body>
</html>