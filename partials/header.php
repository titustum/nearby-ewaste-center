<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Waste Connect - Sustainable Electronics Disposal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="favicon.svg" type="image/svg+xml">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Custom animations and styles */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-100%);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }

        .pulse-animation {
            animation: pulse 2s infinite;
        }

        .slide-in-left {
            animation: slideInLeft 0.3s ease-out;
        }

        .mobile-menu {
            transition: transform 0.3s ease-in-out;
            transform: translateX(-100%);
        }

        .mobile-menu.active {
            transform: translateX(0);
        }

        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .card-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .loading-spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #10b981;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .rating-stars {
            color: #fbbf24;
        }

        .accepted-items {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .item-tag {
            background: #e5f3ff;
            color: #1e40af;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .filter-button {
            transition: all 0.2s ease;
        }

        .filter-button.active {
            background: #10b981;
            color: white;
        }

        .progress-bar {
            height: 4px;
            background: #e5e7eb;
            border-radius: 2px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #10b981, #059669);
            border-radius: 2px;
            transition: width 0.3s ease;
        }
    </style>
</head>

<body class="font-sans antialiased bg-gray-50">

    <!-- Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-40">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-recycle text-2xl text-green-600"></i>
                    <a href="#" class="text-2xl font-bold text-gray-800">E-Waste Connect</a>
                </div>

                <div class="hidden md:flex space-x-6">
                    <a href="#home" class="text-gray-600 hover:text-green-600 font-semibold transition duration-200">Home</a>
                    <a href="#about" class="text-gray-600 hover:text-green-600 font-semibold transition duration-200">About Us</a>
                    <a href="#how-it-works" class="text-gray-600 hover:text-green-600 font-semibold transition duration-200">How It Works</a>
                    <a href="#faq" class="text-gray-600 hover:text-green-600 font-semibold transition duration-200">FAQ</a>
                    <a href="#contact" class="text-gray-600 hover:text-green-600 font-semibold transition duration-200">Contact</a>
                </div>

                <div class="md:hidden">
                    <button id="mobileMenuButton" class="text-gray-600 hover:text-green-600 focus:outline-none">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div id="mobileMenu" class="mobile-menu fixed top-0 left-0 w-80 h-full bg-white shadow-2xl z-50 md:hidden">
        <div class="p-6">
            <div class="flex justify-between items-center mb-8">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-recycle text-xl text-green-600"></i>
                    <span class="text-xl font-bold text-gray-800">E-Waste Connect</span>
                </div>
                <button id="closeMobileMenuButton" class="text-gray-600 hover:text-green-600 focus:outline-none">
                    <i class="fas fa-times text-xl" onclick="document.getElementById('mobileMenu').style.display='hidden'"></i>
                </button>
            </div>
            <nav class="flex flex-col space-y-4">
                <a href="#home" class="text-gray-800 hover:text-green-600 font-semibold text-lg py-3 px-4 block border-b border-gray-100 transition duration-200">
                    <i class="fas fa-home mr-3"></i>Home
                </a>
                <a href="#about" class="text-gray-800 hover:text-green-600 font-semibold text-lg py-3 px-4 block border-b border-gray-100 transition duration-200">
                    <i class="fas fa-info-circle mr-3"></i>About Us
                </a>
                <a href="#how-it-works" class="text-gray-800 hover:text-green-600 font-semibold text-lg py-3 px-4 block border-b border-gray-100 transition duration-200">
                    <i class="fas fa-cogs mr-3"></i>How It Works
                </a>
                <a href="#faq" class="text-gray-800 hover:text-green-600 font-semibold text-lg py-3 px-4 block border-b border-gray-100 transition duration-200">
                    <i class="fas fa-question-circle mr-3"></i>FAQ
                </a>
                <a href="#contact" class="text-gray-800 hover:text-green-600 font-semibold text-lg py-3 px-4 block transition duration-200">
                    <i class="fas fa-envelope mr-3"></i>Contact
                </a>
            </nav>
        </div>
    </div>


    <!-- Progress Bar -->
    <div id="progressContainer" class="progress-bar hidden">
        <div id="progressBar" class="progress-fill" style="width: 0%"></div>
    </div>