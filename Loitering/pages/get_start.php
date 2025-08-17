<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>User Guidelines</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .fade {
      transition: opacity 0.3s ease-in-out;
    }
  </style>
</head>
<body class="bg-gray-100 text-gray-800 font-sans">

  <!-- Header -->
  <header class="bg-red-600 text-white py-6 shadow-md">
    <div class="max-w-4xl mx-auto px-4">
      <h1 class="text-3xl font-bold">User Guidelines</h1>
      <p class="text-sm">Navigate through the steps below to get started.</p>
    </div>
  </header>

  <!-- Progress Indicator -->
  <div class="max-w-4xl mx-auto mt-6 px-4">
    <div class="w-full bg-gray-300 h-2 rounded">
      <div id="progressBar" class="bg-red-600 h-2 rounded transition-all duration-300" style="width: 33%;"></div>
    </div>
    <div class="flex justify-between text-xs text-gray-500 mt-1">
      <span>Step 1</span><span>Step 2</span><span>Step 3</span>
    </div>
  </div>

  <!-- Content -->
  <main class="max-w-3xl mx-auto bg-white mt-6 rounded-lg shadow-md p-6 transition-all duration-500">
    <!-- Section 1: Rules -->
    <div id="section-1" class="fade">
      <h2 class="text-2xl font-semibold mb-4">1️⃣ Rules & Code of Conduct</h2>
      <ul class="list-disc list-inside space-y-2">
        <li>✅ Be respectful to all users.</li>
        <li>✅ No offensive language or hate speech.</li>
        <li>✅ Follow admin instructions promptly.</li>
        <li>✅ Report misuse or suspicious activity.</li>
        <li>✅ Use the platform for its intended purpose.</li>
      </ul>
    </div>

    <!-- Section 2: How to Use -->
    <div id="section-2" class="hidden fade">
      <h2 class="text-2xl font-semibold mb-4">2️⃣ How to Use the System</h2>
      <ol class="list-decimal list-inside space-y-2">
        <li>🖥️ Log in with your user ID and password.</li>
        <li>🧭 Navigate through the dashboard menu.</li>
        <li>⏱️ Check your timer or RFID data in real time.</li>
        <li>📊 Review history and usage logs anytime.</li>
        <li>❓ Contact support for help or guidance.</li>
      </ol>
    </div>

    <!-- Section 3: FAQ -->
    <div id="section-3" class="hidden fade">
      <h2 class="text-2xl font-semibold mb-4">3️⃣ Frequently Asked Questions</h2>
      <div class="space-y-4">
        <div>
          <strong>Q: What if I forget my login?</strong>
          <p>A: Contact admin support.</p>
        </div>
        <div>
          <strong>Q: Can I share my RFID tag?</strong>
          <p>A: No, RFID tags are user-specific and sharing may lead to suspension.</p>
        </div>
        <div>
          <strong>Q: How do I add more time to my balance?</strong>
          <p>A: Visit the front desk.</p>
        </div>
      </div>
    </div>

    <!-- Navigation Buttons -->
    <div class="mt-8 flex justify-between">
      <button id="backBtn" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400 transition" disabled>← Back</button>
      <button id="nextBtn" class="bg-red-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">Next →</button>
    </div>
  </main>

  <!-- Footer -->
  <footer class="text-center py-6 mt-10 text-sm text-gray-500">
    © 2025 Your System. All rights reserved.
  </footer>

  <!-- Script -->
  <script>
    const sections = [
      document.getElementById('section-1'),
      document.getElementById('section-2'),
      document.getElementById('section-3'),
    ];
    const nextBtn = document.getElementById('nextBtn');
    const backBtn = document.getElementById('backBtn');
    const progressBar = document.getElementById('progressBar');

    let current = 0;

    function showSection(index) {
      sections.forEach((sec, i) => {
        sec.classList.toggle('hidden', i !== index);
      });
      progressBar.style.width = `${(index + 1) * (100 / sections.length)}%`;
      backBtn.disabled = index === 0;
      nextBtn.textContent = index === sections.length - 1 ? 'Finish' : 'Next →';
    }

    nextBtn.addEventListener('click', () => {
      if (current < sections.length - 1) {
        current++;
        showSection(current);
      } else {
        // Redirect to home.php when finished
        window.location.href = 'home.php';
      }
    });

    backBtn.addEventListener('click', () => {
      if (current > 0) {
        current--;
        showSection(current);
      }
    });

    showSection(current);
  </script>

</body>
</html>
