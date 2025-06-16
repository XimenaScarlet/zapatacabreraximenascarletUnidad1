<?php
session_start();
$ya_logueado = isset($_SESSION['empleado_id']);
if ($ya_logueado) {
    // Redirige automáticamente si ya está logueado
    header('Location: /saberpepsi/dash.php');
    exit;
}
$error = '';
if (!$ya_logueado && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $conn = new mysqli('sql303.byetcluster.com', 'ezyro_39184205', 'ae6b6e7c9', 'ezyro_39184205_saberpepsi');
    if ($conn->connect_error) {
        $error = 'Error de conexión a la base de datos.';
    } else {
        $stmt = $conn->prepare("SELECT id, usuario, nombre, correo, password, verificado FROM empleados WHERE correo = ?");
        $stmt->bind_param('s', $correo);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            // Solo permite login si verificado es exactamente 1
            if (!isset($row['verificado']) || intval($row['verificado']) !== 1) {
                $error = 'Tu cuenta aún no ha sido verificada. Por favor revisa tu correo o WhatsApp para el código de confirmación.';
            } elseif (password_verify($password, $row['password'])) {
                session_regenerate_id(true);
                $_SESSION['empleado_id'] = $row['id'];
                $_SESSION['empleado_nombre'] = $row['nombre'];
                $_SESSION['empleado_correo'] = $row['correo'];
                $_SESSION['empleado_usuario'] = $row['usuario'];
                // Redirección segura: usar header si es posible, si no, solo JS
                if (!headers_sent()) {
                    header('Location: /saberpepsi/dash.php');
                    exit;
                } else {
                    echo '<script>window.location.href="/saberpepsi/dash.php";</script>';
                    exit;
                }
            } else {
                $error = 'Correo o contraseña incorrectos.';
            }
        } else {
            $error = 'Correo o contraseña incorrectos.';
        }
        $stmt->close();
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pepsi</title>
    <script>
        // Aplica el modo oscuro lo antes posible según localStorage
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
        body {
            background-color: #000000;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .form-input {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }
        .form-input:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: #004B93;
        }
        .login-button {
            background: #004B93;
            transition: all 0.3s ease;
        }
        .login-button:hover {
            background: #003366;
            transform: translateY(-2px);
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
</head>
<body class="bg-black text-white font-sans min-h-screen flex flex-col items-center justify-center py-12">
    <!-- Navbar centrado con modo oscuro/claro a la derecha -->
    <nav class="w-full bg-black py-4 px-8 flex items-center justify-between shadow-md fixed top-0 left-0 z-40">
        <!-- Logo a la izquierda -->
        <div class="flex items-center flex-shrink-0">
            <a href="index.php" class="text-2xl font-bold text-pepsiBlue tracking-wider">PEPSI</a>
        </div>
        <!-- Enlaces centrados -->
        <div class="flex-1 flex items-center justify-center space-x-8">
            <a href="index.php" class="text-white hover:text-pepsiBlue transition-colors">Inicio</a>
            <a href="products.php" class="text-white hover:text-pepsiBlue transition-colors">Productos</a>
            <a href="contact.php" class="text-white hover:text-pepsiBlue transition-colors">Contacto</a>
            <a href="faq.php" class="text-white hover:text-pepsiBlue transition-colors">FAQ</a>
            <a href="privacy.php" class="text-white hover:text-pepsiBlue transition-colors">Privacidad</a>
        </div>
        <!-- Botón modo oscuro/claro a la derecha -->
        <div class="flex items-center space-x-4">
            <button id="darkModeToggle" class="text-white hover:text-pepsiBlue transition-colors" aria-label="Cambiar modo oscuro/claro">
                <i class="fas fa-moon text-xl"></i>
            </button>
        </div>
    </nav>

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
                if (i > 0) html += `<li class="text-white dark:text-white light:text-black">/</li>`;
                if (i < history.length - 1) {
                    html += `<li><a href="${history[i].href}" class="hover:underline text-pepsiBlue">${history[i].label}</a></li>`;
                } else {
                    html += `<li class="text-white dark:text-white light:text-black" aria-current="page">${history[i].label}</li>`;
                }
            }
            breadcrumbList.innerHTML = html;
        })();

        // Dark/Light mode toggle
        document.addEventListener('DOMContentLoaded', function() {
            const darkModeToggle = document.getElementById('darkModeToggle');
            const moonIcon = darkModeToggle.querySelector('.fa-moon, .fa-sun');
            let isDarkMode = localStorage.getItem('darkMode') !== null 
                ? localStorage.getItem('darkMode') === 'true' 
                : true;

            function updateTheme() {
                document.body.classList.toggle('light-mode', !isDarkMode);
                document.body.classList.toggle('dark-mode', isDarkMode);
                document.body.style.backgroundColor = isDarkMode ? '#000000' : '#ffffff';
                document.body.style.color = isDarkMode ? '#ffffff' : '#000000';
                if (moonIcon) {
                    moonIcon.classList.toggle('fa-moon', isDarkMode);
                    moonIcon.classList.toggle('fa-sun', !isDarkMode);
                }
                // Breadcrumb color
                document.querySelectorAll('.breadcrumb-list li, .breadcrumb-list li[aria-current="page"]').forEach(li => {
                    li.style.color = isDarkMode ? '#fff' : '#222';
                });
                document.querySelectorAll('.breadcrumb-list a').forEach(a => {
                    a.style.color = isDarkMode ? '#004B93' : '#004B93';
                });
            }

            updateTheme();

            darkModeToggle.addEventListener('click', () => {
                isDarkMode = !isDarkMode;
                localStorage.setItem('darkMode', isDarkMode);
                updateTheme();
            });
        });
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

    <div class="w-full max-w-md p-8">
        <!-- Logo -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-white tracking-wider">PEPSI</h1>
            <p class="text-gray-400 mt-2">Iniciar Sesión</p>
        </div>

        <!-- Mostrar error si existe -->
        <?php if ($error): ?>
            <script>
                alert("<?= addslashes($error) ?>");
            </script>
        <?php endif; ?>

        <!-- Login Form -->
        <form class="space-y-6" method="post" autocomplete="off">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-300 mb-2">Correo electrónico</label>
                <input type="email" id="email" name="email" required 
                    class="form-input w-full px-4 py-3 rounded-lg text-white placeholder-gray-400"
                    placeholder="tu@email.com"
                    autocomplete="email">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-300 mb-2">Contraseña</label>
                <input type="password" id="password" name="password" required 
                    class="form-input w-full px-4 py-3 rounded-lg text-white placeholder-gray-400"
                    placeholder="••••••••"
                    autocomplete="current-password">
            </div>
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input type="checkbox" id="remember" name="remember" 
                        class="h-4 w-4 rounded border-gray-600 bg-gray-800 text-pepsiBlue focus:ring-pepsiBlue">
                    <label for="remember" class="ml-2 block text-sm text-gray-300">
                        Recordarme
                    </label>
                </div>
                <a href="#" id="forgotLink" class="text-sm text-pepsiBlue hover:underline">
                    ¿Olvidaste tu contraseña?
                </a>
            </div>
            <button type="submit" class="login-button w-full py-3 px-4 rounded-lg text-white font-medium shadow-lg">
                Iniciar Sesión
            </button>
            <div class="text-center mt-4">
                <p class="text-gray-400">¿No tienes una cuenta?
                    <a href="register.php" class="text-pepsiBlue hover:underline ml-1">Regístrate</a>
                </p>
            </div>
        </form>
        <!-- Back to Home -->
        <div class="text-center mt-8">
            <a href="index.php" class="text-gray-400 hover:text-white transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Volver al inicio
            </a>
        </div>
    </div>

    <!-- Popup: Recuperar contraseña -->
    <div id="forgotPopup" class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-60 hidden">
        <div class="bg-white text-black rounded-lg shadow-lg p-6 flex flex-col items-center relative max-w-xs w-full">
            <button onclick="document.getElementById('forgotPopup').style.display='none'" class="absolute top-2 right-2 text-xl text-gray-400 hover:text-red-600">&times;</button>
            <i class="fas fa-key text-3xl mb-2 text-pepsiBlue"></i>
            <div class="font-bold text-lg mb-2">Recuperar contraseña</div>
            <form id="forgotForm" class="w-full space-y-4" autocomplete="off" onsubmit="return false;">
                <label class="block text-sm font-medium mb-1">Usuario</label>
                <input type="text" name="usuario" class="form-input w-full px-3 py-2 rounded text-black" required autocomplete="username" placeholder="Tu usuario">
                <div class="flex flex-col gap-2 mt-2">
                    <label class="block text-sm font-medium mb-1">¿Cómo quieres recibir el código?</label>
                    <div class="flex gap-2">
                        <button type="button" id="forgotWhatsappBtn" class="flex-1 flex flex-col items-center justify-center border rounded px-2 py-2 cursor-pointer hover:border-pepsiBlue focus:outline-none">
                            <i class="fab fa-whatsapp text-2xl mb-1 text-pepsiBlue"></i>
                            <span class="text-xs">WhatsApp</span>
                        </button>
                        <button type="button" id="forgotEmailBtn" class="flex-1 flex flex-col items-center justify-center border rounded px-2 py-2 cursor-pointer hover:border-pepsiBlue focus:outline-none">
                            <i class="fas fa-envelope text-2xl mb-1 text-pepsiBlue"></i>
                            <span class="text-xs">Correo</span>
                        </button>
                    </div>
                </div>
                <div id="forgotError" class="text-red-600 text-sm mt-2 hidden"></div>
                <div id="forgotSuccess" class="text-green-600 text-sm mt-2 hidden"></div>
            </form>
        </div>
    </div>
    <!-- Popup: Código de recuperación -->
    <div id="recoveryCodePopup" class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-60 hidden">
        <div class="bg-white text-black rounded-lg shadow-lg p-6 flex flex-col items-center relative max-w-xs w-full">
            <button onclick="document.getElementById('recoveryCodePopup').style.display='none'" class="absolute top-2 right-2 text-xl text-gray-400 hover:text-red-600">&times;</button>
            <i class="fas fa-key text-3xl mb-2 text-pepsiBlue"></i>
            <div class="font-bold text-lg mb-2">Código de recuperación</div>
            <form id="recoveryCodeForm" class="w-full space-y-4">
                <input type="hidden" name="usuario" value="">
                <label class="block text-sm font-medium mb-1">Ingresa el código recibido</label>
                <input type="text" name="codigo" maxlength="6" pattern="[0-9]{6}" class="form-input w-full px-3 py-2 rounded text-black" required placeholder="######" autocomplete="one-time-code">
                <div id="recoveryCodeError" class="text-red-600 text-sm mt-2 hidden"></div>
                <div id="recoveryCodeSuccess" class="text-green-600 text-sm mt-2 hidden"></div>
                <button type="submit" class="bg-pepsiBlue text-white px-4 py-2 rounded w-full font-bold mt-2">Confirmar código</button>
            </form>
        </div>
    </div>
    <!-- Popup: Cambiar contraseña -->
    <div id="resetPasswordPopup" class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-60 hidden">
        <div class="bg-white text-black rounded-lg shadow-lg p-6 flex flex-col items-center relative max-w-xs w-full">
            <button onclick="document.getElementById('resetPasswordPopup').style.display='none'" class="absolute top-2 right-2 text-xl text-gray-400 hover:text-red-600">&times;</button>
            <i class="fas fa-lock text-3xl mb-2 text-pepsiBlue"></i>
            <div class="font-bold text-lg mb-2">Nueva contraseña</div>
            <form id="resetPasswordForm" class="w-full space-y-4">
                <input type="hidden" name="usuario" value="">
                <label class="block text-sm font-medium mb-1">Nueva contraseña</label>
                <input type="password" name="password" class="form-input w-full px-3 py-2 rounded text-black" required minlength="8" placeholder="••••••••" autocomplete="new-password">
                <label class="block text-sm font-medium mb-1">Confirmar contraseña</label>
                <input type="password" name="confirm" class="form-input w-full px-3 py-2 rounded text-black" required minlength="8" placeholder="••••••••" autocomplete="new-password">
                <div id="resetPasswordError" class="text-red-600 text-sm mt-2 hidden"></div>
                <div id="resetPasswordSuccess" class="text-green-600 text-sm mt-2 hidden"></div>
                <button type="submit" class="bg-pepsiBlue text-white px-4 py-2 rounded w-full font-bold mt-2">Cambiar contraseña</button>
            </form>
        </div>
    </div>

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
            <li style="margin-bottom:0.4em;font-size:0.98em;">¿Problemas para iniciar sesión? <a href="faq.php">Consulta las preguntas frecuentes</a>.</li>
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

    <script>
        // Theme management
        const isDarkMode = localStorage.getItem('darkMode') !== null 
            ? localStorage.getItem('darkMode') === 'true' 
            : true; // Default to dark mode

        function updateTheme() {
            const logo = document.querySelector('h1');
            const inputs = document.querySelectorAll('.form-input');
            const labels = document.querySelectorAll('.text-gray-300, .text-gray-400');
            const loginButton = document.querySelector('.login-button');
            const backLink = document.querySelector('a[href="index.php"]');

            if (isDarkMode) {
                document.body.style.backgroundColor = '#000000';
                document.body.style.color = '#ffffff';
                logo.classList.remove('text-black');
                logo.classList.add('text-white');
                inputs.forEach(input => {
                    input.style.background = 'rgba(255, 255, 255, 0.1)';
                    input.style.borderColor = 'rgba(255, 255, 255, 0.2)';
                    input.style.color = '#ffffff';
                });
                labels.forEach(el => {
                    el.classList.remove('text-gray-600');
                    if (el.classList.contains('text-gray-400')) {
                        el.classList.add('text-gray-400');
                    } else {
                        el.classList.add('text-gray-300');
                    }
                });
                backLink.classList.remove('text-gray-600');
                backLink.classList.add('text-gray-400');
            } else {
                document.body.style.backgroundColor = '#ffffff';
                document.body.style.color = '#000000';
                logo.classList.remove('text-white');
                logo.classList.add('text-black');
                inputs.forEach(input => {
                    input.style.background = 'rgba(0, 0, 0, 0.05)';
                    input.style.borderColor = 'rgba(0, 0, 0, 0.1)';
                    input.style.color = '#000000';
                });
                labels.forEach(el => {
                    el.classList.remove('text-gray-300', 'text-gray-400');
                    el.classList.add('text-gray-600');
                });
                backLink.classList.remove('text-gray-400');
                backLink.classList.add('text-gray-600');
            }
        }

        // Apply initial theme
        updateTheme();

        // Listen for theme changes from other pages
        window.addEventListener('storage', (e) => {
            if (e.key === 'darkMode') {
                const newDarkMode = e.newValue === 'true';
                if (newDarkMode !== isDarkMode) {
                    location.reload();
                }
            }
        });

        // Mostrar popup de recuperación al hacer clic
        document.addEventListener('DOMContentLoaded', function() {
            const forgotLink = document.getElementById('forgotLink');
            if (forgotLink) {
                forgotLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    document.getElementById('forgotPopup').style.display = 'flex';
                    document.getElementById('forgotError').classList.add('hidden');
                    document.getElementById('forgotError').innerText = '';
                    document.getElementById('forgotSuccess').classList.add('hidden');
                    document.getElementById('forgotSuccess').innerText = '';
                    document.querySelector('#forgotForm input[name="usuario"]').value = '';
                    // Desmarcar radios
                    document.querySelectorAll('#forgotForm input[name="metodo"]').forEach(r => r.checked = false);
                });
            }

            // Nuevo: envío automático al hacer clic en método
            function enviarRecuperacion(metodo) {
                const form = document.getElementById('forgotForm');
                const usuario = form.usuario.value.trim();
                const errorDiv = document.getElementById('forgotError');
                const successDiv = document.getElementById('forgotSuccess');
                errorDiv.classList.add('hidden');
                errorDiv.innerText = '';
                successDiv.classList.add('hidden');
                successDiv.innerText = '';

                if (!usuario) {
                    errorDiv.innerText = 'Ingresa tu usuario.';
                    errorDiv.classList.remove('hidden');
                    return;
                }

                fetch('empleados.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({
                        accion: 'recuperar',
                        usuario: usuario,
                        metodo: metodo
                    })
                })
                .then(r => r.json())
                .then(resp => {
                    if (resp.ok) {
                        document.getElementById('forgotPopup').style.display = 'none';
                        document.getElementById('recoveryCodePopup').style.display = 'flex';
                        document.querySelector('#recoveryCodeForm input[name="usuario"]').value = usuario;
                        document.querySelector('#recoveryCodeForm input[name="codigo"]').value = '';
                        document.getElementById('recoveryCodeError').classList.add('hidden');
                        document.getElementById('recoveryCodeError').innerText = '';
                        document.getElementById('recoveryCodeSuccess').classList.add('hidden');
                        document.getElementById('recoveryCodeSuccess').innerText = '';
                    } else {
                        errorDiv.innerText = resp.error || 'No se pudo enviar el código.';
                        errorDiv.classList.remove('hidden');
                    }
                })
                .catch(() => {
                    errorDiv.innerText = 'Error de conexión.';
                    errorDiv.classList.remove('hidden');
                });
            }

            document.getElementById('forgotWhatsappBtn').addEventListener('click', function() {
                enviarRecuperacion('whatsapp');
            });
            document.getElementById('forgotEmailBtn').addEventListener('click', function() {
                enviarRecuperacion('email');
            });

            // Eliminar el submit del forgotForm
            document.getElementById('forgotForm').addEventListener('submit', function(e) {
                e.preventDefault();
                // No hacer nada aquí
            });

            // Enviar código de recuperación por AJAX
            document.getElementById('recoveryCodeForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const usuario = this.usuario.value.trim();
                const codigo = this.codigo.value.trim();
                const errorDiv = document.getElementById('recoveryCodeError');
                const successDiv = document.getElementById('recoveryCodeSuccess');
                errorDiv.classList.add('hidden');
                errorDiv.innerText = '';
                successDiv.classList.add('hidden');
                successDiv.innerText = '';

                if (!usuario || !codigo) {
                    errorDiv.innerText = 'Ingresa el código.';
                    errorDiv.classList.remove('hidden');
                    return;
                }

                fetch('empleados.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({
                        accion: 'confirmar',
                        codigo: codigo,
                        user: usuario
                    })
                })
                .then(r => r.json())
                .then(resp => {
                    if (resp.ok) {
                        // Cerrar popup de código y abrir el de cambio de contraseña
                        document.getElementById('recoveryCodePopup').style.display = 'none';
                        document.getElementById('resetPasswordPopup').style.display = 'flex';
                        document.querySelector('#resetPasswordForm input[name="usuario"]').value = usuario;
                        document.querySelector('#resetPasswordForm input[name="password"]').value = '';
                        document.querySelector('#resetPasswordForm input[name="confirm"]').value = '';
                        document.getElementById('resetPasswordError').classList.add('hidden');
                        document.getElementById('resetPasswordError').innerText = '';
                        document.getElementById('resetPasswordSuccess').classList.add('hidden');
                        document.getElementById('resetPasswordSuccess').innerText = '';
                    } else {
                        errorDiv.innerText = resp.error || 'Código incorrecto.';
                        errorDiv.classList.remove('hidden');
                    }
                })
                .catch(() => {
                    errorDiv.innerText = 'Error de conexión.';
                    errorDiv.classList.remove('hidden');
                });
            });

            // Enviar nueva contraseña por AJAX
            document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const usuario = this.usuario.value.trim();
                const password = this.password.value;
                const confirm = this.confirm.value;
                const errorDiv = document.getElementById('resetPasswordError');
                const successDiv = document.getElementById('resetPasswordSuccess');
                errorDiv.classList.add('hidden');
                errorDiv.innerText = '';
                successDiv.classList.add('hidden');
                successDiv.innerText = '';

                if (!usuario || !password || !confirm) {
                    errorDiv.innerText = 'Completa todos los campos.';
                    errorDiv.classList.remove('hidden');
                    return;
                }
                if (password.length < 8) {
                    errorDiv.innerText = 'La contraseña debe tener al menos 8 caracteres.';
                    errorDiv.classList.remove('hidden');
                    return;
                }
                if (password !== confirm) {
                    errorDiv.innerText = 'Las contraseñas no coinciden.';
                    errorDiv.classList.remove('hidden');
                    return;
                }

                fetch('empleados.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({
                        accion: 'reset_password',
                        usuario: usuario,
                        password: password
                    })
                })
                .then(function(r) { return r.json(); })
                .then(function(resp) {
                    if (resp.ok) {
                        successDiv.innerText = '¡Contraseña cambiada! Ahora puedes iniciar sesión.';
                        successDiv.classList.remove('hidden');
                        setTimeout(() => {
                            document.getElementById('resetPasswordPopup').style.display = 'none';
                        }, 2000);
                    } else {
                        errorDiv.innerText = resp.error || 'No se pudo cambiar la contraseña.';
                        errorDiv.classList.remove('hidden');
                    }
                })
                .catch(function() {
                    errorDiv.innerText = 'Error de conexión.';
                    errorDiv.classList.remove('hidden');
                });
            });
        });

        // Help bubble logic
        document.addEventListener('DOMContentLoaded', function() {
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

        // ...existing code...
    </script>
<!-- Chatbase widget -->
<script>
  window.chatbaseConfig = {
    chatbotId: "Qw3nQwWw3nQwWw3nQwWw3nQwWw",
  }
</script>
<script src="https://www.chatbase.co/embed.min.js" id="chatbase-script" defer></script>

<footer class="bg-black text-white py-8 mt-12">
    <div class="container mx-auto px-6">
        <div class="flex flex-col items-center justify-between space-y-8">
            <div class="text-4xl font-bold blink-pepsi">PEPSI</div>
            <div class="flex flex-wrap justify-center gap-8 text-sm uppercase tracking-wider">
                <a href="#" class="text-white hover:text-pepsiBlue transition-colors">Condiciones de uso</a>
                <a href="#" class="text-white hover:text-pepsiBlue transition-colors">Bases legales</a>
                <a href="#" class="text-white hover:text-pepsiBlue transition-colors">Privacidad</a>
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
