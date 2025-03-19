<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificación - EventReservas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background-color: #4CAF50;
            color: #ffffff;
            text-align: center;
            padding: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .content {
            padding: 20px;
            color: #333333;
            line-height: 1.6;
        }

        .content p {
            margin: 0 0 10px;
        }

        .footer {
            background-color: #f4f4f9;
            text-align: center;
            padding: 10px;
            font-size: 12px;
            color: #888888;
        }

        .footer a {
            color: #4CAF50;
            text-decoration: none;
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
            <p><strong>{{ $mensaje }}</strong></p>
        </div>
        <div class="footer">
            <p>Gracias por usar EventReservas.</p>
            <p><a href="{{ url('/') }}">Visita nuestra página</a></p>
        </div>
    </div>
</body>

</html>
