document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-dismiss]').forEach((button) => {
        button.addEventListener('click', () => button.closest('[data-flash]')?.remove());
    });

    const sidebar = document.getElementById('sidebar');
    document.querySelectorAll('[data-sidebar-toggle]').forEach((button) => {
        button.addEventListener('click', () => sidebar?.classList.toggle('-translate-x-full'));
    });
});
