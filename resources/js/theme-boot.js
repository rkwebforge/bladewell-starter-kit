// Puts the saved theme on <html> before the page is drawn, so nobody sees a flash of the wrong one. It runs inline
// in <head> (the only inline script in the app); SecurityHeaders allows it by its hash. The toggle is in theme.js.
(function () {
    var theme = null;
    try {
        theme = localStorage.getItem('theme');
    } catch (error) {}
    var dark = theme === 'dark' || (theme !== 'light' && window.matchMedia('(prefers-color-scheme: dark)').matches);
    document.documentElement.classList.toggle('dark', dark);
})();
