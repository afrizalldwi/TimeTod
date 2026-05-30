const darkModeToggle = document.getElementById('darkModeToggle');

function getDarkMode() {
    return localStorage.getItem('darkMode') === 'true';
}

function setDarkMode(enabled) {
    localStorage.setItem('darkMode', enabled ? 'true' : 'false');
    if (enabled) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
    updateDarkButton();
}

function updateDarkButton() {
    if (!darkModeToggle) return;
    const isDark = getDarkMode();
    const sun = darkModeToggle.querySelector('.dark-hidden');
    const moon = darkModeToggle.querySelector('.dark-block');
    if (sun) sun.classList.toggle('hidden', isDark);
    if (moon) moon.classList.toggle('hidden', !isDark);
}

if (darkModeToggle) {
    darkModeToggle.addEventListener('click', () => {
        setDarkMode(!getDarkMode());
    });
}

// Initialize dark mode on page load
const storedDarkMode = getDarkMode();
if (storedDarkMode) {
    document.documentElement.classList.add('dark');
}
updateDarkButton();
