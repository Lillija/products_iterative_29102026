// Test condition: the marker attribute proves the JS bundle is loaded via the Vite dev server
document.documentElement.dataset.jsLoaded = 'true';

// Auto-dismiss flash messages (progressive enhancement, see .alert[data-auto-dismiss])
document.querySelectorAll('.alert[data-auto-dismiss]').forEach((alert) => {
    setTimeout(() => alert.remove(), 4000);
});
