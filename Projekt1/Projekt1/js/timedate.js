function updateDateTime() {
  const now = new Date();
  const date = now.toLocaleDateString('pl-PL');
  const time = now.toLocaleTimeString('pl-PL', {
    hour12: false
  });
  document.getElementById("data").textContent = date;
  document.getElementById("zegarek").textContent = time;
}
document.addEventListener('DOMContentLoaded', () => {
  updateDateTime(); 
  setInterval(updateDateTime, 1000); 
});
