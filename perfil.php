<?php
session_start();
// Cabeceras para evitar caché y navegación atrás
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: 0');
if (!isset($_SESSION['empleado_id'])) {
    header('Location: index.php');
    exit;
}
?>
<script>
// Evitar volver atrás tras logout
if (window.history && window.history.pushState) {
    window.history.pushState(null, '', window.location.href);
    window.onpopstate = function () {
        window.location.replace('index.php');
    };
}
</script>
<?php
$host = 'sql303.byetcluster.com';
$user = 'ezyro_39184205';
$pass = 'ae6b6e7c9';
$db = 'ezyro_39184205_saberpepsi';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die('Error de conexión a la base de datos.');
}
$id = $_SESSION['empleado_id'];
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $nuevoCorreo = trim($_POST['correo'] ?? '');
    $nuevoPassword = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $updates = [];
    $params = [];
    $types = '';

    if ($nuevoCorreo) {
        $updates[] = 'correo=?';
        $params[] = $nuevoCorreo;
        $types .= 's';
    }
    if ($nuevoPassword) {
        if (strlen($nuevoPassword) < 8) {
            $error = 'La contraseña debe tener al menos 8 caracteres.';
        } elseif ($nuevoPassword !== $confirmPassword) {
            $error = 'Las contraseñas no coinciden.';
        } else {
            $updates[] = 'password=?';
            $params[] = password_hash($nuevoPassword, PASSWORD_DEFAULT);
            $types .= 's';
        }
    }
    if (!$error && $updates) {
        $sql = "UPDATE empleados SET " . implode(',', $updates) . " WHERE id=?";
        $params[] = $id;
        $types .= 'i';
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        if ($stmt->execute()) {
            $success = 'Perfil actualizado correctamente.';
        } else {
            $error = 'Error al actualizar el perfil.';
        }
        $stmt->close();
    }
}

// Recargar datos actualizados
$stmt = $conn->prepare("SELECT usuario, nombre, correo, numero, imagen FROM empleados WHERE id=?");
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->bind_result($usuario, $nombre, $correo, $numero, $imagen);
$stmt->fetch();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario | Pepsi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        pepsiBlue: '#004B93',
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .perfil-label { color: #004B93; font-weight: 600; }
        .perfil-input { background: #f3f3f3; border: 1px solid #d1d5db; }
        .perfil-input[readonly] { background: #e5e7eb; color: #888; }
        .toggle-password {
            cursor: pointer;
            position: absolute;
            right: 2.5rem;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
        }
        .edit-password-btn {
            cursor: pointer;
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #004B93;
            background: none;
            border: none;
            padding: 0;
        }
        @media (max-width: 768px) {
            .content { padding: 1rem; }
        }
    </style>
</head>
<body class="bg-[#fdf2f8] min-h-screen flex flex-col items-center justify-center">
    <!-- Navbar idéntico al de index.php -->
    <header class="fixed top-0 left-0 right-0 z-50 bg-black/90 backdrop-blur-sm border-b border-white/10 transition-colors duration-300">
        <nav class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <!-- Logo -->
                <div class="flex-shrink-0">
                    <a href="dashboard.php">
                        <h1 class="text-3xl font-bold text-white tracking-wider">PEPSI</h1>
                    </a>
                </div>
                <!-- Navigation Links - Centered -->
                <div class="hidden md:flex items-center justify-center flex-1 space-x-6">
                    <!-- Enlaces eliminados -->
                </div>
                <!-- Right Icons -->
                <div class="flex items-center space-x-4">
                    <button class="text-white hover:text-pepsiBlue transition-colors" id="darkModeToggle">
                        <i class="fas fa-moon text-xl"></i>
                    </button>
                    <!-- Mobile Menu Button -->
                    <button class="md:hidden text-white hover:text-pepsiBlue transition-colors" id="mobileMenuBtn">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
            <!-- Mobile Menu (Hidden by default) -->
            <div id="mobileMenu" class="md:hidden hidden pt-4 transform transition-all duration-300 ease-in-out opacity-0">
                <div class="flex flex-col space-y-3">
                    <!-- Enlaces eliminados -->
                </div>
            </div>
        </nav>
    </header>
    <main class="flex-1 w-full flex flex-col items-center justify-center py-12 pt-32">
        <!-- Breadcrumb -->
        <nav class="w-full max-w-md mb-6" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-300">
                <li>
                    <a href="index.php" class="hover:text-pepsiBlue dark:hover:text-white flex items-center">
                        <i class="fas fa-home mr-1"></i> Inicio
                    </a>
                </li>
                <li>
                    <span class="mx-2">/</span>
                </li>
                <li class="text-pepsiBlue dark:text-white font-semibold">Perfil</li>
            </ol>
        </nav>
        <div class="bg-white dark:bg-gray-900 shadow-lg rounded-xl p-8 w-full max-w-md content relative">
            <h2 class="text-2xl font-bold text-center text-pepsiBlue dark:text-white mb-8">Mi Perfil</h2>
            <form class="w-full space-y-5" autocomplete="off">
                <div class="mb-6">
                    <label class="perfil-label block mb-1" for="nombre">Nombre</label>
                    <input id="nombre" type="text" class="perfil-input w-full px-4 py-2 rounded-lg" value="Gabriel Pérez" readonly>
                </div>
                <div class="mb-6">
                    <label class="perfil-label block mb-1" for="email">Correo electrónico</label>
                    <input id="email" type="email" class="perfil-input w-full px-4 py-2 rounded-lg" value="gabriel@ejemplo.com" readonly>
                </div>
                <div class="mb-6 relative">
                    <label class="perfil-label block mb-1" for="password">Contraseña</label>
                    <input id="password" type="password" class="perfil-input w-full px-4 py-2 rounded-lg pr-12" value="12345678" readonly>
                    <span class="toggle-password" id="togglePassword" style="display:none;"><i class="fas fa-eye"></i></span>
                    <button type="button" class="edit-password-btn" id="editPasswordBtn" title="Editar contraseña"><i class="fas fa-edit"></i></button>
                </div>
                <div class="mb-6">
                    <label class="perfil-label block mb-1" for="telefono">Teléfono</label>
                    <input id="telefono" type="text" class="perfil-input w-full px-4 py-2 rounded-lg" value="+52 55 1234 5678" readonly>
                </div>
                <div class="mb-6">
                    <label class="perfil-label block mb-1" for="fecha">Fecha de nacimiento</label>
                    <input id="fecha" type="text" class="perfil-input w-full px-4 py-2 rounded-lg" value="1990-01-01" readonly>
                </div>
                <div class="flex justify-center mt-8">
                    <a href="logout.php" class="bg-pepsiBlue hover:bg-blue-800 text-white font-semibold px-6 py-2 rounded-lg transition-colors">Cerrar sesión</a>
                </div>
            </form>
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
            <li style="margin-bottom:0.4em;font-size:0.98em;">¿Dudas sobre tu perfil? <a href="faq.php">Consulta las preguntas frecuentes</a>.</li>
            <li style="margin-bottom:0.4em;font-size:0.98em;">¿Problemas técnicos? <a href="mailto:contacto@pepsi.com">Escríbenos por email</a>.</li>
            <li style="margin-bottom:0.4em;font-size:0.98em;">¿Prefieres WhatsApp? <a href="https://wa.me/5218445404397" target="_blank">Contáctanos aquí</a>.</li>
        </ul>
        <div style="font-size:0.95em;color:#666;">O utiliza el chat flotante para hablar con nuestro bot.</div>
    </div>

    <!-- Chatbase Agent Bubble -->
    <button id="chat-bubble-btn"
        class="fixed bottom-4 right-4 z-[9999] w-16 h-16 rounded-full bg-[#004B93] text-white flex items-center justify-center shadow-lg hover:bg-blue-700 transition-all"
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
    // --- Modo oscuro persistente y sincronizado ---
    (function() {
        const darkModeToggle = document.getElementById('darkModeToggle');
        const moonIcon = darkModeToggle.querySelector('i');
        function setThemeFromStorage() {
            let isDarkMode = localStorage.getItem('darkMode') !== 'false';
            document.body.classList.toggle('dark-mode', isDarkMode);
            document.documentElement.classList.toggle('dark', isDarkMode);
            document.body.style.backgroundColor = isDarkMode ? '#18181b' : '#f7f8fa';
            document.body.style.color = isDarkMode ? '#fff' : '#222';
            if (moonIcon) {
                moonIcon.classList.toggle('fa-moon', isDarkMode);
                moonIcon.classList.toggle('fa-sun', !isDarkMode);
                moonIcon.classList.toggle('text-pepsiBlue', !isDarkMode);
                moonIcon.classList.toggle('text-yellow-400', isDarkMode);
            }
        }
        setThemeFromStorage();
        darkModeToggle.addEventListener('click', () => {
            const isDarkMode = !document.body.classList.contains('dark-mode');
            localStorage.setItem('darkMode', isDarkMode);
            setThemeFromStorage();
        });
        // Sincroniza el modo entre pestañas/ventanas
        window.addEventListener('storage', (e) => {
            if (e.key === 'darkMode') setThemeFromStorage();
        });
    })();

        // Navbar mobile menu toggle
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');
        if (mobileMenuBtn && mobileMenu) {
            mobileMenuBtn.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
                mobileMenu.classList.toggle('opacity-0');
            });
        }

        // Contraseña: solo editable tras hacer clic en el icono de editar
        const passwordInput = document.getElementById('password');
        const editPasswordBtn = document.getElementById('editPasswordBtn');
        const togglePassword = document.getElementById('togglePassword');
        if (editPasswordBtn && passwordInput && togglePassword) {
            editPasswordBtn.addEventListener('click', () => {
                passwordInput.readOnly = false;
                passwordInput.focus();
                togglePassword.style.display = 'inline';
                editPasswordBtn.style.display = 'none';
            });
            // Mostrar/ocultar contraseña
            togglePassword.addEventListener('click', () => {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    togglePassword.innerHTML = '<i class="fas fa-eye-slash"></i>';
                } else {
                    passwordInput.type = 'password';
                    togglePassword.innerHTML = '<i class="fas fa-eye"></i>';
                }
            });
        }
    </script>
</body>
</html>
