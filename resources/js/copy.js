// Copy buttons: data-copy="{id}" copies that element's text. The button reads "Copied" for a moment, and the page's
// [data-copy-status] tells screen readers.
document.addEventListener('click', async (event) => {
    const button = event.target.closest('[data-copy]');
    const source = button && document.getElementById(button.dataset.copy);

    if (!source) {
        return;
    }

    const label = button.querySelector('[data-copy-label]');
    const status = document.querySelector('[data-copy-status]');

    try {
        await navigator.clipboard.writeText(source.textContent.trim());
    } catch {
        // No clipboard (plain HTTP on another host, or permission denied): select the text so Ctrl+C still works.
        window.getSelection()?.selectAllChildren(source);
        if (status) status.textContent = 'Selected. Press Ctrl+C or Cmd+C to copy.';

        return;
    }

    if (label) label.textContent = 'Copied';
    if (status) status.textContent = 'Copied';

    clearTimeout(button.copyTimer);
    button.copyTimer = setTimeout(() => {
        if (label) label.textContent = 'Copy';
        if (status) status.textContent = '';
    }, 2000);
});
