<?php
session_start();
header('Expires: Tue, 01 Jan 2000 00:00:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

// Process contact form
$contact_success = false;
$contact_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'], $_POST['email'], $_POST['message'])) {
    require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
    require_once __DIR__ . '/PHPMailer/src/SMTP.php';
    require_once __DIR__ . '/PHPMailer/src/Exception.php';
    
    $nombre = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject'] ?? '');
    $mensaje = trim($_POST['message']);

    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'scarletgirl145@gmail.com';
        $mail->Password = 'iqjxlforynphlthk';
        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';
        $mail->setFrom('scarletgirl145@gmail.com', 'Contacto Web');
        $mail->addAddress('scarletgirl145@gmail.com', 'Admin');
        $mail->Subject = 'Nuevo mensaje de contacto: ' . $subject;
        $body = "<b>Nombre:</b> $nombre<br>"
              ."<b>Email:</b> $email<br>"
              ."<b>Asunto:</b> $subject<br>"
              ."<b>Mensaje:</b><br>".nl2br(htmlspecialchars($mensaje));
        $mail->isHTML(true);
        $mail->Body = $body;
        $mail->send();
        $contact_success = true;
    } catch (\PHPMailer\PHPMailer\Exception $e) {
        $contact_error = 'No se pudo enviar el mensaje. Intenta más tarde.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacto | pepsi</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f0f4f8 0%, #e2e8f0 100%);
            min-height: 100vh;
            color: #334155;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header */
        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            margin: 20px auto;
            max-width: 1200px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 40px;
        }

        .logo {
            font-size: 24px;
            font-weight: 700;
            color: #1e293b;
        }

        .nav-links {
            margin: 0 auto;
            display: flex;
            justify-content: center;
            flex: 1;
            gap: 40px;
            list-style: none;
        }

        .nav-links a {
            text-decoration: none;
            color: #64748b;
            font-weight: 500;
            font-size: 15px;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: #1e293b;
        }

        .contact-btn {
            background: #06b6d4;
            color: white !important;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .contact-btn:hover {
            background: #0891b2;
            transform: translateY(-1px);
        }

        /* Main Content */
        .main-content {
            padding: 60px 0;
        }

        .hero-section {
            text-align: center;
            margin-bottom: 80px;
        }

        .hero-title {
            font-size: 48px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 16px;
        }

        .hero-subtitle {
            font-size: 18px;
            color: #64748b;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }

        /* Contact Section */
        .contact-section {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 40px;
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
        }

        /* Contact Info */
        .contact-info {
            background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
            color: white;
            padding: 60px 40px;
            position: relative;
            overflow: hidden;
        }

        .contact-info::before {
            content: '';
            position: absolute;
            bottom: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .contact-info h3 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .contact-info p {
            font-size: 16px;
            opacity: 0.9;
            margin-bottom: 40px;
            line-height: 1.5;
        }

        .contact-details {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 16px;
            font-size: 16px;
        }

        .contact-icon {
            width: 20px;
            height: 20px;
            opacity: 0.9;
        }

        /* Contact Form */
        .contact-form {
            padding: 60px 40px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-bottom: 24px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-input {
            border: none;
            border-bottom: 2px solid #e2e8f0;
            padding: 16px 0;
            font-size: 16px;
            background: transparent;
            transition: border-color 0.3s ease;
            font-family: 'Inter', sans-serif;
        }

        .form-input:focus {
            outline: none;
            border-bottom-color: #06b6d4;
        }

        .form-input::placeholder {
            color: #94a3b8;
        }

        .form-textarea {
            min-height: 120px;
            resize: vertical;
            padding-top: 16px;
        }

        .submit-btn {
            background: #06b6d4;
            color: white;
            border: none;
            padding: 16px 32px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
        }

        .submit-btn:hover {
            background: #0891b2;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(6, 182, 212, 0.3);
        }

        /* Footer */
        .footer {
            background: white;
            border-radius: 20px;
            margin: 40px auto 20px;
            max-width: 1200px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .footer-logo {
            font-size: 20px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 20px;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-bottom: 20px;
        }

        .footer-links a {
            text-decoration: none;
            color: #64748b;
            font-size: 14px;
            transition: color 0.3s ease;
        }

        .footer-links a:hover {
            color: #1e293b;
        }

        .social-icons {
            display: flex;
            justify-content: center;
            gap: 16px;
        }

        .social-icon {
            width: 40px;
            height: 40px;
            background: #f1f5f9;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .social-icon:hover {
            background: #06b6d4;
            color: white;
            transform: translateY(-2px);
        }

        /* Success/Error Messages */
        .message {
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-weight: 500;
        }

        .message.success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .message.error {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .nav {
                padding: 16px 20px;
            }

            .nav-links {
                display: none;
            }

            .hero-title {
                font-size: 36px;
            }

            .contact-section {
                grid-template-columns: 1fr;
                margin: 0 20px;
            }

            .contact-info,
            .contact-form {
                padding: 40px 30px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .footer-links {
                flex-wrap: wrap;
                gap: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <nav class="nav">
            <div class="logo">Pepsi</div>
            <ul class="nav-links" style="margin: 0 auto; display: flex; justify-content: center; flex: 1; gap: 40px; list-style: none;">
                <li><a href="index.php">Inicio</a></li>
                <li><a href="#services">FAQ</a></li>
                <li><a href="#stories">Privacidad</a></li>
            </ul>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <!-- Hero Section -->
            <section class="hero-section">
                <h1 class="hero-title">Contáctanos</h1>
                <p class="hero-subtitle">
                    ¡Estamos listos para ayudarte! Envíanos un mensaje y te responderemos lo antes posible.
                </p>
            </section>

            <!-- Contact Section -->
            <section class="contact-section">
                <!-- Contact Information -->
                <div class="contact-info">
                    <h3>Información de Contacto</h3>
                    <p>¡Escríbenos o inicia un chat en vivo!</p>
                    <div class="contact-details">
                        <div class="contact-item">
                            <svg class="contact-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                            </svg>
                            +52 844 540 4397
                        </div>
                        <div class="contact-item">
                            <svg class="contact-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                            </svg>
                            scarletgirl145@gmail.com
                        </div>
                        <div class="contact-item">
                            <svg class="contact-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                            </svg>
                            Saltillo, Coahuila, México
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="contact-form">
                    <?php if ($contact_success): ?>
                        <div class="message success">
                            ¡Mensaje enviado correctamente! Te responderemos pronto.
                        </div>
                    <?php elseif ($contact_error): ?>
                        <div class="message error">
                            <?php echo htmlspecialchars($contact_error); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="form-grid">
                            <div class="form-group">
                                <input type="text" name="name" class="form-input" placeholder="Nombre completo" required>
                            </div>
                            <div class="form-group">
                                <input type="email" name="email" class="form-input" placeholder="Correo electrónico" required>
                            </div>
                        </div>
                        <div class="form-group full-width">
                            <input type="text" name="subject" class="form-input" placeholder="Asunto" required>
                        </div>
                        <div class="form-group full-width">
                            <textarea name="message" class="form-input form-textarea" placeholder="Escribe aquí tu mensaje..." required></textarea>
                        </div>
                        <button type="submit" class="submit-btn">Enviar Mensaje</button>
                    </form>
                </div>
            </section>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-logo">saberpepsi</div>
        <div class="footer-links">
            <a href="#terms">Términos</a>
            <a href="#privacy">Privacidad</a>
            <a href="#cookies">Cookies</a>
        </div>
        <div class="social-icons">
            <a href="#" class="social-icon">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.012 8.012 0 0 0 16 8c0-4.42-3.58-8-8-8z"/>
                </svg>
            </a>
            <a href="#" class="social-icon">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M0 1.146C0 .513.526 0 1.175 0h13.65C15.474 0 16 .513 16 1.146v13.708c0 .633-.526 1.146-1.175 1.146H1.175C.526 16 0 15.487 0 14.854V1.146zm4.943 12.248V6.169H2.542v7.225h2.401zm-1.2-8.212c.837 0 1.358-.554 1.358-1.248-.015-.709-.52-1.248-1.342-1.248-.822 0-1.359.54-1.359 1.248 0 .694.521 1.248 1.327 1.248h.016zm4.908 8.212V9.359c0-.216.016-.432.08-.586.173-.431.568-.878 1.232-.878.869 0 1.216.662 1.216 1.634v3.865h2.401V9.25c0-2.22-1.184-3.252-2.764-3.252-1.274 0-1.845.7-2.165 1.193v.025h-.016a5.54 5.54 0 0 1 .016-.025V6.169h-2.4c.03.678 0 7.225 0 7.225h2.4z"/>
                </svg>
            </a>
            <a href="#" class="social-icon">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M5.026 15c6.038 0 9.341-5.003 9.341-9.334 0-.14 0-.282-.006-.422A6.685 6.685 0 0 0 16 3.542a6.658 6.658 0 0 1-1.889.518 3.301 3.301 0 0 0 1.447-1.817 6.533 6.533 0 0 1-2.087.793A3.286 3.286 0 0 0 7.875 6.03a9.325 9.325 0 0 1-6.767-3.429 3.289 3.289 0 0 0 1.018 4.382A3.323 3.323 0 0 1 .64 6.575v.045a3.288 3.288 0 0 0 2.632 3.218 3.203 3.203 0 0 1-.865.115 3.23 3.23 0 0 1-.614-.057 3.283 3.283 0 0 0 3.067 2.277A6.588 6.588 0 0 1 .78 13.58a6.32 6.32 0 0 1-.78-.045A9.344 9.344 0 0 0 5.026 15z"/>
                </svg>
            </a>
        </div>
    </footer>
</body>
</html>
