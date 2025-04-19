<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Page</title>

    <!-- Add CSS for animations -->
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #48bb78; /* bg-green-500 */
            font-family: Arial, sans-serif;
            overflow: hidden;
        }

        .container {
            text-align: center;
            color: white; /* Text color */
        }

        /* Logo styling and animation */
        .logo {
            width: 200px;
            height: 200px;
            background: url('images/logo.jpeg') no-repeat center center;
            background-size: contain;
            animation: fadeInLogo 2s ease-out forwards;
            opacity: 0;
            margin: 0 auto; /* Ensure logo is centered horizontally */
        }

        /* Fade-in effect for logo */
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

        /* Text styling with animation */
        .text {
            margin-top: 20px;
            font-size: 36px;
            font-weight: bold;
            opacity: 0;
            animation: fadeInText 3s 1s forwards; /* Delay text animation */
        }

        /* Fade-in effect for text */
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

        /* Button styling */
        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 18px;
            background-color: #2d6a4f; /* Darker green button */
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
            opacity: 0;
            animation: fadeInButton 3s 2s forwards; /* Delay button animation */
        }

        /* Fade-in effect for button */
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

        /* Button hover effect */
        .btn:hover {
            background-color: #1b5e20; /* Even darker green on hover */
        }
    </style>
</head>
<body>

    <div class="container">
        <!-- Logo with animation -->
        <div class="logo"></div>

        <!-- Text (Hachi Petshop) with animation -->
        <div class="text">Hachi Petshop</div>

        <!-- Button to navigate to homepage -->
        <a href="{{ url('/dashboard') }}" class="btn">Beranda</a>
    </div>

</body>
</html>
