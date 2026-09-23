<?php

namespace App\Mail;

use App\Models\Consulta;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Mail que recibe la inmobiliaria cuando alguien deja una consulta en la web.
 */
class NuevaConsulta extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Consulta $consulta) {}

    public function envelope(): Envelope
    {
        $asunto = $this->consulta->propiedad
            ? 'Nueva consulta por: '.$this->consulta->propiedad->titulo
            : 'Nueva consulta desde la web';

        return new Envelope(
            subject: $asunto,
            // Al tocar "Responder" en el mail se le contesta directo al cliente.
            replyTo: [new Address($this->consulta->email, $this->consulta->nombre)],
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.nueva-consulta');
    }
}
