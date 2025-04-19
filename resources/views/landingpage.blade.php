<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Landing Page</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      background-color: #48bb78;
      font-family: Arial, sans-serif;
      overflow: hidden;
    }

    .container {
      text-align: center;
      color: white;
    }

    .logo {
      width: 200px;
      height: 200px;
      background: url('images/logo.jpeg') no-repeat center center;
      background-size: contain;
      animation: fadeInLogo 1.5s ease-out forwards;
      opacity: 0;
      margin: 0 auto;
    }

    .cat {
      width: 80px;
      height: 80px;
      background: url('images/kucing.png') no-repeat center center;
      background-size: cover;
      position: relative;
      opacity: 0;
      margin-top: 20px;
      animation: fadeInCat 1s ease forwards 2s, walkCat 5s linear infinite 3s;
      display: inline-block;
    }

    @keyframes fadeInLogo {
      0% {
        opacity: 0;
        transform: scale(0);
      }
      100% {
        opacity: 1;
        transform: scale(1);
      }
    }

    @keyframes fadeInCat {
      0% {
        opacity: 0;
        transform: translateY(20px);
      }
      100% {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes walkCat {
      0% { transform: translateX(0); }
      50% { transform: translateX(150px); }
      100% { transform: translateX(0); }
    }

    .text {
      margin-top: 5px;
      font-size: 36px;
      font-weight: bold;
      opacity: 0;
      animation: fadeInText 1s ease forwards;
      animation-delay: 4s;
    }

    @keyframes fadeInText {
      0% {
        opacity: 0;
        transform: translateY(20px);
      }
      100% {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .btn {
      display: inline-block;
      margin-top: 20px;
      padding: 10px 20px;
      font-size: 18px;
      background-color: #2d6a4f;
      color: white;
      text-decoration: none;
      border-radius: 5px;
      transition: background-color 0.3s;
      opacity: 0;
      animation: fadeInButton 1s ease forwards;
      animation-delay: 5s;
    }

    @keyframes fadeInButton {
      0% {
        opacity: 0;
        transform: translateY(20px);
      }
      100% {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .btn:hover {
      background-color: #1b5e20;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="logo"></div>
    <div class="cat" id="cat"></div>
    <div class="text">Hachi Petshop</div>
    <a href="{{ url('/dashboard') }}" class="btn">Masuk</a>
  </div>

  <!-- Audio suara kucing -->
  <audio id="meongSound" preload="auto">
    <source src="sounds/suarameong.mp3" type="audio/mp3" />
    Browser kamu tidak mendukung audio.
  </audio>

  <script>
    const audio = document.getElementById("meongSound");

    // Mainkan suara saat kucing muncul (sekitar 2 detik setelah logo)
    setTimeout(() => {
      audio.currentTime = 0;
      audio.play().catch((e) => {
        console.log("Autoplay error:", e);
      });
    }, 2000);

    // Ulangi suara setiap 5 detik (sama kayak animasi jalan)
    setInterval(() => {
      audio.currentTime = 0;
      audio.play().catch((e) => {
        console.log("Loop play error:", e);
      });
    }, 5000);
  </script>
</body>
</html>
