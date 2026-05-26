<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
(function () {

    // ── Clock ──
    function updateClock() {
        var el = document.getElementById('topbarClock');
        if (!el) return;
        el.textContent = new Date().toLocaleTimeString([], {
            hour: '2-digit', minute: '2-digit', second: '2-digit'
        });
    }
    updateClock();
    setInterval(updateClock, 1000);

    // ── Sidebar Toggle ──
    var sidebar     = document.getElementById('sidebar');
    var topbar      = document.getElementById('topbar');
    var mainContent = document.getElementById('main-content');
    var toggleBtn   = document.getElementById('sidebarToggle');

    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', function () {
            sidebar.classList.toggle('collapsed');
            if (topbar)      topbar.classList.toggle('expanded');
            if (mainContent) mainContent.classList.toggle('expanded');
        });
    }

    // ── Auto-dismiss alerts after 4s ──
    setTimeout(function () {
        $('.alert').fadeOut('slow');
    }, 4000);

})();
</script>