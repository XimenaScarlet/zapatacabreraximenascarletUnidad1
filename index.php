<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pepsi</title>
    <script>
        (function() {
            try {
                var isDarkMode = localStorage.getItem('darkMode') !== null 
                    ? localStorage.getItem('darkMode') === 'true' 
                    : true;
                if (isDarkMode) {
                    document.documentElement.classList.add('dark-mode');
                    document.body && (document.body.style.backgroundColor = '#000000');
                    document.body && (document.body.style.color = '#ffffff');
                } else {
                    document.documentElement.classList.remove('dark-mode');
                    document.body && (document.body.style.backgroundColor = '#ffffff');
                    document.body && (document.body.style.color = '#000000');
                }
            } catch(e){}
        })();
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        pepsiBlue: '#004B93',
                    },
                    fontFamily: {
                        'sans': ['Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <style>
        @keyframes blinkColor {
            0% { color: #004B93; }
            50% { color: #FFFFFF; }
            100% { color: #004B93; }
        }

        .blink-pepsi {
            animation: blinkColor 1.5s linear infinite;
        }

        @keyframes shineMove {
            0% { transform: skewX(-20deg) translateX(-150%); }
            100% { transform: skewX(-20deg) translateX(250%); }
        }

        @keyframes floatRotate {
            0% { transform: translateY(0) rotate(0deg) scale(1); }
            50% { transform: translateY(-15px) rotate(3deg) scale(1.02); }
            100% { transform: translateY(0) rotate(0deg) scale(1); }
        }



        .banner-bottle {
            animation: floatRotate 8s ease-in-out infinite;
            transform-origin: center bottom;
        }

        .banner-gradient {
            background: linear-gradient(
                135deg,
                rgba(0, 75, 147, 0.2) 0%,
                rgba(0, 0, 0, 0.1) 100%
            );
            transition: background 0.3s ease;
        }

        .banner-shine {
            position: absolute;
            inset: 0;
            background: linear-gradient(
                45deg,
                transparent 0%,
                rgba(255, 255, 255, 0.03) 50%,
                transparent 100%
            );
            pointer-events: none;
        }

        .parallax-move {
            transform: translateZ(0);
            transition: transform 0.2s cubic-bezier(0.33, 1, 0.68, 1);
            will-change: transform;
        }

        .parallax-container {
            perspective: 1000px;
            transform-style: preserve-3d;
        }

        .parallax-content {
            transform: translateZ(50px);
            will-change: transform;
        }

        body {
            background-color: #000000;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .product-card {
            transition: all 0.3s ease;
            border-radius: 12px;
            position: relative;
            overflow: hidden;
        }

        .product-card:hover {
            transform: translateY(-10px);
        }

        .product-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at center, rgba(0,75,147,0.1) 0%, transparent 70%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .product-card:hover::before {
            opacity: 1;
        }

        .pepsi-bottle {
            width: 180px;
            height: 420px;
            position: relative;
            margin: 0 auto;
        }

        .bottle-neck {
            width: 50px;
            height: 70px;
            background: linear-gradient(90deg, 
                rgba(0,75,147,0.9) 0%, 
                #004B93 30%,
                #0066cc 50%,
                #004B93 70%,
                rgba(0,75,147,0.9) 100%
            );
            margin: 0 auto;
            border-radius: 10px 10px 5px 5px;
            position: relative;
            box-shadow: inset 0 -5px 10px rgba(0,0,0,0.2);
        }

        .bottle-cap {
            width: 58px;
            height: 22px;
            background: linear-gradient(90deg, #003366 0%, #004B93 50%, #003366 100%);
            border-radius: 6px;
            position: absolute;
            top: -11px;
            left: -4px;
            border: 1px solid rgba(255,255,255,0.1);
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .bottle-body {
            width: 100%;
            height: 350px;
            background: linear-gradient(90deg, 
                rgba(0,75,147,0.9) 0%, 
                #004B93 30%,
                #0066cc 50%,
                #004B93 70%,
                rgba(0,75,147,0.9) 100%
            );
            border-radius: 90px;
            position: relative;
            overflow: hidden;
            box-shadow: 
                inset 0 0 40px rgba(0,0,0,0.3),
                0 5px 15px rgba(0,0,0,0.2);
        }

        .bottle-shine {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        .shine-line {
            position: absolute;
            top: 0;
            left: 0;
            width: 30%;
            height: 100%;
            background: linear-gradient(90deg, 
                transparent 0%, 
                rgba(255,255,255,0.1) 50%, 
                transparent 100%
            );
            animation: shineMove 3s infinite;
        }

        .bottle-label {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 85%;
            height: 140px;
            background: rgba(255,255,255,0.15);
            border-radius: 12px;
            box-shadow: 
                inset 0 0 20px rgba(255,255,255,0.2),
                0 0 10px rgba(0,0,0,0.2);
            overflow: hidden;
        }

        .bottle-label::before {
            content: 'PEPSI';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: rgba(255,255,255,0.9);
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 2px;
        }

        .lime .bottle-body,
        .lime .bottle-neck {
            background: linear-gradient(90deg, 
                rgba(132,204,22,0.9) 0%, 
                rgb(132,204,22) 30%,
                rgb(163,230,53) 50%,
                rgb(132,204,22) 70%,
                rgba(132,204,22,0.9) 100%
            );
        }

        .lime .bottle-cap {
            background: linear-gradient(90deg, rgb(101,163,13) 0%, rgb(132,204,22) 50%, rgb(101,163,13) 100%);
        }

        .lime .bottle-label::before {
            content: 'PEPSI LIMA';
        }

        .info-link {
            color: #004B93;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
            padding-bottom: 2px;
            display: inline-block;
            margin-top: 0.5rem;
        }

        .info-link:after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 50%;
            background-color: #004B93;
            transition: all 0.3s ease;
        }

        .info-link:hover:after {
            width: 100%;
            left: 0;
        }

        .product-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-top: 1.5rem;
            margin-bottom: 0.5rem;
            letter-spacing: 0.05em;
            line-height: 1.2;
        }
    </style>
</head>
<body class="bg-black text-white font-sans min-h-screen">
    <a href="#main-content" class="sr-only focus:not-sr-only">Ir al contenido principal</a>

    <header class="fixed top-0 left-0 right-0 z-50 bg-black/90 backdrop-blur-sm border-b border-white/10 transition-colors duration-300">
        <nav class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex-shrink-0">
                    <h1 class="text-3xl font-bold text-white tracking-wider">PEPSI</h1>
                </div>

                <div class="hidden md:flex items-center justify-center flex-1 space-x-6">
                    <a href="products.php" class="text-white hover:text-pepsiBlue transition-colors">Productos</a>
                    <a href="contact.php" class="text-white hover:text-pepsiBlue transition-colors">Contacto</a>
                    <a href="faq.php" class="text-white hover:text-pepsiBlue transition-colors">FAQ</a>
                    <a href="privacy.php" class="text-white hover:text-pepsiBlue transition-colors">Privacidad</a>
                </div>

                <div class="flex items-center space-x-4">
                    <a href="login.php" class="text-white hover:text-pepsiBlue transition-colors">
                        <i class="fas fa-user text-xl"></i>
                    </a>
                    <button class="text-white hover:text-pepsiBlue transition-colors" id="darkModeToggle">
                        <i class="fas fa-moon text-xl"></i>
                    </button>

                    <button class="md:hidden text-white hover:text-pepsiBlue transition-colors">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>

            <div class="md:hidden hidden pt-4 transform transition-all duration-300 ease-in-out opacity-0">
                <div class="flex flex-col space-y-3">
                    <a href="products.php" class="text-white hover:text-pepsiBlue transition-colors">Productos</a>
                    <a href="contact.php" class="text-white hover:text-pepsiBlue transition-colors">Contacto</a>
                    <a href="faq.php" class="text-white hover:text-pepsiBlue transition-colors">FAQ</a>
                    <a href="privacy.php" class="text-white hover:text-pepsiBlue transition-colors">Privacidad</a>
                </div>
            </div>
        </nav>
    </header>

    <nav aria-label="breadcrumb" class="w-full flex justify-center pt-24 pb-2">
        <ol class="flex items-center space-x-2 text-lg font-semibold breadcrumb-list" id="breadcrumb-list"></ol>
    </nav>
    <script>

        (function() {
            const maxBreadcrumb = 8;
            const storageKey = 'pepsi_breadcrumb_history';
            let history = [];
            try {
                history = JSON.parse(localStorage.getItem(storageKey)) || [];
            } catch(e) { history = []; }

            function getLabel(path) {
                if (path.endsWith('index.php') || path === '/' || path === '') return 'Inicio';
                if (path.endsWith('products.php')) return 'Productos';
                if (path.endsWith('contact.php')) return 'Contacto';
                if (path.endsWith('faq.php')) return 'FAQ';
                if (path.endsWith('privacy.php')) return 'Privacidad';
                if (path.endsWith('register.php')) return 'Registro';
                if (path.endsWith('login.php')) return 'Iniciar Sesión';
                let name = path.split('/').pop().replace('.php', '');
                return name.charAt(0).toUpperCase() + name.slice(1);
            }

            function normalizePath(path) {
                const a = document.createElement('a');
                a.href = path;
                return a.pathname.replace(/\/+$/, '');
            }

            const currentPath = normalizePath(window.location.pathname);
            const currentLabel = getLabel(currentPath);

            if (/\.php$/.test(currentPath)) {
                history = history.filter(item => normalizePath(item.href) !== currentPath);
                history.push({ href: currentPath, label: currentLabel });
            }
            if (history.length > maxBreadcrumb) history = history.slice(-maxBreadcrumb);
            localStorage.setItem(storageKey, JSON.stringify(history));

            const breadcrumbList = document.getElementById('breadcrumb-list');
            let html = '';
            for (let i = 0; i < history.length; i++) {
                if (i > 0) html += `<li class="text-white">/</li>`;
                if (i < history.length - 1) {
                    html += `<li><a href="${history[i].href}" class="hover:underline text-pepsiBlue">${history[i].label}</a></li>`;
                } else {
                    html += `<li class="text-white" aria-current="page">${history[i].label}</li>`;
                }
            }
            breadcrumbList.innerHTML = html;
        })();
    </script>
    <style>
        body.light-mode .breadcrumb-list li,
        body.light-mode .breadcrumb-list li.text-white,
        body.light-mode .breadcrumb-list li[aria-current="page"] {
            color: #222 !important;
        }
        body.light-mode .breadcrumb-list a {
            color: #004B93 !important;
        }
    </style>
    <main id="main-content" class="pt-24 pb-16">
        <div class="container mx-auto px-6 mb-16">
            <div class="relative banner-gradient rounded-2xl overflow-hidden border border-white/5 parallax-container" id="banner">
                <div class="banner-shine"></div>
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_50%,rgba(0,75,147,0.2),transparent_70%)]"></div>
                <div class="grid md:grid-cols-2 gap-8 items-center p-8 md:p-12 parallax-content">
                    <div class="space-y-6 text-center md:text-left">
                        <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold tracking-tight">
                            Refresca tu día<br>
                            <span class="text-pepsiBlue">con Pepsi</span>
                        </h2>
                        <p id="hero-desc" class="text-lg text-gray-300">
                            Descubre el sabor único que ha refrescado generaciones.
                            Una experiencia inigualable en cada sorbo.
                        </p>
                        <div class="flex flex-wrap gap-4 justify-center md:justify-start">
                            <button class="bg-pepsiBlue hover:bg-blue-700 text-white px-8 py-3 rounded-full font-medium transition-all transform hover:scale-105">
                                Descubre más
                            </button>
                            <a href="products.php" class="border border-white/20 hover:border-pepsiBlue text-white px-8 py-3 rounded-full font-medium transition-all transform hover:scale-105 inline-block">
                                Ver productos
                            </a>
                        </div>
                    </div>
                    <div class="relative flex justify-center">
                        <div class="pepsi-bottle banner-bottle w-48 md:w-64 hover:scale-105 transition-transform duration-300 parallax-move">
                            <div class="bottle-neck">
                                <div class="bottle-cap"></div>
                            </div>
                            <div class="bottle-body">
                                <div class="bottle-shine">
                                    <div class="shine-line"></div>
                                </div>
                                <div class="bottle-label"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="absolute top-0 left-0 w-full h-full pointer-events-none">
                    <div class="absolute top-1/2 left-1/4 w-24 h-24 bg-pepsiBlue/20 rounded-full blur-2xl"></div>
                    <div class="absolute bottom-1/4 right-1/3 w-32 h-32 bg-blue-500/10 rounded-full blur-3xl"></div>
                </div>
            </div>
        </div>

        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="product-card flex flex-col items-center text-center p-6">
                    <a href="#" class="block">
                        <div class="pepsi-bottle">
                            <div class="bottle-neck">
                                <div class="bottle-cap"></div>
                            </div>
                            <div class="bottle-body">
                                <div class="bottle-shine">
                                    <div class="shine-line"></div>
                                </div>
                                <div class="bottle-label"></div>
                            </div>
                        </div>
                        <h3 class="product-title">
                            ZERO<br>
                            AZÚCAR
                        </h3>
                        <p class="info-link">Info producto</p>
                    </a>
                </div>

                <div class="product-card flex flex-col items-center text-center p-6">
                    <a href="#" class="block">
                        <div class="pepsi-bottle">
                            <div class="bottle-neck">
                                <div class="bottle-cap"></div>
                            </div>
                            <div class="bottle-body">
                                <div class="bottle-shine">
                                    <div class="shine-line"></div>
                                </div>
                                <div class="bottle-label"></div>
                            </div>
                        </div>
                        <h3 class="product-title">
                            ZERO<br>
                            AZÚCAR ZERO CAFEÍNA
                        </h3>
                        <p class="info-link">Info producto</p>
                    </a>
                </div>

                <div class="product-card flex flex-col items-center text-center p-6">
                    <a href="#" class="block">
                        <div class="pepsi-bottle lime">
                            <div class="bottle-neck">
                                <div class="bottle-cap"></div>
                            </div>
                            <div class="bottle-body">
                                <div class="bottle-shine">
                                    <div class="shine-line"></div>
                                </div>
                                <div class="bottle-label"></div>
                            </div>
                        </div>
                        <h3 class="product-title">
                            ZERO<br>
                            AZÚCAR LIMA
                        </h3>
                        <p class="info-link">Info producto</p>
                    </a>
                </div>

                <div class="product-card flex flex-col items-center text-center p-6">
                    <a href="#" class="block">
                        <div class="pepsi-bottle">
                            <div class="bottle-neck">
                                <div class="bottle-cap"></div>
                            </div>
                            <div class="bottle-body">
                                <div class="bottle-shine">
                                    <div class="shine-line"></div>
                                </div>
                                <div class="bottle-label"></div>
                            </div>
                        </div>
                        <h3 class="product-title">
                            ORIGINAL
                        </h3>
                        <p class="info-link">Info producto</p>
                    </a>
                </div>
            </div>
        </div>
    </main>

    <button id="chat-bubble-btn"
        class="fixed bottom-4 right-4 z-[9999] w-16 h-16 rounded-full bg-pepsiBlue text-white flex items-center justify-center shadow-lg hover:bg-blue-700 transition-all"
        aria-label="Abrir chat"
        style="box-shadow: 0 4px 16px rgba(0,0,0,0.2); font-size: 2rem;">
        <i class="fas fa-comment-dots"></i>
    </button>

    <iframe
        id="chat-iframe"
        src="https://www.chatbase.co/chatbot-iframe/IOgCBWRXBPOCAXtMrfEzy"
        frameborder="0"
        class="fixed bottom-24 right-4 z-[9999] w-72 md:w-80 h-[450px] md:h-[500px] rounded-lg shadow-lg bg-white"
        style="max-height: 80vh; max-width: calc(100% - 2rem); display: none;"
    ></iframe>

    <footer class="bg-black text-white py-8">
        <div class="container mx-auto px-6">
            <div class="flex flex-col items-center justify-between space-y-8">
                <div class="text-4xl font-bold blink-pepsi">PEPSI</div>

                <div class="flex space-x-8">
                    <a href="#" class="text-white hover:text-pepsiBlue transition-colors">
                        <i class="fab fa-twitter text-2xl"></i>
                    </a>
                    <a href="#" class="text-white hover:text-pepsiBlue transition-colors">
                        <i class="fab fa-facebook text-2xl"></i>
                    </a>
                    <a href="#" class="text-white hover:text-pepsiBlue transition-colors">
                        <i class="fab fa-instagram text-2xl"></i>
                    </a>
                </div>

                <div id="footer-links" class="flex flex-wrap justify-center gap-8 text-sm uppercase tracking-wider">
                    <a href="contact.php" class="footer-link text-white hover:text-pepsiBlue transition-colors">Contacto</a>
                    <a href="faq.php" class="footer-link text-white hover:text-pepsiBlue transition-colors">FAQ</a>
                </div>

                <div class="text-sm text-gray-500">
                    © 2022 PEPSICO, INC.
                </div>
            </div>
        </div>
    </footer>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const darkModeToggle = document.getElementById('darkModeToggle');
            const moonIcon = darkModeToggle.querySelector('.fa-moon, .fa-sun');
            let isDarkMode = localStorage.getItem('darkMode') !== null 
                ? localStorage.getItem('darkMode') === 'true' 
                : true;
            function updateTheme() {
                document.body.classList.toggle('dark-mode', isDarkMode);
                document.body.style.backgroundColor = isDarkMode ? '#000000' : '#ffffff';
                document.body.style.color = isDarkMode ? '#ffffff' : '#000000';

                const header = document.querySelector('header');
                if (header) {
                    header.classList.toggle('bg-black/90', isDarkMode);
                    header.classList.toggle('bg-white/90', !isDarkMode);
                    header.classList.toggle('border-white/10', isDarkMode);
                    header.classList.toggle('border-black/10', !isDarkMode);
                }

                const logo = document.querySelector('h1');
                if (logo) {
                    logo.classList.toggle('text-white', isDarkMode);
                    logo.classList.toggle('text-black', !isDarkMode);
                }

                const navLinks = document.querySelectorAll('a:not(.info-link):not(.blink-pepsi), button:not(.info-link)');
                navLinks.forEach(link => {
                    link.classList.toggle('text-white', isDarkMode);
                    link.classList.toggle('text-black', !isDarkMode);
                    link.classList.toggle('hover:text-pepsiBlue', isDarkMode);
                    link.classList.toggle('hover:text-black', !isDarkMode);
                });

                const productCards = document.querySelectorAll('.product-card');
                productCards.forEach(card => {
                    card.style.backgroundColor = isDarkMode ? 'transparent' : 'rgba(0,0,0,0.05)';
                });

                const infoLinks = document.querySelectorAll('.info-link');
                infoLinks.forEach(link => {
                    link.style.color = '#004B93';
                });


                const heroDesc = document.getElementById('hero-desc');
                if (heroDesc) {
                    heroDesc.style.color = isDarkMode ? '#d1d5db' : '#222';
                }

                if (moonIcon) {
                    if (isDarkMode) {
                        moonIcon.classList.add('fa-moon');
                        moonIcon.classList.remove('fa-sun');
                    } else {
                        moonIcon.classList.remove('fa-moon');
                        moonIcon.classList.add('fa-sun');
                    }
                }

                const footerLinks = document.querySelectorAll('.footer-link');
                if (footerLinks.length) {
                    footerLinks.forEach(link => {
                        link.style.color = isDarkMode ? '' : '#fff';
                    });
                }
            }

            updateTheme();

            darkModeToggle.addEventListener('click', () => {
                isDarkMode = !isDarkMode;
                localStorage.setItem('darkMode', isDarkMode);
                updateTheme();
            });

            const banner = document.getElementById('banner');
            const parallaxElements = banner.querySelectorAll('.parallax-move');

            banner.addEventListener('mousemove', (e) => {
                const rect = banner.getBoundingClientRect();
                const centerX = rect.left + rect.width / 2;
                const centerY = rect.top + rect.height / 2;
                const moveX = (e.clientX - centerX) / 25;
                const moveY = (e.clientY - centerY) / 25;
                const rotateX = moveY * -0.5;
                const rotateY = moveX * 0.5;

                banner.querySelector('.parallax-content').style.transform = 
                    `translateZ(50px) rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;

                parallaxElements.forEach(element => {
                    element.style.transform = `translate(${moveX}px, ${moveY}px) scale(1.05)`;
                });
            });

            banner.addEventListener('mouseleave', () => {
                banner.querySelector('.parallax-content').style.transform = 'translateZ(50px) rotateX(0) rotateY(0)';
                parallaxElements.forEach(element => {
                    element.style.transform = 'translate(0, 0) scale(1)';
                });
            });

            const mobileMenuButton = document.querySelector('.md\\:hidden .fa-bars').parentElement;
            const mobileMenu = document.querySelector('.md\\:hidden.hidden.pt-4');
            
            document.addEventListener('click', (e) => {
                if (!mobileMenuButton.contains(e.target) && !mobileMenu.contains(e.target)) {
                    mobileMenu.classList.add('opacity-0');
                    setTimeout(() => {
                        mobileMenu.classList.add('hidden');
                    }, 300);
                }
            });

            mobileMenuButton.addEventListener('click', (e) => {
                e.stopPropagation();
                if (mobileMenu.classList.contains('hidden')) {
                    mobileMenu.classList.remove('hidden');
                    requestAnimationFrame(() => {
                        mobileMenu.classList.remove('opacity-0');
                    });
                } else {
                    mobileMenu.classList.add('opacity-0');
                    setTimeout(() => {
                        mobileMenu.classList.add('hidden');
                    }, 300);
                }
            });
        });
    </script>
    <script>
        const chatBtn = document.getElementById('chat-bubble-btn');
        const chatIframe = document.getElementById('chat-iframe');
        let chatOpen = false;

        chatBtn.addEventListener('click', () => {
            chatOpen = !chatOpen;
            chatIframe.style.display = chatOpen ? 'block' : 'none';
            chatBtn.querySelector('i').classList.toggle('fa-comment-dots', !chatOpen);
            chatBtn.querySelector('i').classList.toggle('fa-times', chatOpen);
        });

        document.addEventListener('click', (e) => {
            if (chatOpen && !chatIframe.contains(e.target) && !chatBtn.contains(e.target)) {
                chatOpen = false;
                chatIframe.style.display = 'none';
                chatBtn.querySelector('i').classList.add('fa-comment-dots');
                chatBtn.querySelector('i').classList.remove('fa-times');
            }
        });

        chatIframe.addEventListener('click', e => e.stopPropagation());
        chatBtn.addEventListener('click', e => e.stopPropagation());
    </script>
</body>
</html>
