let timeLeft = 60;
const countdown = document.getElementById('countdown');

function startTimer() {
  const interval = setInterval(() => {
    if (timeLeft <= 0) {
      clearInterval(interval);
      countdown.textContent = "Expirado";
      countdown.style.color = "red";
    } else {
      const minutes = Math.floor(timeLeft / 60);
      const seconds = timeLeft % 60;
      countdown.textContent = `${String(minutes).padStart(2, "0")}:${String(seconds).padStart(2, "0")}`;
      timeLeft--;
    }
  }, 1000);
}

function toggleDarkMode() {
  document.body.classList.toggle('light');
}

let fontSize = 16;
function changeFontSize(change) {
  fontSize += change;
  document.body.style.fontSize = fontSize + 'px';
}

document.addEventListener('DOMContentLoaded', startTimer);