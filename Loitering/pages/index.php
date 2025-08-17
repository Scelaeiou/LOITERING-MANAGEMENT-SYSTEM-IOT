<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard</title>

  <!-- Tailwind + FontAwesome -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <!-- Custom Styles -->
  <link rel="stylesheet" href="dashboard.css">
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

  <!-- Top Navbar -->
  <header>
    <div class="navbar">
      <div class="logo text-xl font-bold"><a href="#">Loitering Management System   </a></div>

      <a href="get_start.php" class="action_btn">🚀 Get Started</a>

      <div class="toggle_btn text-2xl">
        <i class="fa-solid fa-bars"></i>
      </div>
    </div>

    <!-- Mobile Dropdown -->
    <div class="dropdown_menu bg-white shadow-md p-4 md:hidden">
      <ul class="flex flex-col gap-2">
        <li><a href="home.php">Dashboard</a></li>
        <li><a href="login.php" class="text-red-600">Logout</a></li>
        <li><a href="get_start.php" class="action_btn text-center">🚀 Get Started</a></li>
      </ul>
    </div>
  </header>

  <!-- Main Dashboard Layout -->
  <div class="flex flex-1">

    <!-- Sidebar -->
    <aside class="hidden md:block w-64 bg-rgba(255, 255, 255, 0.8) shadow-md h-full p-5">
      <nav class="flex flex-col space-y-4">
        <a href="home.php" class="text-gray-700 hover:text-red-500 font-medium">📊 Dashboard</a>
        <a href="get_start.php" class="text-gray-700 hover:text-red-500 font-medium">🚀 Getting Started</a>
        <a href="login.php" class="text-red-600 hover:text-red-800 font-medium">🔓 Logout</a>
      </nav>
    </aside>

    <!-- Content -->
    <main class="flex-1 p-6">
      <h2 class="text-3xl font-semibold text-gray-800 mb-6">About Us</h2>
      <div class="bg-rgba(255, 255, 255, 0.8) p-6 rounded-lg shadow-md mb-6">
        <p class="text-gray-700 leading-relaxed text-justify text-lg font-semibold italic">
          We are a team of passionate 4th-year Computer Engineering students committed to addressing real-world challenges through innovative technology. Over the course of our studies, we’ve built a strong foundation in both hardware and software systems, allowing us to turn complex ideas into practical solutions.
          <br><br>
          Our capstone project, the <strong style="color:rgba(220, 38, 38, 0.6);">Loitering Management System</strong>
            , is designed to monitor and regulate time-based activities in shared spaces. By combining RFID technology, IoT devices, and a centralized database, the system helps optimize space usage, reduce unnecessary loitering, and enhance oversight through real-time automation.
           <br><br>
          With this project, we aim to deliver smart, reliable solutions that reflect our technical skills and prepare us for impactful careers in embedded systems, intelligent automation, and connected technologies.
        </p>
      </div>

      <section id="home" class="bg-rgba(255, 255, 255, 0.8) p-6 rounded-lg shadow-md">
        <h1 class="text-xl font-bold text-gray-800">Future Updates</h1>
        <p class="text-gray-600 mt-2">....
        </p>
      </section>
    </main>
  </div>

  <script>
    const toggleBtn = document.querySelector('.toggle_btn');
    const toggleBtnIcon = document.querySelector('.toggle_btn i');
    const dropDownMenu = document.querySelector('.dropdown_menu');

    toggleBtn.onclick = function () {
      dropDownMenu.classList.toggle('open');
      const isOpen = dropDownMenu.classList.contains('open');
      toggleBtnIcon.className = isOpen ? 'fa-solid fa-xmark' : 'fa-solid fa-bars';
    }
  </script>

</body>
</html>
