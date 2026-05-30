const timerDisplay = document.getElementById('timerDisplay');
const timerLabel = document.getElementById('timerLabel');
const startBtn = document.getElementById('startBtn');
const pauseBtn = document.getElementById('pauseBtn');
const resetBtn = document.getElementById('resetBtn');
const customMinutes = document.getElementById('customMinutes');
const quoteText = document.getElementById('quoteText');
const quoteAuthor = document.getElementById('quoteAuthor');
const focusTimeDisplay = document.getElementById('focusTimeDisplay');

let currentSession = localStorage.getItem('timerSession') || 'focus';
let timeLeft = parseInt(localStorage.getItem('timerTimeLeft')) || sessionTimes[currentSession] || 1500;
let isRunning = localStorage.getItem('timerRunning') === 'true';
let endTime = localStorage.getItem('timerEndTime') ? parseInt(localStorage.getItem('timerEndTime')) : null;
let timerInterval = null;

function formatTime(seconds) {
    const m = Math.floor(seconds / 60).toString().padStart(2, '0');
    const s = (seconds % 60).toString().padStart(2, '0');
    return `${m}:${s}`;
}

function updateDisplay() {
    timerDisplay.textContent = formatTime(timeLeft);
    timerLabel.textContent = sessionLabels[currentSession] || 'Focus Time';
    document.title = `(${formatTime(timeLeft)}) TimeTod`;
}

function setSession(session) {
    if (isRunning) return;
    currentSession = session;
    timeLeft = sessionTimes[session];
    endTime = null;
    localStorage.setItem('timerSession', session);
    localStorage.setItem('timerTimeLeft', timeLeft);
    localStorage.removeItem('timerEndTime');
    customMinutes.value = Math.floor(sessionTimes[session] / 60);
    updateDisplay();
    updateSessionButtons();
}

function updateSessionButtons() {
    document.querySelectorAll('.session-btn').forEach(btn => {
        const session = btn.dataset.session;
        const isActive = session === currentSession;
        btn.classList.toggle('bg-[#4D96FF]', isActive);
        btn.classList.toggle('text-white', isActive);
        btn.classList.toggle('bg-white', !isActive);
        btn.classList.toggle('text-black', !isActive);
    });
}

function showQuote() {
    if (!quoteText) return;
    const today = new Date();
    const idx = today.getDate() % quotes.length;
    const quote = quotes[idx];
    quoteText.textContent = `"${quote.text}"`;
    quoteAuthor.textContent = `— ${quote.author}`;
}

function playNotification() {
    try {
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        const playTone = (freq, duration, startTime) => {
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.type = 'square';
            osc.frequency.setValueAtTime(freq, startTime);
            gain.gain.setValueAtTime(0.3, startTime);
            gain.gain.exponentialRampToValueAtTime(0.01, startTime + duration);
            osc.start(startTime);
            osc.stop(startTime + duration);
        };
        playTone(800, 0.15, ctx.currentTime);
        playTone(1000, 0.15, ctx.currentTime + 0.2);
        playTone(1200, 0.3, ctx.currentTime + 0.4);
    } catch (e) {
        console.log('Audio not available');
    }
}

function sendBrowserNotification(title, body) {
    if (!('Notification' in window)) return;
    if (Notification.permission === 'granted') {
        new Notification(title, { body, icon: '/favicon.ico' });
    } else if (Notification.permission !== 'denied') {
        Notification.requestPermission().then(perm => {
            if (perm === 'granted') {
                new Notification(title, { body, icon: '/favicon.ico' });
            }
        });
    }
}

function updateUIState() {
    if (isRunning) {
        startBtn.disabled = true;
        startBtn.classList.add('opacity-50', 'cursor-not-allowed');
        pauseBtn.disabled = false;
        pauseBtn.classList.remove('opacity-50', 'cursor-not-allowed');
    } else {
        startBtn.disabled = false;
        startBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        pauseBtn.disabled = true;
        pauseBtn.classList.add('opacity-50', 'cursor-not-allowed');
    }
}

async function completeSession() {
    try {
        const response = await fetch('/timer/complete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                type: currentSession,
                duration: timeLeft <= 0 ? sessionTimes[currentSession] : timeLeft
            })
        });

        if (response.ok) {
            playNotification();
            sendBrowserNotification(
                currentSession === 'focus' ? 'Focus session complete! 🎉' : 'Break time over!',
                currentSession === 'focus'
                    ? 'Great work! Time for a break.'
                    : 'Ready to focus again?'
            );
            showQuote();

            if (focusTimeDisplay) {
                const todayRes = await fetch('/timer/today');
                if (todayRes.ok) {
                    const todayData = await todayRes.json();
                    const hours = Math.floor(todayData.total_focus_time / 3600);
                    const mins = Math.floor((todayData.total_focus_time % 3600) / 60);
                    focusTimeDisplay.textContent =
                        `${hours.toString().padStart(2, '0')}:${mins.toString().padStart(2, '0')}`;
                }
            }
        }
    } catch (e) {
        console.error('Failed to save session:', e);
    }
}

function tick() {
    if (!endTime) return;
    const now = Date.now();
    timeLeft = Math.max(0, Math.ceil((endTime - now) / 1000));
    localStorage.setItem('timerTimeLeft', timeLeft);
    updateDisplay();

    if (timeLeft <= 0) {
        stopTimer();
        completeSession();
        resetTimer();
    }
}

function startTimer() {
    if (isRunning || timeLeft <= 0) return;
    isRunning = true;
    endTime = Date.now() + timeLeft * 1000;
    localStorage.setItem('timerRunning', 'true');
    localStorage.setItem('timerEndTime', endTime.toString());
    updateUIState();

    tick();
    timerInterval = setInterval(tick, 1000);
}

function stopTimer() {
    isRunning = false;
    localStorage.setItem('timerRunning', 'false');
    localStorage.removeItem('timerEndTime');

    if (timerInterval) {
        clearInterval(timerInterval);
        timerInterval = null;
    }

    endTime = null;
    updateUIState();
}

function resetTimer() {
    stopTimer();
    timeLeft = sessionTimes[currentSession];
    localStorage.setItem('timerTimeLeft', timeLeft);
    customMinutes.value = Math.floor(sessionTimes[currentSession] / 60);
    updateDisplay();
}

function setCustomMinutes() {
    if (isRunning) return;
    const minutes = parseInt(customMinutes.value) || 1;
    const seconds = minutes * 60;
    timeLeft = seconds;
    sessionTimes[currentSession] = seconds;
    localStorage.setItem('timerTimeLeft', timeLeft);
    updateDisplay();
}

// Event listeners
pauseBtn.addEventListener('click', stopTimer);
startBtn.addEventListener('click', startTimer);
resetBtn.addEventListener('click', resetTimer);
customMinutes.addEventListener('change', setCustomMinutes);

document.querySelectorAll('.session-btn').forEach(btn => {
    btn.addEventListener('click', () => setSession(btn.dataset.session));
});

// Sync timer state from localStorage (handle page refresh while running)
function syncFromStorage() {
    if (isRunning && endTime) {
        tick();
        if (timeLeft > 0) {
            timerInterval = setInterval(tick, 1000);
            updateUIState();
        } else {
            isRunning = false;
            localStorage.setItem('timerRunning', 'false');
            localStorage.removeItem('timerEndTime');
            updateUIState();
            resetTimer();
        }
    }
}

// Init
customMinutes.value = Math.floor(sessionTimes[currentSession] / 60);
updateSessionButtons();
updateDisplay();
showQuote();
updateUIState();

if (isRunning) {
    syncFromStorage();
}

// Keyboard shortcuts
document.addEventListener('keydown', (e) => {
    if (e.target.tagName === 'INPUT' || e.target.tagName === 'SELECT') return;
    if (e.key === ' ' || e.key === 'Space') {
        e.preventDefault();
        if (isRunning) stopTimer(); else startTimer();
    }
    if (e.key === 'r' || e.key === 'R') resetTimer();
});
