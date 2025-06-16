<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Política de Privacidad | Pepsi</title>
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
</head>
<body class="bg-black text-white font-sans min-h-screen">
    <!-- Header -->
    <header class="fixed top-0 left-0 right-0 z-50 bg-black/90 backdrop-blur-sm border-b border-white/10 transition-colors duration-300">
        <nav class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <!-- Logo -->
                <div class="flex-shrink-0">
                    <a href="index.php" class="text-3xl font-bold text-white tracking-wider">PEPSI</a>
                </div>

                <!-- Navigation Links - Centered -->
                <div class="hidden md:flex items-center justify-center flex-1 space-x-6">
                   <a href="index.php" class="text-white hover:text-pepsiBlue transition-colors">Inicio</a>
                    <a href="products.php" class="text-white hover:text-pepsiBlue transition-colors">Productos</a>
                    <a href="contact.php" class="text-white hover:text-pepsiBlue transition-colors">Contacto</a>
                    <a href="faq.php" class="text-white hover:text-pepsiBlue transition-colors">FAQ</a>
                </div>

                <!-- Right Icons -->
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

            <!-- Mobile Menu -->
            <div class="md:hidden hidden pt-4 transform transition-all duration-300 ease-in-out opacity-0">
                <div class="flex flex-col space-y-3">
                    <a href="index.php" class="text-white hover:text-pepsiBlue transition-colors">Inicio</a>
                    <a href="products.php" class="text-white hover:text-pepsiBlue transition-colors">Productos</a>
                    <a href="contact.php" class="text-white hover:text-pepsiBlue transition-colors">Contacto</a>
                    <a href="faq.php" class="text-white hover:text-pepsiBlue transition-colors">FAQ</a>
                </div>
            </div>
        </nav>
    </header>

    <!-- Breadcrumb dinámico -->
    <nav aria-label="breadcrumb" class="w-full flex justify-center pt-24 pb-2">
        <ol class="flex items-center space-x-2 text-lg font-semibold breadcrumb-list" id="breadcrumb-list"></ol>
    </nav>
    <script>
        // Breadcrumb real basado en historial de navegación (máx 8)
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
        @keyframes blinkColor {
            0% { color: #004B93; }
            50% { color: #FFFFFF; }
            100% { color: #004B93; }
        }
        .blink-pepsi {
            animation: blinkColor 1.5s linear infinite;
        }
        /* Footer links always white, even in light mode */
        body.light-mode footer a.text-white {
            color: #fff !important;
        }
    </style>
    <!-- Main Content -->
    <main class="pt-24 pb-16">
        <div class="container mx-auto px-6">
            <div class="max-w-4xl mx-auto">
                <!-- Hero Section -->
                <div class="mb-12">
                    <h1 class="text-4xl md:text-5xl font-bold mb-6">POLÍTICA DE PRIVACIDAD DE PEPSICO EUROPA</h1>
                    <p class="text-gray-400 mb-4">Última actualización: Mar 27, 2025</p>
                </div>

                <!-- Introduction -->
                <div class="space-y-6 mb-12 text-gray-300">
                    <p>Sabemos que la privacidad es importante para ti y estamos comprometidos a proteger tus datos personales cuando interactúas con las marcas de PepsiCo.</p>
                    <p>En esta política de privacidad, explicamos cómo recopilamos, usamos y protegemos tus datos personales. También describimos cómo puedes tomar decisiones y tomar el control.</p>
                    <p>Hay muchos detalles a continuación para que tenga la información que necesita. Sin embargo, si tiene poco tiempo, hay algunos puntos particularmente importantes:</p>
                </div>

                <!-- Info Cards Grid -->
                <div class="grid md:grid-cols-2 gap-8 mb-12">
                    <!-- Card 1 -->
                    <div class="bg-black/20 p-6 rounded-lg border border-white/10">
                        <h3 class="text-xl font-bold mb-4">Recopilamos y utilizamos tu información personal</h3>
                        <a href="#" class="text-pepsiBlue hover:underline">Conoce qué recopilamos, por qué y cómo se recopila y utiliza</a>
                    </div>

                    <!-- Card 2 -->
                    <div class="bg-black/20 p-6 rounded-lg border border-white/10">
                        <h3 class="text-xl font-bold mb-4">Tienes derechos y opciones</h3>
                        <a href="#" class="text-pepsiBlue hover:underline">Aprende a ejercer tus derechos y opciones</a>
                    </div>

                    <!-- Card 3 -->
                    <div class="bg-black/20 p-6 rounded-lg border border-white/10">
                        <h3 class="text-xl font-bold mb-4">Compartimos tu información personal con determinados terceros</h3>
                        <a href="#" class="text-pepsiBlue hover:underline">Conozca con quién compartimos tu información</a>
                    </div>

                    <!-- Card 4 -->
                    <div class="bg-black/20 p-6 rounded-lg border border-white/10">
                        <h3 class="text-xl font-bold mb-4">Puedes ponerte en contacto con nosotros en cualquier momento</h3>
                        <a href="#" class="text-pepsiBlue hover:underline">Cómo ponerse en contacto</a>
                    </div>
                </div>

                <!-- Privacy Sections -->
                <div class="space-y-8">
                    <div class="border-b border-white/10 py-4">
                        <button class="w-full flex items-center justify-between text-left">
                            <h2 class="text-xl font-bold">1. Alcance de esta Política</h2>
                            <i class="fas fa-plus text-pepsiBlue"></i>
                        </button>
                    </div>

                    <div class="border-b border-white/10 py-4">
                        <button class="w-full flex items-center justify-between text-left">
                            <h2 class="text-xl font-bold">2. ¿Quiénes somos?</h2>
                            <i class="fas fa-plus text-pepsiBlue"></i>
                        </button>
                    </div>

                    <div class="border-b border-white/10 py-4">
                        <button class="w-full flex items-center justify-between text-left">
                            <h2 class="text-xl font-bold">3. ¿Qué datos personales recopilamos?</h2>
                            <i class="fas fa-plus text-pepsiBlue"></i>
                        </button>
                    </div>

                    <div class="border-b border-white/10 py-4">
                        <button class="w-full flex items-center justify-between text-left">
                            <h2 class="text-xl font-bold">4. ¿Recopilamos datos sensibles sobre ti?</h2>
                            <i class="fas fa-plus text-pepsiBlue"></i>
                        </button>
                    </div>

                    <div class="border-b border-white/10 py-4">
                        <button class="w-full flex items-center justify-between text-left">
                            <h2 class="text-xl font-bold">5. ¿Con quién se compartirán tus datos?</h2>
                            <i class="fas fa-plus text-pepsiBlue"></i>
                        </button>
                    </div>

                    <div class="border-b border-white/10 py-4">
                        <button class="w-full flex items-center justify-between text-left">
                            <h2 class="text-xl font-bold">6. ¿Cómo puedes controlar tus datos personales?</h2>
                            <i class="fas fa-plus text-pepsiBlue"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Help Bubble Button and Popup -->
    <button class="help-bubble-btn" id="helpBubbleBtn" title="Ayuda"
        style="position: fixed; left: 32px; bottom: 32px; z-index: 9999; background: #004B93; color: #fff; border: none; border-radius: 50%; width: 60px; height: 60px; box-shadow: 0 4px 16px rgba(0,0,0,0.18); display: flex; align-items: center; justify-content: center; font-size: 2rem; cursor: pointer; transition: background 0.2s;">
        <i class="fas fa-question"></i>
    </button>
    <div class="help-bubble-popup" id="helpBubblePopup"
        style="position: fixed; left: 110px; bottom: 50px; z-index: 10000; background: #fff; color: #222; border-radius: 16px; box-shadow: 0 8px 32px rgba(0,0,0,0.18); padding: 1.5rem 2rem 1.5rem 1.5rem; min-width: 260px; max-width: 320px; display: none; flex-direction: column; gap: 1rem; font-size: 1rem; border-left: 6px solid #004B93;">
        <button class="close-help-bubble" id="closeHelpBubble" title="Cerrar" style="position: absolute; top: 10px; right: 18px; background: none; border: none; color: #888; font-size: 1.3rem; cursor: pointer;">&times;</button>
        <h4 style="margin:0 0 0.5rem 0;font-size:1.15rem;color:#004B93;font-weight:bold;">¿Necesitas ayuda?</h4>
        <ul style="margin:0 0 0.5rem 0;padding-left:1.2em;">
            <li style="margin-bottom:0.4em;font-size:0.98em;">¿Tienes dudas sobre la privacidad? <a href="faq.php">Consulta las preguntas frecuentes</a>.</li>
            <li style="margin-bottom:0.4em;font-size:0.98em;">¿Problemas técnicos? <a href="mailto:contacto@pepsi.com">Escríbenos por email</a>.</li>
            <li style="margin-bottom:0.4em;font-size:0.98em;">¿Prefieres WhatsApp? <a href="https://wa.me/5218445404397" target="_blank">Contáctanos aquí</a>.</li>
        </ul>
        <div style="font-size:0.95em;color:#666;">O utiliza el chat flotante para hablar con nuestro bot.</div>
    </div>

    <!-- Chatbase Agent Bubble -->
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

    <!-- Footer -->
    <footer class="bg-black text-white py-8">
        <div class="container mx-auto px-6">
            <div class="flex flex-col items-center justify-between space-y-8">
                <!-- Logo -->
                <div class="text-4xl font-bold blink-pepsi">PEPSI</div>

                <!-- Social Links -->
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

                <!-- Footer Links -->
                <div class="flex flex-wrap justify-center gap-8 text-sm uppercase tracking-wider">
                    <a href="#" class="text-white hover:text-pepsiBlue transition-colors">Condiciones de uso</a>
                    <a href="#" class="text-white hover:text-pepsiBlue transition-colors">Bases legales</a>
                    <a href="#" class="text-white hover:text-pepsiBlue transition-colors">Privacidad</a>
                    <a href="contact.php" class="text-white hover:text-pepsiBlue transition-colors">Contacto</a>
                    <a href="faq.php" class="text-white hover:text-pepsiBlue transition-colors">FAQ</a>
                    <a href="sitemap.php" class="text-white hover:text-pepsiBlue transition-colors">Mapa del sitio</a>
                </div>

                <!-- Copyright -->
                <div class="text-sm text-gray-500">
                    © 2022 PEPSICO, INC.
                </div>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Theme management
            const darkModeToggle = document.getElementById('darkModeToggle');
            const moonIcon = darkModeToggle.querySelector('.fa-moon');
            let isDarkMode = localStorage.getItem('darkMode') !== null 
                ? localStorage.getItem('darkMode') === 'true' 
                : true;

            function updateTheme() {
                document.body.classList.toggle('dark-mode', isDarkMode);
                document.body.style.backgroundColor = isDarkMode ? '#000000' : '#ffffff';
                document.body.style.color = isDarkMode ? '#ffffff' : '#000000';

                const header = document.querySelector('header');
                header.classList.toggle('bg-black/90', isDarkMode);
                header.classList.toggle('bg-white/90', !isDarkMode);
                header.classList.toggle('border-white/10', isDarkMode);
                header.classList.toggle('border-black/10', !isDarkMode);

                moonIcon.classList.toggle('fa-moon', isDarkMode);
                moonIcon.classList.toggle('fa-sun', !isDarkMode);

                const navLinks = document.querySelectorAll('a:not(.text-pepsiBlue), button:not(.text-pepsiBlue)');
                navLinks.forEach(link => {
                    link.classList.toggle('text-white', isDarkMode);
                    link.classList.toggle('text-black', !isDarkMode);
                });
            }

            // Apply initial theme
            updateTheme();

            darkModeToggle.addEventListener('click', () => {
                isDarkMode = !isDarkMode;
                localStorage.setItem('darkMode', isDarkMode);
                updateTheme();
            });

            // Mobile menu toggle
            const mobileMenuButton = document.querySelector('.md\\:hidden .fa-bars').parentElement;
            const mobileMenu = document.querySelector('.md\\:hidden.hidden');
            
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

            // Privacy section toggles
            const privacySections = document.querySelectorAll('.border-b button');
            
            privacySections.forEach(section => {
                section.addEventListener('click', () => {
                    const icon = section.querySelector('i');
                    icon.classList.toggle('fa-plus');
                    icon.classList.toggle('fa-minus');
                });
            });

            // Help bubble logic
            const helpBtn = document.getElementById('helpBubbleBtn');
            const helpPopup = document.getElementById('helpBubblePopup');
            const closeHelp = document.getElementById('closeHelpBubble');
            let helpOpen = false;

            function openHelp() {
                helpPopup.style.display = 'flex';
                helpOpen = true;
            }
            function closeHelpPopup() {
                helpPopup.style.display = 'none';
                helpOpen = false;
            }
            helpBtn.addEventListener('click', function() {
                if (helpOpen) {
                    closeHelpPopup();
                } else {
                    openHelp();
                }
            });
            closeHelp.addEventListener('click', closeHelpPopup);

            document.addEventListener('mousedown', function(e) {
                if (helpOpen && !helpPopup.contains(e.target) && e.target !== helpBtn) {
                    closeHelpPopup();
                }
            });

            // Chat bubble logic
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
        });
    </script>
<!-- Chatbase widget -->
<script>
  window.chatbaseConfig = {
    chatbotId: "Qw3nQwWw3nQwWw3nQwWw3nQwWw3nQwWw",
  }
</script>
<script src="https://www.chatbase.co/embed.min.js" id="chatbase-script" defer></script>
</body>
</html>