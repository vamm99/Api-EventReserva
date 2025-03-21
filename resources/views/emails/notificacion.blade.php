<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificación - EventReservas</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            background-color: #1a202c;
            color: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 500px;
            width: 100%;
            background: linear-gradient(to right, #2563eb, #7e22ce);
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(to right, #1e40af, #6b21a8);
            padding: 16px;
            text-align: center;
        }

        .header h1 {
            font-size: 22px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
        }

        .content {
            padding: 24px;
            color: #e2e8f0;
        }

        .content p {
            margin: 10px 0;
            font-size: 16px;
        }

        .message-box {
            background-color: #2d3748;
            padding: 16px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            font-size: 18px;
            font-weight: 600;
            color: #ffffff;
            text-align: center;
        }

        .footer {
            background-color: #2d3748;
            padding: 16px;
            text-align: center;
            font-size: 14px;
            color: #a0aec0;
        }

        .footer a {
            color: #63b3ed;
            text-decoration: none;
        }

        .footer a:hover {
            color: #90cdf4;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>EventReservas</h1>
        </div>
        <div class="content">
            <p>Hola,</p>
            <p>Tienes una nueva notificación:</p>
            <p class="message-box">{{ $mensaje }}</p>
        </div>
        <div class="footer">
            <p>Gracias por usar EventReservas.</p>
            <p><a href="{{ url('/') }}">Visita nuestra página</a></p>
        </div>
    </div>
</body>

</html>
