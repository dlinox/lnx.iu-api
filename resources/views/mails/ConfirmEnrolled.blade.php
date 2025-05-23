<!-- SendCredentials -->

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>
        Crear Cuenta
    </title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
            text-align: center;
        }

        .container {
            max-width: 500px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin: auto;
        }

        .header {
            font-size: 22px;
            font-weight: bold;
            color: #004080;
            margin-bottom: 20px;
        }

        .code {
            font-size: 28px;
            font-weight: bold;
            color: #040466;
            padding: 10px;
            border: 2px dashed #040466;
            display: inline-block;
            margin: 15px 0;
        }

        .footer {
            font-size: 12px;
            color: #666;
            margin-top: 20px;
        }

        .logo {
            width: 100px;
        }
    </style>
</head>

<body>
    <div class="container">
        <img class="logo" src="https://revistas.unap.edu.pe/portal/img/logo.png" alt="UNA Puno">

        <div class="header">
            Confirmación de matrícula
        </div>
        <p>
            Estimado(a) {{ $student->student }},<br>
            Su matrícula ha sido confirmada exitosamente. A continuación, encontrará mas detalles de su matrícula:
        </p>
        <div class="footer">
            Instituto de Informática - UNA Puno <br>
            © {{ date('Y') }} Universidad Nacional del Altiplano Puno

        </div>
</body>

</html>