document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-chart]').forEach(el => {
        const value = parseInt(el.getAttribute('data-value'), 10) || 0;
        const bar = el.querySelector('span');
        if (bar) {
            bar.style.width = `${value}%`;
        }
    });
});
