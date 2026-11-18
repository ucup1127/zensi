// Placeholder — nanti diisi per modul
document.addEventListener('DOMContentLoaded', () => {
    // Auto-hide alert setelah 5 detik
    document.querySelectorAll('.alert').forEach(el => {
        setTimeout(() => {
            el.style.transition = 'opacity .4s';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 400);
        }, 5000);
    });
});