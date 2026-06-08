import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mobileSidebar = document.getElementById('mobileSidebar');
    const sidebarBackdrop = document.getElementById('sidebarBackdrop');
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const mobileMenu = document.getElementById('mobileMenu');

    const toggleSidebar = () => {
        if (!mobileSidebar || !sidebarBackdrop) {
            return;
        }

        mobileSidebar.classList.toggle('-translate-x-full');
        sidebarBackdrop.classList.toggle('opacity-0');
        sidebarBackdrop.classList.toggle('pointer-events-none');
    };

    sidebarToggle?.addEventListener('click', toggleSidebar);
    sidebarBackdrop?.addEventListener('click', toggleSidebar);

    mobileMenuToggle?.addEventListener('click', () => mobileMenu?.classList.toggle('hidden'));

    const mainContent = document.querySelector('main');
    if (mainContent) {
        mainContent.classList.remove('opacity-0');
    }

    document.querySelectorAll('a[href^="/"]:not([target="_blank"])').forEach(link => {
        if (!link.dataset.noTransition) {
            link.addEventListener('click', event => {
                const href = link.getAttribute('href');
                if (!href || href.startsWith('#') || href.startsWith('mailto:') || href.startsWith('tel:')) {
                    return;
                }

                const sameOrigin = link.origin === window.location.origin;
                const local = sameOrigin && link.href !== window.location.href;

                if (!local) {
                    return;
                }

                event.preventDefault();
                document.body.classList.add('page-exit');
                setTimeout(() => window.location.href = href, 220);
            });
        }
    });
});
