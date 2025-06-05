<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EnrolledMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $student;
    protected $details;
    protected $updated;
    public function __construct($student, $details, $updated = false)
    {
        $this->student = $student;
        $this->details = $details;
        $this->updated = $updated;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Matricula registrada',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mails.Enrolled',
            with: [
                'student' => $this->student,
                'details' => $this->details,
                'updated' => $this->updated,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
