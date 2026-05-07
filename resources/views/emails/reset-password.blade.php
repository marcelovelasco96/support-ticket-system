<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
</head>

<body style="font-family: Arial, sans-serif; background-color:#f4f4f4; padding:20px;">

    <div style="max-width:600px; margin:auto; background:white; padding:20px; border-radius:8px;">

        <!-- Título -->

        <h1 style="color: #00528e; text-align:center;">
            Organización Demo
        </h1>

        <h2 style="color: gray; text-align:center;">
            Sistema de Soporte Interno
        </h2>

        <p>Hola {{ $user->name }},</p>

        <p>
            {{ $isSet
                ? 'Se ha creado una cuenta para ti en el Sistema de Soporte Interno.'
                : 'Has solicitado restablecer tu contraseña de acceso al Sistema de Soporte Interno.' }}
        </p>

        <p>
            Haz clic en el siguiente botón <b> desde una computadora autorizada</b>:
        </p>

        <!-- Botón -->
        <div style="text-align:center; margin:30px 0;">
            <a href="{{ $url }}"
                style="background:#00528e; color:white; padding:12px 20px; text-decoration:none; border-radius:6px; border:2px solid #fbbf00; display:inline-block; font-weight:bold;">
                {{ $isSet ? 'Establecer contraseña' : 'Restablecer contraseña' }}
            </a>
        </div>

        <!-- Link alternativo -->
        <p style="font-size:12px; color:#666;">
            Si el botón no funciona, copia y pega este enlace en tu navegador:
        </p>

        <p style="font-size:12px; word-break:break-all;">
            {{ $url }}
        </p>

        <hr>

        <p style="font-size:12px; color:#999; text-align:center;">
            Equipo de soporte<br>
            Organización Demo
        </p>

    </div>

</body>

</html>
