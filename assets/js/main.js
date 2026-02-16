document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById('theme-toggle');
    const body = document.body;
    const content = document.querySelector('main.content');
    if (document.querySelector('.sidebar')) {
        content?.classList.add('with-sidebar');
    }

    const saved = localStorage.getItem('theme');
    if (saved) {
        body.setAttribute('data-theme', saved);
    }

    toggle?.addEventListener('click', () => {
        const next = body.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
        body.setAttribute('data-theme', next);
        localStorage.setItem('theme', next);
    });
});
