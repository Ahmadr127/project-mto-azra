/**
 * Toast.js - Minimal stub untuk layouts/app.blade.php:455
 * Dipakai oleh inline script di app.blade.php:458-469 untuk session('success')/('error')/$errors
 * Tidak butuh Vite/build, murni vanilla JS + Tailwind via CDN.
 */
(function () {
    function ensureContainer() {
        let c = document.getElementById('toast-container');
        if (!c) {
            c = document.createElement('div');
            c.id = 'toast-container';
            c.className = 'fixed top-4 right-4 z-[9999] flex flex-col gap-2 pointer-events-none';
            document.body.appendChild(c);
        }
        return c;
    }

    function show(message, type, opts) {
        opts = opts || {};
        const duration = opts.duration || (type === 'error' ? 5000 : 3500);
        const container = ensureContainer();
        const el = document.createElement('div');
        const bg = type === 'success' ? 'bg-green-600' : type === 'error' ? 'bg-red-600' : 'bg-gray-800';
        el.className = bg + ' text-white px-4 py-3 rounded-lg shadow-lg text-sm font-medium pointer-events-auto flex items-start gap-3 max-w-sm transition-all duration-200';
        el.style.opacity = '0';
        el.style.transform = 'translateY(-8px)';
        el.innerHTML = '<span class="flex-1">' + escapeHtml(String(message)) + '</span><button type="button" class="ml-2 -mr-1 -my-1 p-1 opacity-70 hover:opacity-100" aria-label="Close">&times;</button>';
        const btn = el.querySelector('button');
        if (btn) btn.addEventListener('click', function () { dismiss(); });
        container.appendChild(el);
        requestAnimationFrame(function () {
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        });
        let t = setTimeout(dismiss, duration);
        el.addEventListener('mouseenter', function () { clearTimeout(t); });
        el.addEventListener('mouseleave', function () { t = setTimeout(dismiss, 1200); });
        function dismiss() {
            clearTimeout(t);
            el.style.opacity = '0';
            el.style.transform = 'translateY(-8px)';
            setTimeout(function () { if (el.parentNode) el.parentNode.removeChild(el); }, 200);
        }
    }

    function escapeHtml(s) {
        return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    window.Toast = {
        success: function (msg, opts) { show(msg, 'success', opts); },
        error: function (msg, opts) { show(msg, 'error', opts); },
        show: show
    };
})();
