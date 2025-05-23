<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConfirmEnrolledMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $student;
    protected $attachment;
    public function __construct($student, $attachment = null)
    {
        $this->attachment = $attachment;
        $this->student = $student;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmación matricula',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mails.ConfirmEnrolled',
            with: ['student' => $this->student],

        );
    }

    public function attachments(): array
    {
        return [
            $this->attachment
        ];
    }
}
