<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapa del Sitio | Pepsi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #000; color: #fff; font-family: 'Inter', sans-serif; }
        .sitemap-list a { color: #004B93; }
        .sitemap-list a:hover { text-decoration: underline; color: #1d9bf0; }
        .sitemap-section { margin-bottom: 2rem; }
    </style>
</head>
<body class="bg-black text-white min-h-screen flex flex-col">
    <header class="w-full bg-black py-4 px-8 flex items-center justify-between shadow-md">
        <a href="index.php" class="text-2xl font-bold text-pepsiBlue tracking-wider">PEPSI</a>
        <nav class="flex items-center space-x-6">
            <a href="index.php" class="hover:text-pepsiBlue">Inicio</a>
            <a href="products.php" class="hover:text-pepsiBlue">Productos</a>
            <a href="contact.php" class="hover:text-pepsiBlue">Contacto</a>
            <a href="faq.php" class="hover:text-pepsiBlue">FAQ</a>
            <a href="privacy.php" class="hover:text-pepsiBlue">Privacidad</a>
            <a href="login.php" class="hover:text-pepsiBlue">Iniciar Sesión</a>
            <a href="register.php" class="hover:text-pepsiBlue">Registro</a>
        </nav>
    </header>
    <main class="flex-1 flex flex-col items-center justify-center py-12">
        <h1 class="text-4xl font-bold mb-8 text-pepsiBlue">Mapa del Sitio</h1>
        <div class="sitemap-list max-w-xl w-full mx-auto bg-white/10 rounded-lg p-8 shadow-lg">
            <div class="sitemap-section">
                <h2 class="text-xl font-semibold mb-2">Secciones principales</h2>
                <ul class="list-disc pl-6 space-y-1">
                    <li><a href="index.php">Inicio</a></li>
                    <li><a href="products.php">Productos</a></li>
                    <li><a href="contact.php">Contacto</a></li>
                    <li><a href="faq.php">Preguntas Frecuentes</a></li>
                    <li><a href="privacy.php">Política de Privacidad</a></li>
                    <li><a href="login.php">Iniciar Sesión</a></li>
                    <li><a href="register.php">Registro</a></li>
                    <li><a href="sitemap.php">Mapa del Sitio</a></li>
                </ul>
            </div>
            <div class="sitemap-section">
                <h2 class="text-xl font-semibold mb-2">Secciones secundarias</h2>
                <ul class="list-disc pl-6 space-y-1">
                    <li><a href="products.php#bebidas">Bebidas</a></li>
                    <li><a href="faq.php#pagos">Pagos</a></li>
                    <li><a href="contact.php#soporte">Soporte</a></li>
                </ul>
            </div>
            <div class="sitemap-section">
                <h2 class="text-xl font-semibold mb-2">Elementos adicionales</h2>
                <ul class="list-disc pl-6 space-y-1">
                    <li><a href="https://www.facebook.com/pepsi" target="_blank">Facebook</a></li>
                    <li><a href="https://www.instagram.com/pepsi" target="_blank">Instagram</a></li>
                    <li><a href="https://www.twitter.com/pepsi" target="_blank">Twitter</a></li>
                    <li><a href="privacy.php#cookies">Política de Cookies</a></li>
                </ul>
            </div>
        </div>
    </main>
    <!-- Chatbase widget -->
    <script>
      window.chatbaseConfig = {
        chatbotId: "Qw3nQwWw3nQwWw3nQwWw3nQwWw3nQwWw",
      }
    </script>
    <script src="https://www.chatbase.co/embed.min.js" id="chatbase-script" defer></script>
</body>
</html>
