<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificacionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $mensaje;

    /**
     * Crear una nueva instancia del mensaje.
     */
    public function __construct($mensaje)
    {
        $this->mensaje = $mensaje;
    }

    /**
     * Construir el mensaje de correo.
     */
    public function build()
    {
        return $this->subject('Nueva Notificación')
            ->view('emails.notificacion')
            ->with(['mensaje' => $this->mensaje]);
    }
}
