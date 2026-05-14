<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Credenciales de acceso</title>
</head>

<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">

    <div style="max-width: 600px; margin: auto; background: #ffffff; padding: 20px; border-radius: 8px;">
        <h2 style="color: #00528e;">Credenciales de acceso al sistema</h2>

        <p>Hola {{ $name }},</p>

        <p>Se ha creado tu cuenta para acceder al Sistema de Soporte OTDTI.</p>

        <hr>

        <p><strong>Correo:</strong> {{ $email }}</p>
        <p><strong>Contraseña temporal:</strong> {{ $password }}</p>

        <hr>

        <p>Por seguridad, te recomendamos cambiar tu contraseña al ingresar al sistema.</p>

        <p style="margin-top: 30px;">
            <strong>Oficina de Transformación Digital y Tecnologías de la Información</strong><br>
            Municipalidad Distrital de Breña
        </p>
    </div>

</body>

</html>
