<?php
// Enviar código de estado HTTP 404
http_response_code(404);

// Obtener URL base dinámica
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$base_url = $protocol . "://" . $host . "/";

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error 404 - Página no encontrada</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: #eeeeee;
            color: #333333;
            font-family: Arial, Helvetica, sans-serif;
        }

        .container {
            width: min(760px, 100%);
            text-align: center;
        }

        .error-image {
            display: block;
            width: min(100%, 560px);
            max-height: 340px;
            object-fit: contain;
            margin: 0 auto;
        }

        h1 {
            margin: 8px 0 12px;
            color: #f26732;
            font-size: clamp(2.5rem, 8vw, 5rem);
            line-height: 1;
        }

        p {
            margin: 0 0 26px;
            color: #555555;
            font-size: 1.1rem;
        }

        .btn-back {
            border: 0;
            border-radius: 4px;
            background: #f26732;
            color: #ffffff;
            padding: 13px 28px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .btn-back:hover {
            background: #d94f20;
        }

        .btn-back:focus-visible {
            outline: 3px solid #333333;
            outline-offset: 3px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>¡Ups!</h1>
        <img
            src="<?= htmlspecialchars($base_url, ENT_QUOTES, 'UTF-8') ?>img/error.jpg"
            alt="Error 404"
            class="error-image">
        <p>La página que buscas no existe o fue movida.</p>
        <button type="button" class="btn-back" onclick="window.history.back();">Volver</button>
    </div>
</body>
</html>