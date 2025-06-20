<?php
$error = '';
$success = '';
$showCodePopup = false;
$codigo = null;

require_once __DIR__ . '/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = new mysqli($host, $user, $pass, $db);
    if ($conn->connect_error) {
        $error = 'Error de conexión a la base de datos.';
    } else {

        $usuario = trim($_POST['username'] ?? '');
        $nombre = trim($_POST['fullname'] ?? '');
        $correo = trim($_POST['email'] ?? '');
        $numero = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirmPassword'] ?? '';
        $metodo = $_POST['verificationMethod'] ?? '';
        $imagen = null;

        $stmt = $conn->prepare("SELECT usuario, correo, numero FROM empleados WHERE usuario = ? OR correo = ? OR numero = ?");
        $stmt->bind_param('sss', $usuario, $correo, $numero);
        $stmt->execute();
        $stmt->bind_result($u, $c, $n);
        $existe_usuario = $existe_correo = $existe_numero = false;
        while ($stmt->fetch()) {
            file_put_contents('registro_debug.log', "DB usuario: $u, correo: $c, numero: $n\n", FILE_APPEND);
            if ($u === $usuario) $existe_usuario = true;
            if ($c === $correo) $existe_correo = true;
            if ($n === $numero) $existe_numero = true;
        }
        if ($existe_usuario) {
            $error = 'El usuario ya está registrado.';
        } elseif ($existe_correo) {
            $error = 'El correo ya está registrado.';
        } elseif ($existe_numero) {
            $error = 'El número ya está registrado.';
        } elseif ($password !== $confirmPassword) {
            $error = 'Las contraseñas no coinciden.';
        } else {
            if (empty($_FILES['imagen']) || !isset($_FILES['imagen']['tmp_name']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
                $error = 'La foto de perfil es obligatoria.';
            } else {
                $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
                $imgName = uniqid('img_') . '.' . $ext;
                $imgPath = __DIR__ . '/uploads/' . $imgName;
                if (!is_dir(__DIR__ . '/uploads')) mkdir(__DIR__ . '/uploads', 0777, true);
                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $imgPath)) {
                    $imagen = 'uploads/' . $imgName;
                } else {
                    $error = 'Error al subir la imagen de perfil.';
                }
            }

            $hash = password_hash($password, PASSWORD_DEFAULT);
            $codigo = rand(100000, 999999);
            $verificado = 0;
            $stmt2 = $conn->prepare("INSERT INTO empleados (usuario, nombre, correo, numero, password, imagen, codigo_confirmacion, verificado) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt2->bind_param('ssssssii', $usuario, $nombre, $correo, $numero, $hash, $imagen, $codigo, $verificado);
            if ($stmt2->execute()) {
                $success = '¡Registro exitoso! Revisa tu correo o WhatsApp para el código de verificación.';
                $showCodePopup = true;
                if ($metodo === 'email') {
                    $mail = new PHPMailer(true);
                    try {
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com';
                        $mail->SMTPAuth = true;
                        $mail->Username = '';
                        $mail->Password = '';
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port = 587;
                        $mail->CharSet = 'UTF-8';
                        $mail->setFrom('', 'Pepsi');
                        $mail->addAddress($correo);
                        $mail->Subject = 'Código de confirmación Pepsi';
                        $mail->Body = "Tu código de confirmación es: $codigo";
                        $mail->isHTML(false);
                        $mail->send();
                    } catch (Exception $e) {
                        $error = 'No se pudo enviar el correo: ' . $mail->ErrorInfo;
                    }
                } elseif ($metodo === 'whatsapp') {
                    $instance_id = 'instance124477';
                    $token = '';
                    $mensaje = "Tu código de confirmación Pepsi es: $codigo";
                    $numeroEnvio = preg_replace('/\D/', '', $numero);
                    if (strlen($numeroEnvio) == 10) {
                        $numeroEnvio = "52$numeroEnvio";
                    }
                    if (strpos($numeroEnvio, '+') !== 0) {
                        $numeroEnvio = '+' . $numeroEnvio;
                    }
                    $params = array(
                        'token' => $token,
                        'to' => $numeroEnvio,
                        'body' => $mensaje
                    );
                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => "https://api.ultramsg.com/$instance_id/messages/chat",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 30,
                        CURLOPT_SSL_VERIFYHOST => 0,
                        CURLOPT_SSL_VERIFYPEER => 0,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "POST",
                        CURLOPT_POSTFIELDS => http_build_query($params),
                        CURLOPT_HTTPHEADER => array(
                            "content-type: application/x-www-form-urlencoded"
                        ),
                    ));
                    $result = curl_exec($curl);
                    $err = curl_error($curl);
                    curl_close($curl);
                    $apiResp = json_decode($result, true);

                    if ($err) {
                        $error = 'cURL Error: ' . $err;
                    } elseif (!isset($apiResp['id'])) {
                        $errorMsg = isset($apiResp['error']) ? $apiResp['error'] : 'No se pudo enviar el código por WhatsApp.';
                        $error = $errorMsg;
                    }
                }
            } else {
                $error = 'Error al registrar. Intenta de nuevo.';
            }
            if (isset($stmt2) && $stmt2) $stmt2->close();
        }
        if (isset($stmt) && $stmt) $stmt->close();
        if (isset($conn) && $conn) $conn->close();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['codigo_confirmacion'])) {
    $codigoIngresado = trim($_POST['codigo_confirmacion']);
    $correo = trim($_POST['email'] ?? '');
    $numero = trim($_POST['phone'] ?? '');
    $usuario = trim($_POST['username'] ?? '');
    $tabla = 'empleados';

    $conn = new mysqli($host, $user, $pass, $db);
    if (!$conn->connect_error) {
        $where = [];
        $params = [];
        $types = '';
        if ($correo) {
            $where[] = 'correo=?';
            $params[] = $correo;
            $types .= 's';
        }
        if ($numero) {
            $where[] = 'numero=?';
            $params[] = $numero;
            $types .= 's';
        }
        if ($usuario) {
            $where[] = 'usuario=?';
            $params[] = $usuario;
            $types .= 's';
        }
        $where_sql = implode(' OR ', $where);
        $sql = "SELECT codigo_confirmacion FROM $tabla WHERE $where_sql ORDER BY id DESC LIMIT 1";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $stmt->bind_result($codigo_bd);
            if ($stmt->fetch()) {
                if ($codigo_bd && $codigo_bd == $codigoIngresado) {
                    $stmt->close();
                    $sql2 = "UPDATE $tabla SET verificado=1 WHERE $where_sql";
                    $stmt2 = $conn->prepare($sql2);
                    if ($stmt2) {
                        $stmt2->bind_param($types, ...$params);
                        $stmt2->execute();
                        $stmt2->close();
                    }

                    header('Location: login.php');
                    exit;
                } else {
                    $error = 'Código incorrecto.';
                }
            } else {
                $error = 'No se encontró el usuario para confirmar.';
            }
            $stmt->close();
        } else {
            $error = 'Error en la consulta de confirmación.';
        }
        $conn->close();
    } else {
        $error = 'Error de conexión a la base de datos.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nuevo'])) {
    header('Content-Type: application/json');
    $error = '';
    $success = '';
    $codigo = null;

    $conn = new mysqli($host, $user, $pass, $db);
    if ($conn->connect_error) {
        echo json_encode(['ok' => false, 'error' => 'Error de conexión a la base de datos.']);
        exit;
    }
    $usuario = trim($_POST['username'] ?? '');
    $nombre = trim($_POST['fullname'] ?? '');
    $correo = trim($_POST['email'] ?? '');
    $numero = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $metodo = $_POST['verificationMethod'] ?? '';
    $imagen = null;
¿
    $stmt = $conn->prepare("SELECT usuario, correo, numero FROM empleados WHERE usuario = ? OR correo = ? OR numero = ?");
    $stmt->bind_param('sss', $usuario, $correo, $numero);
    $stmt->execute();
    $stmt->bind_result($u, $c, $n);
    $existe_usuario = $existe_correo = $existe_numero = false;
    while ($stmt->fetch()) {
        file_put_contents('registro_debug.log', "DB usuario: $u, correo: $c, numero: $n\n", FILE_APPEND);
        if ($u === $usuario) $existe_usuario = true;
        if ($c === $correo) $existe_correo = true;
        if ($n === $numero) $existe_numero = true;
    }
    if ($existe_usuario) {
        echo json_encode(['ok' => false, 'error' => 'El usuario ya está registrado.']);
        $stmt->close();
        $conn->close();
        exit;
    } elseif ($existe_correo) {
        echo json_encode(['ok' => false, 'error' => 'El correo ya está registrado.']);
        $stmt->close();
        $conn->close();
        exit;
    } elseif ($existe_numero) {
        echo json_encode(['ok' => false, 'error' => 'El número ya está registrado.']);
        $stmt->close();
        $conn->close();
        exit;
    }

    if (empty($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
        $error = 'La foto de perfil es obligatoria.';
    } else {
        $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $imgName = uniqid('img_') . '.' . $ext;
        $imgPath = __DIR__ . '/uploads/' . $imgName;
        if (!is_dir(__DIR__ . '/uploads')) mkdir(__DIR__ . '/uploads', 0777, true);
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $imgPath)) {
            $imagen = 'uploads/' . $imgName;
        } else {
            $error = 'Error al subir la imagen de perfil.';
        }
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $codigo = rand(100000, 999999);
    $verificado = 0;
    $stmt2 = $conn->prepare("INSERT INTO empleados (usuario, nombre, correo, numero, password, imagen, codigo_confirmacion, verificado) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt2->bind_param('ssssssii', $usuario, $nombre, $correo, $numero, $hash, $imagen, $codigo, $verificado);
    if ($stmt2->execute()) {
        if ($metodo === 'email') {
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = '';
                $mail->Password = '';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                $mail->CharSet = 'UTF-8';
                $mail->setFrom('', 'Pepsi');
                $mail->addAddress($correo);
                $mail->Subject = 'Código de confirmación Pepsi';
                $mail->Body = "Tu código de confirmación es: $codigo";
                $mail->isHTML(false);
                $mail->send();
            } catch (Exception $e) {
                echo json_encode(['ok' => false, 'error' => 'No se pudo enviar el correo: ' . $mail->ErrorInfo]);
                $stmt2->close();
                $stmt->close();
                $conn->close();
                exit;
            }
        } elseif ($metodo === 'whatsapp') {
            $instance_id = 'instance124477';
            $token = 'h2nxn1htpbryg4of';
            $mensaje = "Tu código de confirmación Pepsi es: $codigo";
            $numeroEnvio = preg_replace('/\D/', '', $numero);
            if (strlen($numeroEnvio) == 10) {
                $numeroEnvio = "52$numeroEnvio";
            }
            if (strpos($numeroEnvio, '+') !== 0) {
                $numeroEnvio = '+' . $numeroEnvio;
            }
            $params = array(
                'token' => $token,
                'to' => $numeroEnvio,
                'body' => $mensaje
            );
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.ultramsg.com/$instance_id/messages/chat",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => http_build_query($params),
                CURLOPT_HTTPHEADER => array(
                    "content-type: application/x-www-form-urlencoded"
                ),
            ));
            $result = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            $apiResp = json_decode($result, true);

            if ($err) {
                echo json_encode(['ok' => false, 'error' => 'cURL Error: ' . $err]);
                $stmt2->close();
                $stmt->close();
                $conn->close();
                exit;
            } elseif (!isset($apiResp['id'])) {
                $errorMsg = isset($apiResp['error']) ? $apiResp['error'] : 'No se pudo enviar el código por WhatsApp.';
                echo json_encode(['ok' => false, 'error' => $errorMsg]);
                $stmt2->close();
                $stmt->close();
                $conn->close();
                exit;
            }
        }
        echo json_encode(['ok' => true]);
    } else {
        echo json_encode(['ok' => false, 'error' => 'Error al registrar. Intenta de nuevo.']);
    }
    $stmt2->close();
    $stmt->close();
    $conn->close();
    exit;
}
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
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
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
        .register-button {
            background: #004B93;
            transition: all 0.3s ease;
        }
        .register-button:hover {
            background: #003366;
            transform: translateY(-2px);
        }
        .image-upload {
            border: 2px dashed rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }
        .image-upload:hover {
            border-color: #004B93;
        }

        .verification-method {
            border-color: rgba(255, 255, 255, 0.2);
            position: relative;
        }

        .verification-method::after {
            content: '';
            position: absolute;
            inset: -2px;
            border-radius: inherit;
            border: 2px solid transparent;
            transition: border-color 0.3s ease;
        }

        .verification-method input:checked + div {
            color: #004B93;
        }

        .verification-method input:checked + div i {
            color: #004B93;
        }

        .verification-method input:checked::after {
            border-color: #004B93;
        }

        .verification-method:has(input:checked) {
            background: rgba(0, 75, 147, 0.1);
        }

        body.light-mode .verification-method {
            border-color: rgba(0, 0, 0, 0.2);
        }

        body.light-mode .verification-method:has(input:checked) {
            background: rgba(0, 75, 147, 0.05);
        }

        body.light-mode .bg-black\/20 {
            background: rgba(0, 0, 0, 0.05);
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

        body.light-mode footer a.text-white {
            color: #fff !important;
        }
    </style>
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
</head>
<body class="bg-black text-white font-sans min-h-screen flex flex-col items-center justify-center py-12">

    <nav class="w-full bg-black py-4 px-8 flex items-center justify-between shadow-md fixed top-0 left-0 z-40">

        <div class="flex items-center flex-shrink-0">
            <a href="index.php" class="text-2xl font-bold text-pepsiBlue tracking-wider">PEPSI</a>
        </div>

        <div class="flex-1 flex items-center justify-center space-x-8">
            <a href="index.php" class="text-white hover:text-pepsiBlue transition-colors">Inicio</a>
            <a href="products.php" class="text-white hover:text-pepsiBlue transition-colors">Productos</a>
            <a href="contact.php" class="text-white hover:text-pepsiBlue transition-colors">Contacto</a>
            <a href="faq.php" class="text-white hover:text-pepsiBlue transition-colors">FAQ</a>
            <a href="privacy.php" class="text-white hover:text-pepsiBlue transition-colors">Privacidad</a>
        </div>

        <div class="flex items-center space-x-4">
            <button id="darkModeToggle" class="text-white hover:text-pepsiBlue transition-colors" aria-label="Cambiar modo oscuro/claro">
                <i class="fas fa-moon text-xl"></i>
            </button>
        </div>
    </nav>
    <nav aria-label="breadcrumb" class="w-full flex justify-center pt-24 pb-2">
        <ol class="flex items-center space-x-2 text-lg font-semibold breadcrumb-list" id="breadcrumb-list">
        </ol>
    </nav>
    <script>

        (function() {
            const breadcrumbList = document.getElementById('breadcrumb-list');

            let prev = document.referrer;
            let prevLabel = '';
            let prevHref = '';

            if (prev) {
                try {
                    const url = new URL(prev);

                    if (url.hostname === window.location.hostname) {
                        prevHref = url.pathname.split('/').pop();

                        if (prevHref === 'index.php' || prevHref === '') prevLabel = 'Inicio';
                        else if (prevHref === 'products.php') prevLabel = 'Productos';
                        else if (prevHref === 'contact.php') prevLabel = 'Contacto';
                        else if (prevHref === 'faq.php') prevLabel = 'FAQ';
                        else if (prevHref === 'privacy.php') prevLabel = 'Privacidad';
                        else prevLabel = prevHref.replace('.php', '').replace(/^\w/, c => c.toUpperCase());
                        prevHref = url.pathname;
                    }
                } catch(e) {}
            }

            let html = '';
            if (prevLabel && prevHref) {
                html += `<li><a href="${prevHref}" class="hover:underline text-pepsiBlue">${prevLabel}</a></li>`;
                html += `<li class="text-white">/</li>`;
            }
            html += `<li class="text-white" aria-current="page">Registro</li>`;
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
    <div class="w-full flex flex-col items-center justify-center mt-8 mb-4">
        <h1 class="text-4xl font-bold text-white tracking-wider">PEPSI</h1>
        <p class="text-gray-400 mt-2">Crear Cuenta</p>
    </div>

    <div id="popupError" class="fixed inset-0 flex items-center justify-center z-50 hidden">
        <div class="bg-white text-red-600 rounded-lg shadow-lg p-6 flex flex-col items-center relative max-w-xs w-full">
            <button onclick="document.getElementById('popupError').style.display='none'" class="absolute top-2 right-2 text-xl text-red-400 hover:text-red-600">&times;</button>
            <i class="fas fa-times-circle text-4xl mb-2"></i>
            <div class="font-bold text-lg mb-2">Error</div>
            <div class="text-center" id="popupErrorMsg"></div>
        </div>
    </div>
    <div id="popupSuccess" class="fixed inset-0 flex items-center justify-center z-50 hidden">
        <div class="bg-white text-green-600 rounded-lg shadow-lg p-6 flex flex-col items-center relative max-w-xs w-full">
            <button onclick="document.getElementById('popupSuccess').style.display='none'" class="absolute top-2 right-2 text-xl text-green-400 hover:text-green-600">&times;</button>
            <i class="fas fa-check-circle text-4xl mb-2"></i>
            <div class="font-bold text-lg mb-2">¡Registro exitoso!</div>
            <div class="text-center" id="popupSuccessMsg"></div>
        </div>
    </div>

    <div id="popupCode" class="fixed inset-0 flex items-center justify-center z-50" style="display:none;">
        <div class="bg-white text-black rounded-lg shadow-lg p-6 flex flex-col items-center relative max-w-xs w-full">
            <button onclick="document.getElementById('popupCode').style.display='none'" class="absolute top-2 right-2 text-xl text-gray-400 hover:text-red-600">&times;</button>
            <i class="fas fa-key text-4xl mb-2 text-pepsiBlue"></i>
            <div class="font-bold text-lg mb-2">Código de Confirmación</div>
            <div class="text-center mb-2">Ingresa el código de 6 dígitos que te enviamos.</div>
            <form id="codeForm" method="post" autocomplete="off">
                <input type="hidden" name="email" value="">
                <input type="hidden" name="phone" value="">
                <input type="hidden" name="username" value="">
                <input type="text" name="codigo_confirmacion" maxlength="6" pattern="[0-9]{6}" class="border rounded px-4 py-2 text-center text-lg mb-2 w-full" placeholder="######" required>
                <button type="submit" class="bg-pepsiBlue text-white px-4 py-2 rounded w-full font-bold">Confirmar</button>
            </form>
            <div id="codeError" class="text-red-600 mt-2 hidden"></div>
            <div id="codeSuccess" class="text-green-600 mt-2 hidden"></div>
        </div>
    </div>

    <div class="w-full max-w-2xl p-8 flex flex-col items-center justify-center">

        <form class="space-y-6 w-full" id="registerForm" method="post" enctype="multipart/form-data" autocomplete="off">

            <div class="text-center space-y-2">
                <label for="profileImage" class="block text-sm font-medium text-gray-300">Foto de perfil</label>
                <div class="image-upload mx-auto w-32 h-32 rounded-full flex items-center justify-center cursor-pointer relative overflow-hidden group" id="imageUploadContainer">
                    <input type="file" accept="image/*" id="profileImage" name="imagen" class="hidden" required>
                    <img id="imagePreview" class="absolute inset-0 w-full h-full object-cover hidden">
                    <div id="uploadPlaceholder" class="text-center z-10">
                        <i class="fas fa-camera text-2xl mb-2"></i>
                        <p class="text-sm">Subir foto</p>
                    </div>
                    <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                        <p class="text-white text-sm">Cambiar foto</p>
                    </div>
                </div>
                <p id="imageError" class="text-red-500 text-sm hidden">La foto de perfil es obligatoria.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div>
                    <label for="username" class="block text-sm font-medium text-gray-300 mb-2">Usuario</label>
                    <input type="text" id="username" name="username" required 
                        class="form-input w-full px-4 py-3 rounded-lg text-white placeholder-gray-400"
                        placeholder="usuario123"
                        autocomplete="username">
                </div>

                <div>
                    <label for="fullname" class="block text-sm font-medium text-gray-300 mb-2">Nombre Completo</label>
                    <input type="text" id="fullname" name="fullname" required 
                        class="form-input w-full px-4 py-3 rounded-lg text-white placeholder-gray-400"
                        placeholder="Juan Pérez"
                        autocomplete="name">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-300 mb-2">Correo electrónico</label>
                    <input type="email" id="email" name="email" required 
                        class="form-input w-full px-4 py-3 rounded-lg text-white placeholder-gray-400"
                        placeholder="tu@email.com"
                        autocomplete="email">
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-300 mb-2">WhatsApp</label>
                    <input type="tel" id="phone" name="phone" required 
                        class="form-input w-full px-4 py-3 rounded-lg text-white placeholder-gray-400"
                        placeholder="Ej: 8445404397"
                        autocomplete="tel"
                        pattern="[0-9]{10}"
                        title="Debe ser un número de 10 dígitos">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-300 mb-2">Contraseña</label>
                    <input type="password" id="password" name="password" required 
                        class="form-input w-full px-4 py-3 rounded-lg text-white placeholder-gray-400"
                        placeholder="••••••••"
                        autocomplete="new-password"
                        minlength="8"
                        pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                        title="Debe contener al menos 8 caracteres, incluyendo mayúsculas, minúsculas y números">
                </div>

                <div>
                    <label for="confirmPassword" class="block text-sm font-medium text-gray-300 mb-2">Confirmar Contraseña</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" required 
                        class="form-input w-full px-4 py-3 rounded-lg text-white placeholder-gray-400"
                        placeholder="••••••••"
                        autocomplete="new-password"
                        minlength="8">
                </div>
            </div>

            <div class="space-y-3">
                <label class="block text-sm font-medium text-gray-300">Método de verificación</label>
                <div class="grid grid-cols-2 gap-4">
                    <label class="verification-method flex items-center justify-center p-4 border rounded-lg cursor-pointer transition-all duration-300 hover:border-pepsiBlue group">
                        <input type="radio" name="verificationMethod" value="whatsapp" class="hidden" required>
                        <div class="text-center">
                            <i class="fab fa-whatsapp text-2xl mb-2 text-gray-400 group-hover:text-pepsiBlue transition-colors"></i>
                            <p class="text-sm">WhatsApp</p>
                        </div>
                    </label>
                    <label class="verification-method flex items-center justify-center p-4 border rounded-lg cursor-pointer transition-all duration-300 hover:border-pepsiBlue group">
                        <input type="radio" name="verificationMethod" value="email" class="hidden">
                        <div class="text-center">
                            <i class="fas fa-envelope text-2xl mb-2 text-gray-400 group-hover:text-pepsiBlue transition-colors"></i>
                            <p class="text-sm">Correo electrónico</p>
                        </div>
                    </label>
                </div>
            </div>

            <div class="flex justify-center">
                <div class="g-recaptcha" data-sitekey=""></div>
            </div>

            <div class="space-y-6">
                <div class="bg-black/20 p-4 rounded-lg">
                    <p class="text-sm text-gray-300 font-medium mb-2">La contraseña debe contener:</p>
                    <ul class="text-sm text-gray-400 list-disc pl-5 space-y-1">
                        <li>Al menos 8 caracteres</li>
                        <li>Al menos una letra mayúscula</li>
                        <li>Al menos una letra minúscula</li>
                        <li>Al menos un número</li>
                    </ul>
                </div>

                <button type="submit" class="register-button w-full py-4 px-6 rounded-lg text-white font-medium shadow-lg text-lg">
                    Crear Cuenta
                </button>
            </div>

            <div class="text-center mt-4">
                <p class="text-gray-400">¿Ya tienes una cuenta?
                    <a href="login.php" class="text-pepsiBlue hover:underline ml-1">Iniciar Sesión</a>
                </p>
            </div>
        </form>

        <div class="text-center mt-8">
            <a href="index.php" class="text-gray-400 hover:text-white transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Volver al inicio
            </a>
        </div>
    </div>

    <script>

        const isDarkMode = localStorage.getItem('darkMode') !== null 
            ? localStorage.getItem('darkMode') === 'true' 
            : true;

        function updateTheme() {
            const logo = document.querySelector('h1');
            const inputs = document.querySelectorAll('.form-input');
            const labels = document.querySelectorAll('.text-gray-300, .text-gray-400');
            const imageUpload = document.querySelector('.image-upload');

            document.body.classList.toggle('light-mode', !isDarkMode);
            
            if (!isDarkMode) {
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
                imageUpload.style.borderColor = 'rgba(0, 0, 0, 0.2)';
            
                document.querySelectorAll('.verification-method i').forEach(icon => {
                    icon.classList.remove('text-gray-400');
                    icon.classList.add('text-gray-600');
                });
            } else {
                document.querySelectorAll('.verification-method i').forEach(icon => {
                    icon.classList.remove('text-gray-600');
                    icon.classList.add('text-gray-400');
                });
            }
        }

        updateTheme();

        window.addEventListener('storage', (e) => {
            if (e.key === 'darkMode') {
                location.reload();
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const profileImage = document.getElementById('profileImage');
            const imagePreview = document.getElementById('imagePreview');
            const uploadPlaceholder = document.getElementById('uploadPlaceholder');
            const imageUploadContainer = document.getElementById('imageUploadContainer');
            if (profileImage) {
                profileImage.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            imagePreview.src = e.target.result;
                            imagePreview.classList.remove('hidden');
                            uploadPlaceholder.classList.add('hidden');
                        }
                        reader.readAsDataURL(file);
                    }
                });
            }

            if (imageUploadContainer && profileImage) {
                imageUploadContainer.addEventListener('click', function(e) {

                    if (e.target !== profileImage) {
                        profileImage.click();
                    }
                });

                if (uploadPlaceholder) {
                    uploadPlaceholder.addEventListener('click', function(e) {
                        e.stopPropagation();
                        profileImage.click();
                    });
                }
            }
        });
        const form = document.getElementById('registerForm');
        const imageError = document.getElementById('imageError');
        const profileImage = document.getElementById('profileImage');


        form.addEventListener('submit', function(e) {
            imageError.classList.add('hidden');
            document.getElementById('imageUploadContainer').classList.remove('border-red-500');
            let hasErrors = false;

            if (!profileImage.files.length) {
                e.preventDefault();
                imageError.classList.remove('hidden');
                imageError.innerText = 'La foto de perfil es obligatoria.';
                var popupErrorMsg = document.getElementById('popupErrorMsg');
                var popupError = document.getElementById('popupError');
                if (popupErrorMsg && popupError) {
                    popupErrorMsg.innerText = 'La foto de perfil es obligatoria.';
                    popupError.style.display = 'flex';
                }
                document.getElementById('imageUploadContainer').classList.add('border-red-500');
                document.getElementById('imageUploadContainer').scrollIntoView({behavior: 'smooth', block: 'center'});
                return;
            }

            if (usernameInput.classList.contains('border-red-500')) {
                e.preventDefault();
                var popupErrorMsg = document.getElementById('popupErrorMsg');
                var popupError = document.getElementById('popupError');
                if (popupErrorMsg && popupError) {
                    popupErrorMsg.innerText = 'El usuario ya está registrado. Elige otro.';
                    popupError.style.display = 'flex';
                }
                usernameInput.focus();
                return;
            }
            if (emailInput.classList.contains('border-red-500')) {
                e.preventDefault();
                var popupErrorMsg = document.getElementById('popupErrorMsg');
                var popupError = document.getElementById('popupError');
                if (popupErrorMsg && popupError) {
                    popupErrorMsg.innerText = 'El correo ya está en uso. Usa otro correo.';
                    popupError.style.display = 'flex';
                }
                emailInput.focus();
                return;
            }
            if (phoneInput.classList.contains('border-red-500')) {
                e.preventDefault();
                var popupErrorMsg = document.getElementById('popupErrorMsg');
                var popupError = document.getElementById('popupError');
                if (popupErrorMsg && popupError) {
                    popupErrorMsg.innerText = 'El número ya está registrado. Usa otro número.';
                    popupError.style.display = 'flex';
                }
                phoneInput.focus();
                return;
            }
            if (!usernameInput.value.trim() || !emailInput.value.trim() || !phoneInput.value.trim()) {
                e.preventDefault();
                return;
            }

            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('confirmPassword');
            if (passwordInput.value !== confirmPasswordInput.value) {
                confirmPasswordInput.classList.add('border-red-500');
                hasErrors = true;
            } else {
                confirmPasswordInput.classList.remove('border-red-500');
            }

            const metodo = document.querySelector('input[name="verificationMethod"]:checked');
            if (!metodo) {
                alert('Selecciona un método de verificación');
                hasErrors = true;
            }
            const captcha = document.querySelector('.g-recaptcha-response')?.value;
            if (!captcha) {
                alert('Completa el captcha');
                hasErrors = true;
            }

            const usernameError = document.getElementById('usernameError')?.innerText;
            const emailError = document.getElementById('emailError')?.innerText;
            const phoneError = document.getElementById('phoneError')?.innerText;
            if ((usernameError && usernameError.length > 0) ||
                (emailError && emailError.length > 0) ||
                (phoneError && phoneError.length > 0)) {
                hasErrors = true;
            }

            if (hasErrors) return;

            const formData = new FormData();
            formData.append('nuevo', '1');
            formData.append('username', form.username.value);
            formData.append('fullname', form.fullname.value);
            formData.append('email', form.email.value);
            formData.append('password', form.password.value);
            formData.append('confirmPassword', form.confirmPassword.value);
            formData.append('phone', form.phone.value);
            formData.append('verificationMethod', metodo.value);
            formData.append('captcha', captcha);
            if (profileImage.files.length) {
                formData.append('imagen', profileImage.files[0]);
            }

            formData.append('g-recaptcha-response', captcha);

            fetch('empleados.php', {
                method: 'POST',
                body: formData
            })
            .then(async r => {
                let respText = await r.text();
                let resp;
                try {
                    resp = JSON.parse(respText);
                } catch (e) {
                    document.getElementById('popupErrorMsg').innerText = 'Respuesta inesperada del servidor:\n' + respText;
                    document.getElementById('popupError').style.display = 'flex';
                    return;
                }
                if (resp.ok) {
                    document.getElementById('popupCode').style.display = 'flex';
                    document.querySelector('#codeForm input[name="email"]').value = form.email.value;
                    document.querySelector('#codeForm input[name="phone"]').value = form.phone.value;
                    document.querySelector('#codeForm input[name="username"]').value = form.username.value;
                    document.getElementById('codeError').classList.add('hidden');
                    document.getElementById('codeError').innerText = '';
                    document.getElementById('codeSuccess').classList.add('hidden');
                    document.getElementById('codeSuccess').innerText = '';
                    window._correo_registro = form.email.value;
                    window._num_registro = form.phone.value;
                    window._user_registro = form.username.value;
                    window._metodo_registro = metodo.value;
                } else {

                    if (resp.error &&
                        resp.error.match(/usuario ya est[áa] registrado|correo ya est[áa] registrado|n[uú]mero ya est[áa] registrado/i)) {

                    } else {
                        document.getElementById('popupErrorMsg').innerText = resp.error || 'Error desconocido';
                        document.getElementById('popupError').style.display = 'flex';
                    }
                }
            })
            .catch(() => {
                document.getElementById('popupErrorMsg').innerText = 'Error de conexión con el servidor';
                document.getElementById('popupError').style.display = 'flex';
            });
        });

        const codeForm = document.getElementById('codeForm');
        if (codeForm) {
            codeForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const code = codeForm.querySelector('input[name="codigo_confirmacion"]').value;
                fetch('empleados.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({
                        accion: 'confirmar',
                        codigo: code,
                        correo: window._correo_registro,
                        num: window._num_registro,
                        user: window._user_registro,
                        metodo: window._metodo_registro
                    })
                })
                .then(r => r.json())
                .then(resp => {
                    if (resp.ok) {
                        document.getElementById('codeError').classList.add('hidden');
                        document.getElementById('codeSuccess').classList.remove('hidden');
                        document.getElementById('codeSuccess').innerText = '¡Cuenta confirmada!';

                        setTimeout(() => {
                            window.location.href = 'login.php';
                        }, 2000);
                    } else {
                        document.getElementById('codeError').classList.remove('hidden');
                        document.getElementById('codeError').innerText = resp.error || 'Código incorrecto';
                        document.getElementById('codeSuccess').classList.add('hidden');
                        document.getElementById('codeSuccess').innerText = '';
                    }
                })
                .catch(() => {
                    document.getElementById('codeError').innerText = 'Error de conexión';
                    document.getElementById('codeError').classList.remove('hidden');
                });
            });
        }

        const darkModeToggle = document.getElementById('darkModeToggle');
        const moonIcon = darkModeToggle.querySelector('.fa-moon, .fa-sun');
        let _isDarkMode = localStorage.getItem('darkMode') !== null 
            ? localStorage.getItem('darkMode') === 'true' 
            : true;

        function updateDarkModeIcon() {
            if (_isDarkMode) {
                moonIcon.classList.add('fa-moon');
                moonIcon.classList.remove('fa-sun');
            } else {
                moonIcon.classList.remove('fa-moon');
                moonIcon.classList.add('fa-sun');
            }
        }
        updateDarkModeIcon();

        darkModeToggle.addEventListener('click', () => {
            _isDarkMode = !_isDarkMode;
            localStorage.setItem('darkMode', _isDarkMode);
            updateDarkModeIcon();
            location.reload();
        });+
        function checkFieldExists(field, value, callback) {
            if (!value) {
                callback(false, '');
                return;
            }
            fetch('empleados.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    accion: 'existe',
                    campo: field,
                    valor: value
                })
            })
            .then(r => r.json())
            .then(resp => {
                callback(resp.exists, resp.error || '');
            })
            .catch(() => {
                callback(false, 'Error de conexión');
            });
        }

        function showFieldError(fieldId, msg) {
            let el = document.getElementById(fieldId + 'Error');
            if (!el) {
                el = document.createElement('div');
                el.id = fieldId + 'Error';
                el.className = 'text-red-500 text-sm mt-1';
                const input = document.getElementById(fieldId);
                input.parentNode.appendChild(el);
            }
            el.innerText = msg;
            el.style.display = msg ? 'block' : 'none';
        }

        function updateRegisterButtonState() {
            const btn = form.querySelector('button[type="submit"]');
            const errors = [
                document.getElementById('usernameError')?.innerText,
                document.getElementById('emailError')?.innerText,
                document.getElementById('phoneError')?.innerText
            ];

            const hasImage = profileImage.files.length > 0;
            btn.disabled = errors.some(msg => msg && msg.length > 0) || !hasImage;
            btn.classList.toggle('opacity-50', btn.disabled);
            btn.classList.toggle('cursor-not-allowed', btn.disabled);
        }

        profileImage.addEventListener('change', function() {
            imageError.classList.add('hidden');
            document.getElementById('imageUploadContainer').classList.remove('border-red-500');
            updateRegisterButtonState();
        });
        updateRegisterButtonState();

        const usernameInput = document.getElementById('username');
        usernameInput.addEventListener('blur', function() {
            const value = this.value.trim();
            checkFieldExists('usuario', value, function(exists, msg) {
                if (exists) {
                    showFieldError('username', 'El usuario ya está registrado. Elige otro.');
                    usernameInput.classList.add('border-red-500');
                    usernameInput.setCustomValidity('El usuario ya está registrado. Elige otro.');
                } else {
                    showFieldError('username', '');
                    usernameInput.classList.remove('border-red-500');
                    usernameInput.setCustomValidity('');
                }
            });
        });

        const emailInput = document.getElementById('email');
        emailInput.addEventListener('blur', function() {
            const value = this.value.trim();
            checkFieldExists('correo', value, function(exists, msg) {
                if (exists) {
                    showFieldError('email', 'El correo ya está en uso. Usa otro correo.');
                    emailInput.classList.add('border-red-500');
                    emailInput.setCustomValidity('El correo ya está en uso. Usa otro correo.');
                } else {
                    showFieldError('email', '');
                    emailInput.classList.remove('border-red-500');
                    emailInput.setCustomValidity('');
                }
            });
        });

        const phoneInput = document.getElementById('phone');
        phoneInput.addEventListener('blur', function() {
            const value = this.value.trim();
            checkFieldExists('numero', value, function(exists, msg) {
                if (exists) {
                    showFieldError('phone', 'El número ya está registrado. Usa otro número.');
                    phoneInput.classList.add('border-red-500');
                    phoneInput.setCustomValidity('El número ya está registrado. Usa otro número.');
                } else {
                    showFieldError('phone', '');
                    phoneInput.classList.remove('border-red-500');
                    phoneInput.setCustomValidity('');
                }
            });
        });

        form.addEventListener('submit', function(e) {
            if (usernameInput.classList.contains('border-red-500')) {
                var popupErrorMsg = document.getElementById('popupErrorMsg');
                var popupError = document.getElementById('popupError');
                if (popupErrorMsg && popupError) {
                    popupErrorMsg.innerText = 'El usuario ya está registrado. Elige otro.';
                    popupError.style.display = 'flex';
                }
                e.preventDefault();
                return;
            }
            if (emailInput.classList.contains('border-red-500')) {
                var popupErrorMsg = document.getElementById('popupErrorMsg');
                var popupError = document.getElementById('popupError');
                if (popupErrorMsg && popupError) {
                    popupErrorMsg.innerText = 'El correo ya está en uso. Usa otro correo.';
                    popupError.style.display = 'flex';
                }
                e.preventDefault();
                return;
            }
            if (phoneInput.classList.contains('border-red-500')) {
                var popupErrorMsg = document.getElementById('popupErrorMsg');
                var popupError = document.getElementById('popupError');
                if (popupErrorMsg && popupError) {
                    popupErrorMsg.innerText = 'El número ya está registrado. Usa otro número.';
                    popupError.style.display = 'flex';
                }
                e.preventDefault();
                return;
            }

        });
    </script>

    <button class="help-bubble-btn" id="helpBubbleBtn" title="Ayuda"
        style="position: fixed; left: 32px; bottom: 32px; z-index: 9999; background: #004B93; color: #fff; border: none; border-radius: 50%; width: 60px; height: 60px; box-shadow: 0 4px 16px rgba(0,0,0,0.18); display: flex; align-items: center; justify-content: center; font-size: 2rem; cursor: pointer; transition: background 0.2s;">
        <i class="fas fa-question"></i>
    </button>
    <div class="help-bubble-popup" id="helpBubblePopup"
        style="position: fixed; left: 110px; bottom: 50px; z-index: 10000; background: #fff; color: #222; border-radius: 16px; box-shadow: 0 8px 32px rgba(0,0,0,0.18); padding: 1.5rem 2rem 1.5rem 1.5rem; min-width: 260px; max-width: 320px; display: none; flex-direction: column; gap: 1rem; font-size: 1rem; border-left: 6px solid #004B93;">
        <button class="close-help-bubble" id="closeHelpBubble" title="Cerrar" style="position: absolute; top: 10px; right: 18px; background: none; border: none; color: #888; font-size: 1.3rem; cursor: pointer;">&times;</button>
        <h4 style="margin:0 0 0.5rem 0;font-size:1.15rem;color:#004B93;font-weight:bold;">¿Necesitas ayuda?</h4>
        <ul style="margin:0 0 0.5rem 0;padding-left:1.2em;">
            <li style="margin-bottom:0.4em;font-size:0.98em;">¿Tienes dudas sobre el registro? <a href="faq.php">Consulta las preguntas frecuentes</a>.</li>
            <li style="margin-bottom:0.4em;font-size:0.98em;">¿Problemas técnicos? <a href="mailto:contacto@pepsi.com">Escríbenos por email</a>.</li>
            <li style="margin-bottom:0.4em;font-size:0.98em;">¿Prefieres WhatsApp? <a href="https://wa.me/5218445404397" target="_blank">Contáctanos aquí</a>.</li>
        </ul>
        <div style="font-size:0.95em;color:#666;">O utiliza el chat flotante para hablar con nuestro bot.</div>
    </div>

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

    <footer class="bg-black text-white py-8 mt-12">
        <div class="container mx-auto px-6">
            <div class="flex flex-col items-center justify-between space-y-8">
                <div class="text-4xl font-bold blink-pepsi">PEPSI</div>
                <div class="flex flex-wrap justify-center gap-8 text-sm uppercase tracking-wider">
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

<?php if (!empty($error)): ?>
<script>

    window.addEventListener('DOMContentLoaded', function() {
        var popupErrorMsg = document.getElementById('popupErrorMsg');
        var popupError = document.getElementById('popupError');
        var imgCont = document.getElementById('imageUploadContainer');
        var imgErr = document.getElementById('imageError');
        var errorMsg = <?php echo json_encode($error); ?>;
        if (popupErrorMsg && popupError) {
            popupErrorMsg.innerText = errorMsg;
            popupError.style.display = 'flex';
        }
        if (errorMsg.toLowerCase().includes('foto de perfil')) {
            if (imgCont) imgCont.classList.add('border-red-500');
            if (imgErr) imgErr.classList.remove('hidden');

            if (imgCont && typeof imgCont.scrollIntoView === 'function') {
                imgCont.scrollIntoView({behavior: 'smooth', block: 'center'});
            }
        }
    });
</script>
<?php endif; ?>
