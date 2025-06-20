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
    protected $enrollment;
    protected $attachment;
    public function __construct($student, $enrollment, $attachment = null)
    {
        $this->attachment = $attachment;
        $this->student = $student;
        $this->enrollment = $enrollment;
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
            with: [
                'student' => $this->student,
                'enrollment' => $this->enrollment,
                'withAttachment' => $this->attachment == null ? false : true,
            ],
        );
    }

    public function attachments(): array
    {
        if ($this->attachment == null) {
            return [];
        }
        return [
            $this->attachment
        ];
    }
}
