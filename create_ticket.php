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
        /* Prevent layout from shrinking below this width */
        html, body { min-width: 1024px; }
        
        .select-checkbox { display: none; }
        .custom-select-wrapper { position: relative; width: 100%; }
        .select-label { width: 100%; background: rgba(15, 23, 42, 0.5); border: 2px solid rgba(96, 165, 250, 0.3); border-radius: 0.5rem; padding: 0.75rem 1rem; color: #cbd5e1; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-size: 0.875rem; transition: all 0.3s; font-family: 'Space Mono', monospace; font-weight: 500; user-select: none; }
        .select-label:hover { background: rgba(15, 23, 42, 0.7); border-color: rgba(96, 165, 250, 0.5); }
        .select-label svg { width: 1.25rem; height: 1.25rem; color: #60a5fa; transition: transform 0.3s ease; }
        .select-checkbox:checked ~ .select-label svg { transform: rotate(180deg); }
        .select-checkbox:checked ~ .select-label { border-color: #60a5fa; background: rgba(15, 23, 42, 0.8); }
        .select-options { display: none; position: absolute; top: 100%; left: 0; right: 0; background: rgba(20, 30, 50, 0.98); border: 2px solid rgba(96, 165, 250, 0.3); border-top: none; border-radius: 0 0 0.5rem 0.5rem; list-style: none; padding: 0.5rem 0; margin: 0; max-height: 220px; overflow-y: auto; z-index: 10; backdrop-filter: blur(10px); }
        .select-checkbox:checked ~ .select-options { display: block; }
        .select-options li { padding: 0.9rem 1.25rem; color: #cbd5e1; cursor: pointer; transition: all 0.2s; border-left: 3px solid transparent; }
        .select-options li:hover { background: rgba(96, 165, 250, 0.15); border-left-color: #60a5fa; padding-left: 1.5rem; }
        .select-options label { display: block; width: 100%; padding: 0; margin: 0; cursor: pointer; }
    </style>
</head>
<body class="text-slate-300 min-w-384git push origin main flex items-center justify-center p-4">

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
    <div class="relative group">
        <label class="block text-xs font-bold text-blue-400 mb-2 uppercase tracking-widest ml-1">Categorie</label>
        <div class="custom-select-wrapper">
            <input type="checkbox" id="category-toggle" class="select-checkbox">
            <label for="category-toggle" class="select-label">
                <span id="category-selected">Hardware</span>
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </label>
            <ul class="select-options" id="category-options">
                <li><label for="category-toggle" data-value="Hardware" style="color: #60a5fa;">Hardware</label></li>
                <li><label for="category-toggle" data-value="Software" style="color: #10b981;">Software</label></li>
                <li><label for="category-toggle" data-value="Rețea" style="color: #f59e0b;">Rețea</label></li>
            </ul>
            <input type="hidden" name="category" id="category-input" value="Hardware">
        </div>
    </div>

    <div class="relative group">
        <label class="block text-xs font-bold text-blue-400 mb-2 uppercase tracking-widest ml-1">Prioritate</label>
        <div class="custom-select-wrapper">
            <input type="checkbox" id="priority-toggle" class="select-checkbox">
            <label for="priority-toggle" class="select-label">
                <span id="priority-selected">Low (Scăzută)</span>
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </label>
            <ul class="select-options" id="priority-options">
                <li><label for="priority-toggle" data-value="Low" style="color: #10b981;">Low (Scăzută)</label></li>
                <li><label for="priority-toggle" data-value="High" style="color: #f59e0b;">High (Ridicată)</label></li>
                <li><label for="priority-toggle" data-value="Critical" style="color: #ef4444;">Critical (Urgent!)</label></li>
            </ul>
            <input type="hidden" name="priority" id="priority-input" value="Low">
        </div>
    </div>
</div>

            <div>
                <label class="block text-xs font-bold text-blue-400 mb-2 uppercase">Descriere Detaliată</label>
                <textarea name="description" id="description-textarea" rows="4" required placeholder="Descrie aici problema tehnică..."
                    class="w-full bg-slate-900/50 border border-white/10 rounded-xl px-4 py-3 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition resize-none overflow-hidden"></textarea>
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

    <script>
        // Close other dropdown when one opens
        const categoryToggle = document.getElementById('category-toggle');
        const priorityToggle = document.getElementById('priority-toggle');
        
        categoryToggle.addEventListener('change', () => {
            if (categoryToggle.checked) {
                priorityToggle.checked = false;
            }
        });
        
        priorityToggle.addEventListener('change', () => {
            if (priorityToggle.checked) {
                categoryToggle.checked = false;
            }
        });
        
        // Handle option selection with CSS checkbox
        document.querySelectorAll('[data-value]').forEach(label => {
            label.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                
                const value = label.getAttribute('data-value');
                const wrapper = label.closest('.custom-select-wrapper');
                const isCategory = wrapper.querySelector('#category-input');
                const isPriority = wrapper.querySelector('#priority-input');
                
                if (isCategory) {
                    document.getElementById('category-input').value = value;
                    document.getElementById('category-selected').textContent = label.textContent;
                    setTimeout(() => {
                        document.getElementById('category-toggle').checked = false;
                    }, 0);
                } else if (isPriority) {
                    document.getElementById('priority-input').value = value;
                    document.getElementById('priority-selected').textContent = label.textContent;
                    setTimeout(() => {
                        document.getElementById('priority-toggle').checked = false;
                    }, 0);
                }
            });
        });
        
        // Close dropdowns when clicking outside
        document.addEventListener('click', (e) => {
            const categoryWrapper = document.querySelector('.custom-select-wrapper #category-toggle').closest('.custom-select-wrapper');
            const priorityWrapper = document.querySelector('.custom-select-wrapper #priority-toggle').closest('.custom-select-wrapper');
            
            // Check if click is outside both wrappers
            if (!categoryWrapper.contains(e.target)) {
                categoryToggle.checked = false;
            }
            if (!priorityWrapper.contains(e.target)) {
                priorityToggle.checked = false;
            }
        });
        
        // Auto-expand textarea based on content
        const textarea = document.getElementById('description-textarea');
        textarea.addEventListener('input', () => {
            textarea.style.height = 'auto';
            textarea.style.height = (textarea.scrollHeight) + 'px';
        });
        
        // Set initial height
        textarea.style.height = (textarea.scrollHeight) + 'px';
    </script>

</body>
</html>