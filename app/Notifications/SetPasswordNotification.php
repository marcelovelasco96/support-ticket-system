<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class SetPasswordNotification extends ResetPassword implements ShouldQueue
{
    use Queueable;

    public function toMail($notifiable): MailMessage
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
            'type' => 'set',
        ], false));

        return (new MailMessage)
            ->subject('Establecer contraseña - Sistema de Soporte OTDTI')
            ->view('emails.reset-password', [
                'user' => $notifiable,
                'url' => $url,
                'isSet' => true,
            ]);
    }
}
