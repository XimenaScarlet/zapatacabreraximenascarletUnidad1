<?php
$productos = [
    [
        'nombre' => 'Pepsi Original',
        'categoria' => 'clasicos',
        'imagen' => 'https://www.pepsico.com/images/default-source/products-brands/pepsi_12oz.png',
        'descripcion' => 'El sabor original que ha refrescado generaciones. Una experiencia única en cada sorbo.',
        'precio' => 18.00,
        'estrellas' => 5,
        'badge' => 'Clásico',
        'badge_color' => 'bg-pepsiBlue',
        'rating' => '5.0',
    ],
    [
        'nombre' => 'Pepsi Zero Sugar',
        'categoria' => 'zero-azucar',
        'imagen' => 'https://www.pepsico.com/images/default-source/products-brands/pepsi_zero_sugar.png',
        'descripcion' => 'Todo el sabor Pepsi que amas, sin azúcar. Perfecto para un estilo de vida saludable.',
        'precio' => 20.00,
        'estrellas' => 4,
        'badge' => 'Zero Azúcar',
        'badge_color' => 'bg-green-600',
        'rating' => '4.0',
    ],
    [
        'nombre' => 'Pepsi Zero Cafeína',
        'categoria' => 'zero-azucar',
        'imagen' => 'https://www.pepsico.com/images/default-source/products-brands/pepsi_zero_sugar.png',
        'descripcion' => 'El refrescante sabor Pepsi sin cafeína. Perfecto para cualquier momento del día.',
        'precio' => 20.00,
        'estrellas' => 4.5,
        'badge' => 'Sin Cafeína',
        'badge_color' => 'bg-purple-600',
        'rating' => '4.5',
    ],
    [
        'nombre' => 'Pepsi Cherry',
        'categoria' => 'sabor',
        'imagen' => 'https://www.pepsico.com/images/default-source/products-brands/pepsi_wild_cherry.png',
        'descripcion' => 'Una deliciosa combinación de Pepsi con sabor a cereza. Una explosión de sabor.',
        'precio' => 22.00,
        'estrellas' => 5,
        'badge' => 'Sabor Especial',
        'badge_color' => 'bg-red-600',
        'rating' => '5.0',
    ],
    [
        'nombre' => 'Pepsi Lime',
        'categoria' => 'edicion-limitada',
        'imagen' => 'https://www.pepsico.com/images/default-source/products-brands/pepsi_lime.png',
        'descripcion' => 'El refrescante sabor de Pepsi con un toque de lima. Perfecto para el verano.',
        'precio' => 22.00,
        'estrellas' => 4,
        'badge' => 'Edición Limitada',
        'badge_color' => 'bg-green-500',
        'rating' => '4.0',
    ],
    [
        'nombre' => 'Pepsi Mango',
        'categoria' => 'sabor',
        'imagen' => '',
        'descripcion' => 'Pepsi con un toque tropical de mango. Edición especial.',
        'precio' => 24.00,
        'estrellas' => 4,
        'badge' => 'Edición Limitada',
        'badge_color' => 'bg-yellow-500',
        'rating' => '4.0',
    ],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pepsi</title>
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
        body {
            background-color: #000000;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .product-card {
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .product-card:hover {
            transform: translateY(-5px);
            border-color: #004B93;
        }

        .star-rating {
            color: #FFD700;
        }

        .category-link {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .category-link::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 100%;
            height: 2px;
            background: #004B93;
            transform: scaleX(0);
            transform-origin: right;
            transition: transform 0.3s ease;
        }

        .category-link:hover::after,
        .category-link.active::after {
            transform: scaleX(1);
            transform-origin: left;
        }

        .category-link:hover, .category-link.active {
            background: rgba(0, 75, 147, 0.1);
            color: #004B93;
        }

        .product-card {
            opacity: 1;
            transform: translateY(0);
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            height: 100%;
        }

        .product-card.hidden-product {
            opacity: 0;
            transform: translateY(20px);
            pointer-events: none;
            position: absolute;
        }

        .products-container {
            position: relative;
            min-height: 800px;
        }

        @media (max-width: 768px) {
            .products-container {
                min-height: 1600px;
            }
            .sidebar {
                margin-bottom: 2rem;
                position: sticky;
                top: 5rem;
                z-index: 10;
                background: rgba(0, 0, 0, 0.8);
                backdrop-filter: blur(10px);
                border-radius: 1rem;
            }
            .category-link {
                padding: 0.75rem 1rem;
            }
            .product-card {
                animation: fadeIn 0.5s ease-out;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .product-card:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .product-image-container {
            transition: transform 0.3s ease;
            transform-origin: center bottom;
        }

        .product-card:hover .product-image-container {
            transform: scale(1.1) translateY(-5px);
        }

        body:not(.dark-mode) {
            background-color: #ffffff;
            color: #000000;
        }

        body:not(.dark-mode) .product-card {
            border-color: rgba(0, 0, 0, 0.1);
            background: rgba(0, 0, 0, 0.02);
        }

        body:not(.dark-mode) .sidebar {
            background: rgba(0, 0, 0, 0.02);
            border-color: rgba(0, 0, 0, 0.1);
        }

        body.light-mode .breadcrumb-list li,
        body.light-mode .breadcrumb-list li.text-white,
        body.light-mode .breadcrumb-list li[aria-current="page"] {
            color: #222 !important;
        }
        body.light-mode .breadcrumb-list a {
            color: #004B93 !important;
        }

        @keyframes blinkColor {
            0% { color: #004B93; }
            50% { color: #FFFFFF; }
            100% { color: #004B93; }
        }
        .blink-pepsi {
            animation: blinkColor 1.5s linear infinite;
        }
    </style>
</head>
<body class="bg-black text-white font-sans min-h-screen">
    <header class="fixed top-0 left-0 right-0 z-50 bg-black/90 backdrop-blur-sm border-b border-white/10 transition-colors duration-300">
        <nav class="container mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex-shrink-0">
                <a href="index.php" class="text-3xl font-bold text-white tracking-wider">PEPSI</a>
            </div>

            <div id="navLinks" class="hidden md:flex items-center justify-center flex-1 space-x-6 transition-all duration-300">
                <a href="index.php" class="text-white hover:text-pepsiBlue transition-colors">Inicio</a>
                <a href="contact.php" class="text-white hover:text-pepsiBlue transition-colors">Contacto</a>
                <a href="faq.php" class="text-white hover:text-pepsiBlue transition-colors">FAQ</a>
                <a href="privacy.php" class="text-white hover:text-pepsiBlue transition-colors">Privacidad</a>
            </div>

            <div id="navSearchbarContainer" class="hidden md:flex items-center ml-2 transition-all duration-300" style="max-width:320px;flex:0 0 auto;display:none;">
                <input id="navSearchInput" type="text" placeholder="Buscar productos..." class="w-full px-4 py-2 rounded-full border border-pepsiBlue focus:outline-none focus:border-pepsiBlue text-black" style="font-size:1.1rem; min-width:180px;">
            </div>

            <div class="flex items-center space-x-4 ml-4">
                <button id="searchIconBtn" class="text-white hover:text-pepsiBlue transition-colors" aria-label="Buscar productos">
                    <i class="fas fa-search text-xl"></i>
                </button>
                <a href="login.php" class="text-white hover:text-pepsiBlue transition-colors">
                    <i class="fas fa-user text-xl"></i>
                </a>
                <button class="text-white hover:text-pepsiBlue transition-colors" id="darkModeToggle">
                    <i class="fas fa-moon text-xl"></i>
                </button>
                <button class="md:hidden text-white hover:text-pepsiBlue transition-colors" id="mobileMenuButton">
                    <i class="fas fa-bars text-xl"></i>
                </button>
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
    <div class="pt-24 pb-16">
        <div class="container mx-auto px-6">
            <div class="flex flex-col md:flex-row gap-8">
                <aside class="md:w-64 flex-shrink-0">
                    <div class="sidebar rounded-lg border border-white/10 p-6 sticky top-24 bg-black/20">
                        <h2 class="text-xl font-semibold mb-4">Categorías</h2>
                        <nav class="space-y-2">
            <a href="#" class="category-link block px-4 py-2 rounded-lg active" data-category="todos">
                Todos los productos
                <span class="text-sm text-gray-400 ml-2">(6)</span>
            </a>
            <a href="#" class="category-link block px-4 py-2 rounded-lg" data-category="zero-azucar">
                Bebidas Zero Azúcar
                <span class="text-sm text-gray-400 ml-2">(2)</span>
            </a>
            <a href="#" class="category-link block px-4 py-2 rounded-lg" data-category="sabor">
                Bebidas con Sabor
                <span class="text-sm text-gray-400 ml-2">(2)</span>
            </a>
            <a href="#" class="category-link block px-4 py-2 rounded-lg" data-category="edicion-limitada">
                Edición Limitada
                <span class="text-sm text-gray-400 ml-2">(1)</span>
            </a>
            <a href="#" class="category-link block px-4 py-2 rounded-lg" data-category="clasicos">
                Clásicos
                <span class="text-sm text-gray-400 ml-2">(1)</span>
            </a>
                        </nav>
                    </div>
                </aside>

                <div class="flex-1">
                    <div class="products-container">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 relative">
                        <div class="product-card rounded-xl p-6 flex flex-col">
                            <div class="relative mb-4 product-image-container">
                                <img src="https://www.pepsico.com/images/default-source/products-brands/pepsi_12oz.png" 
                                     alt="Pepsi Original" 
                                     class="w-32 mx-auto">
                                <span class="absolute top-0 right-0 bg-pepsiBlue text-white text-sm px-3 py-1 rounded-full" data-category="clasicos">
                                    Clásico
                                </span>
                            </div>
                            <h3 class="text-xl font-bold mb-2">Pepsi Original</h3>
                            <div class="star-rating mb-2">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <span class="text-gray-400 ml-2">5.0</span>
                            </div>
                            <p class="text-gray-400 mb-4">El sabor original que ha refrescado generaciones. Una experiencia única en cada sorbo.</p>
                            <div class="mt-auto flex justify-center">
                                <a href="login.php" class="bg-pepsiBlue text-white px-6 py-2 rounded font-semibold shadow hover:bg-blue-900 transition">
                                    Inicia sesión para comprar
                                </a>
                            </div>
                        </div>

                        <div class="product-card rounded-xl p-6 flex flex-col">
                            <div class="relative mb-4">
                                <img src="https://www.pepsico.com/images/default-source/products-brands/pepsi_zero_sugar.png" 
                                     alt="Pepsi Zero Sugar" 
                                     class="w-32 mx-auto transform hover:scale-105 transition-transform">
                                <span class="absolute top-0 right-0 bg-green-600 text-white text-sm px-3 py-1 rounded-full" data-category="zero-azucar">
                                    Zero Azúcar
                                </span>
                            </div>
                            <h3 class="text-xl font-bold mb-2">Pepsi Zero Sugar</h3>
                            <div class="star-rating mb-2">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="far fa-star"></i>
                                <span class="text-gray-400 ml-2">4.0</span>
                            </div>
                            <p class="text-gray-400 mb-4">Todo el sabor Pepsi que amas, sin azúcar. Perfecto para un estilo de vida saludable.</p>
                            <div class="mt-auto flex justify-center">
                                <a href="login.php" class="bg-pepsiBlue text-white px-6 py-2 rounded font-semibold shadow hover:bg-blue-900 transition">
                                    Inicia sesión para comprar
                                </a>
                            </div>
                        </div>

                        <div class="product-card rounded-xl p-6 flex flex-col">
                            <div class="relative mb-4">
                                <img src="https://www.pepsico.com/images/default-source/products-brands/pepsi_zero_sugar.png" 
                                     alt="Pepsi Zero Cafeína" 
                                     class="w-32 mx-auto transform hover:scale-105 transition-transform">
                                <span class="absolute top-0 right-0 bg-purple-600 text-white text-sm px-3 py-1 rounded-full" data-category="zero-azucar">
                                    Sin Cafeína
                                </span>
                            </div>
                            <h3 class="text-xl font-bold mb-2">Pepsi Zero Cafeína</h3>
                            <div class="star-rating mb-2">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                                <span class="text-gray-400 ml-2">4.5</span>
                            </div>
                            <p class="text-gray-400 mb-4">El refrescante sabor Pepsi sin cafeína. Perfecto para cualquier momento del día.</p>
                            <div class="mt-auto flex justify-center">
                                <a href="login.php" class="bg-pepsiBlue text-white px-6 py-2 rounded font-semibold shadow hover:bg-blue-900 transition">
                                    Inicia sesión para comprar
                                </a>
                            </div>
                        </div>

                        <div class="product-card rounded-xl p-6 flex flex-col">
                            <div class="relative mb-4">
                                <img src="https://www.pepsico.com/images/default-source/products-brands/pepsi_wild_cherry.png" 
                                     alt="Pepsi Cherry" 
                                     class="w-32 mx-auto transform hover:scale-105 transition-transform">
                                <span class="absolute top-0 right-0 bg-red-600 text-white text-sm px-3 py-1 rounded-full" data-category="sabor">
                                    Sabor Especial
                                </span>
                            </div>
                            <h3 class="text-xl font-bold mb-2">Pepsi Cherry</h3>
                            <div class="star-rating mb-2">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <span class="text-gray-400 ml-2">5.0</span>
                            </div>
                            <p class="text-gray-400 mb-4">Una deliciosa combinación de Pepsi con sabor a cereza. Una explosión de sabor.</p>
                            <div class="mt-auto flex justify-center">
                                <a href="login.php" class="bg-pepsiBlue text-white px-6 py-2 rounded font-semibold shadow hover:bg-blue-900 transition">
                                    Inicia sesión para comprar
                                </a>
                            </div>
                        </div>

                        <div class="product-card rounded-xl p-6 flex flex-col">
                            <div class="relative mb-4">
                                <img src="https://www.pepsico.com/images/default-source/products-brands/pepsi_lime.png" 
                                     alt="Pepsi Lime" 
                                     class="w-32 mx-auto transform hover:scale-105 transition-transform">
                                <span class="absolute top-0 right-0 bg-green-500 text-white text-sm px-3 py-1 rounded-full" data-category="edicion-limitada">
                                    Edición Limitada
                                </span>
                            </div>
                            <h3 class="text-xl font-bold mb-2">Pepsi Lime</h3>
                            <div class="star-rating mb-2">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="far fa-star"></i>
                                <span class="text-gray-400 ml-2">4.0</span>
                            </div>
                            <p class="text-gray-400 mb-4">El refrescante sabor de Pepsi con un toque de lima. Perfecto para el verano.</p>
                            <div class="mt-auto flex justify-center">
                                <a href="login.php" class="bg-pepsiBlue text-white px-6 py-2 rounded font-semibold shadow hover:bg-blue-900 transition">
                                    Inicia sesión para comprar
                                </a>
                            </div>
                        </div>

                        <div class="product-card rounded-xl p-6 flex flex-col">
                            <div class="relative mb-4">
                                <img src="https://www.pepsico.com/images/default-source/products-brands/pepsi_mango.png" 
                                     alt="Pepsi Mango" 
                                     class="w-32 mx-auto transform hover:scale-105 transition-transform">
                                <span class="absolute top-0 right-0 bg-yellow-500 text-white text-sm px-3 py-1 rounded-full" data-category="sabor">
                                    Nuevo Sabor
                                </span>
                            </div>
                            <h3 class="text-xl font-bold mb-2">Pepsi Mango</h3>
                            <div class="star-rating mb-2">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                                <span class="text-gray-400 ml-2">4.5</span>
                            </div>
                            <p class="text-gray-400 mb-4">Una deliciosa fusión de Pepsi con sabor a mango. Una experiencia tropical.</p>
                            <div class="mt-auto flex justify-center">
                                <a href="login.php" class="bg-pepsiBlue text-white px-6 py-2 rounded font-semibold shadow hover:bg-blue-900 transition">
                                    Inicia sesión para comprar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const darkModeToggle = document.getElementById('darkModeToggle');
        const moonIcon = darkModeToggle.querySelector('.fa-moon');
        let isDarkMode = localStorage.getItem('darkMode') !== null 
            ? localStorage.getItem('darkMode') === 'true' 
            : true;

        function updateTheme() {
            document.body.classList.toggle('dark-mode', isDarkMode);
            const header = document.querySelector('header');
            const navLinks = document.querySelectorAll('a:not(.category-link), button:not(.category-link)');

            header.classList.toggle('bg-black/90', isDarkMode);
            header.classList.toggle('bg-white/90', !isDarkMode);
            header.classList.toggle('border-white/10', isDarkMode);
            header.classList.toggle('border-black/10', !isDarkMode);

            moonIcon.classList.toggle('fa-moon', isDarkMode);
            moonIcon.classList.toggle('fa-sun', !isDarkMode);

            navLinks.forEach(link => {
                if (!link.classList.contains('text-pepsiBlue')) {
                    link.classList.toggle('text-white', isDarkMode);
                    link.classList.toggle('text-black', !isDarkMode);
                }
            });
        }

        updateTheme();

        darkModeToggle.addEventListener('click', () => {
            isDarkMode = !isDarkMode;
            localStorage.setItem('darkMode', isDarkMode);
            updateTheme();
        });

        const categoryLinks = document.querySelectorAll('.category-link');
        const productCards = document.querySelectorAll('.product-card');

        function filterProducts(category) {
            productCards.forEach(card => {
                const badge = card.querySelector('[data-category]');
                const shouldShow = category === 'todos' || badge.dataset.category === category;
                
                if (shouldShow) {
                    card.classList.remove('hidden-product');
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, 50);
                } else {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px)';
                    setTimeout(() => {
                        card.classList.add('hidden-product');
                    }, 500);
                }
            });
        }

        categoryLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                categoryLinks.forEach(l => l.classList.remove('active'));
                link.classList.add('active');
                filterProducts(link.dataset.category);
            });
        });

        const mobileMenuButton = document.getElementById('mobileMenuButton');
        const mobileMenu = document.querySelector('.md\\:flex.items-center');

        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

        const searchIconBtn = document.getElementById('searchIconBtn');
        const navSearchbarContainer = document.getElementById('navSearchbarContainer');
        const navSearchInput = document.getElementById('navSearchInput');

        document.addEventListener('DOMContentLoaded', function() {
            navSearchbarContainer.classList.add('hidden');
            navSearchbarContainer.style.display = 'none';
        });

        function openSearchbar() {
            navSearchbarContainer.classList.remove('hidden');
            navSearchbarContainer.style.display = 'flex';
            navSearchInput.value = '';
            navSearchInput.focus();
        }

        function closeSearchbar() {
            navSearchbarContainer.classList.add('hidden');
            navSearchbarContainer.style.display = 'none';
            productCards.forEach(card => card.classList.remove('hidden-product'));
        }

        searchIconBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const isOpen = !navSearchbarContainer.classList.contains('hidden');
            if (isOpen) {
                closeSearchbar();
            } else {
                openSearchbar();
            }
        });

        navSearchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeSearchbar();
            }
        });

        document.addEventListener('mousedown', function(e) {
            if (
                !navSearchbarContainer.contains(e.target) &&
                !searchIconBtn.contains(e.target)
            ) {
                if (!navSearchbarContainer.classList.contains('hidden')) {
                    closeSearchbar();
                }
            }
        });

        navSearchInput.addEventListener('input', function() {
            const query = navSearchInput.value.trim().toLowerCase();
            productCards.forEach(card => {
                const name = card.querySelector('h3')?.textContent.toLowerCase() || '';
                const desc = card.querySelector('p')?.textContent.toLowerCase() || '';
                if (name.includes(query) || desc.includes(query)) {
                    card.classList.remove('hidden-product');
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                } else {
                    card.classList.add('hidden-product');
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px)';
                }
            });
        });

        navSearchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeSearchbar();
            }
        });

        document.addEventListener('mousedown', function(e) {
            if (
                !navSearchbarContainer.contains(e.target) &&
                !searchIconBtn.contains(e.target)
            ) {
                if (!navSearchbarContainer.classList.contains('hidden')) {
                    closeSearchbar();
                }
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            if (navSearchbarContainer.classList.contains('hidden')) {
                navLinks.classList.remove('mr-4');
                navLinks.classList.remove('justify-start');
                navLinks.classList.add('justify-center');
            }
        });

        window.chatbaseConfig = {
          chatbotId: "",
        }
    </script>
<script src="https://www.chatbase.co/embed.min.js" id="chatbase-script" defer></script>

<footer class="bg-black text-white py-8 mt-12">
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

            <div class="flex flex-wrap justify-center gap-8 text-sm uppercase tracking-wider">
                <a href="privacy.php" class="text-white hover:text-pepsiBlue transition-colors">Privacidad</a>
                <a href="contact.php" class="text-white hover:text-pepsiBlue transition-colors">Contacto</a>
                <a href="faq.php" class="text-white hover:text-pepsiBlue transition-colors">FAQ</a>
                <a href="sitemap.php" class="text-white hover:text-pepsiBlue transition-colors">Mapa del sitio</a>
            </div>
            <div class="text-sm text-gray-500">
                © 2022 PEPSICO, INC.
            </div>
        </div>
    </div>
</footer>
</body>
</html>
