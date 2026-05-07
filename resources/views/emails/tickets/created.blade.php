<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Ticket registrado</title>
</head>

<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">

    <div style="max-width: 600px; margin: auto; background: #ffffff; padding: 20px; border-radius: 8px;">

        <h2 style="color: #00528e;">Ticket registrado correctamente</h2>

        <p>Hola,</p>

        <p>Se ha registrado tu ticket de soporte con el siguiente detalle:</p>

        <hr>

        <p><strong>Número de ticket:</strong> #{{ $ticket->id }}</p>
        <p><strong>Asunto:</strong> {{ $ticket->subject }}</p>
        <p><strong>Descripción:</strong> {{ $ticket->description }}</p>
        <p><strong>Estado:</strong> {{ \App\Models\Ticket::statusLabel($ticket->status) }}</p>

        <hr>

        <p>Lo resolveremos a la brevedad posible.</p>

        <p style="margin-top: 30px;">
            <strong>Equipo de soporte</strong><br>
            Organización Demo
        </p>

    </div>

</body>

</html>
