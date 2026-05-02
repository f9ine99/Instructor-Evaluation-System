(function () {
    function init() {
        var layout = document.getElementById('dashboardLayout');
        var btn = document.getElementById('sidebarCollapseBtn');
        if (!layout || !btn) return;

        function apply(collapsed) {
            layout.classList.toggle('sidebar--collapsed', collapsed);
            btn.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
            btn.setAttribute('title', collapsed ? 'Expand sidebar' : 'Collapse sidebar');
            try {
                localStorage.setItem('hope_dashboard_sidebar_collapsed', collapsed ? '1' : '0');
            } catch (e) {}
        }

        var saved = false;
        try {
            var v = localStorage.getItem('hope_dashboard_sidebar_collapsed');
            if (v === null && localStorage.getItem('hope_admin_sidebar_collapsed') === '1') {
                saved = true;
                localStorage.setItem('hope_dashboard_sidebar_collapsed', '1');
            } else {
                saved = v === '1';
            }
        } catch (e) {}
        apply(saved);
        btn.addEventListener('click', function () {
            apply(!layout.classList.contains('sidebar--collapsed'));
        });
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
