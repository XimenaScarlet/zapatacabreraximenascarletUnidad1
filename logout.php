<?php
session_start();
session_unset();
session_destroy();
// Evita caché para que no se pueda volver atrás
header('Expires: Tue, 01 Jan 2000 00:00:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Location: index.php');
exit;
?>
<!-- Si el navegador ignora headers, fuerza recarga y limpia historial -->
<script>
    window.location.replace('index.php');
    if (window.history && window.history.pushState) {
        window.history.pushState(null, '', window.location.href);
        window.onpopstate = function () {
            window.location.replace('index.php');
        };
    }
</script>
