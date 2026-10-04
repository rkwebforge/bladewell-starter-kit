// The theme menu: Light, Dark or System (follows the device). The choice is kept in this browser's localStorage, and
// theme-boot.js applies it on the next page before anything is drawn.
const KEY = 'theme';
const deviceIsDark = window.matchMedia('(prefers-color-scheme: dark)');

function saved() {
    try {
        return localStorage.getItem(KEY);
    } catch {
        return null; // Storage blocked (some private modes): follow the device.
    }
}

function apply(theme) {
    const choice = theme === 'light' || theme === 'dark' ? theme : 'system';
    const dark = choice === 'dark' || (choice === 'system' && deviceIsDark.matches);
    document.documentElement.classList.toggle('dark', dark);

    for (const item of document.querySelectorAll('[data-theme-choice]')) {
        const current = item.dataset.themeChoice === choice;
        // aria-current tells screen readers which one is chosen; the tick shows it.
        current ? item.setAttribute('aria-current', 'true') : item.removeAttribute('aria-current');
        item.querySelector('[data-theme-tick]')?.classList.toggle('invisible', !current);
    }
}

document.addEventListener('click', (event) => {
    const item = event.target.closest?.('[data-theme-choice]');
    if (!item) {
        return;
    }
    const choice = item.dataset.themeChoice;
    try {
        choice === 'system' ? localStorage.removeItem(KEY) : localStorage.setItem(KEY, choice);
    } catch {
        // Not saved, but this page still switches.
    }
    apply(choice);
});

// System follows the device as it changes (many switch to dark at sunset), and other open tabs follow a new choice.
deviceIsDark.addEventListener('change', () => apply(saved()));
window.addEventListener('storage', (event) => event.key === KEY && apply(event.newValue));

apply(saved());
