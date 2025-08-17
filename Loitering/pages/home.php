<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>ESP32 Dashboard</title>
  <link rel="stylesheet" href="style1.2.css" />
  <style>
    .timer-box {
      font-size: 1.5rem;
      margin: 10px 0;
    }
    .status {
      font-size: 1.5rem;
      margin-left: 10px;
    }
  </style>
  
  <script>
    function formatTime(seconds) {
      const h = Math.floor(seconds / 3600).toString().padStart(2, '0');
      const m = Math.floor((seconds % 3600) / 60).toString().padStart(2, '0');
      const s = (seconds % 60).toString().padStart(2, '0');
      return `${h}:${m}:${s}`;
    }

    let serverTimeOffset = 0;

    function updateTimers() {
      fetch('get_timers.php?ts=' + new Date().getTime())
        .then(res => res.json())
        .then(data => {
          const serverTime = data.server_time;
          const clientTime = Math.floor(Date.now() / 1000);
          serverTimeOffset = serverTime - clientTime;

          const now = clientTime + serverTimeOffset;

          for (let i = 1; i <= 6; i++) {
            const timer = data.timers[i];
            const span = document.getElementById('time' + i);
            const status = document.getElementById('status' + i);

            if (timer && timer.end_time) {
              const diff = timer.end_time - now;
              span.textContent = diff > 0 ? formatTime(diff) : "00:00:00";

              const lastSeenAgo = now - timer.last_seen;
              status.textContent = lastSeenAgo < 10 ? "🟢" : "🔴"; // online if seen in last 10s
            } else {
              span.textContent = "--:--:--";
              status.textContent = "🔴";
            }
          }
        })
        .catch(err => console.error("Failed to fetch timers:", err));
    }

    setInterval(updateTimers, 1000);
    window.onload = updateTimers;
  </script>
</head>
<body>
  <div class="container">
    <aside class="sidebar">
      <div class="logo">Loitering Dashboard</div>
      <nav class="nav">
        <a href="home.php">🏠 Home</a>
        <a href="history.php">📜 History</a>
        <a href="control.php">🎛️ Control Area</a>
        <a href="register.php">ℹ️ Register Area</a>
        <a href="index.php" class="back-button">⬅ Back to Main</a>
      </nav>
    </aside>

    <main class="main">
      <section id="home" class="card">
        <h1>Dashboard Overview</h1>
        <p>Live timers updating based on RFID tag activity.</p>

        <?php for ($i = 1; $i <= 6; $i++): ?>
          <div class="timer-box" id="table<?= $i ?>">
            Table <?= $i ?>: 
            <span id="time<?= $i ?>">--:--:--</span>
            <span class="status" id="status<?= $i ?>">🔴</span>
          </div>
        <?php endfor; ?>
      </section>
    </main>
  </div>
</body>
</html>
