<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ExamRegistrationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $exam;
    public $token;

    /**
     * Create a new message instance.
     */
    public function __construct($exam, $token)
    {
        $this->exam = $exam;
        $this->token = $token;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Exam Registration Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.exam-registration',
            with: [
                'examName' => $this->exam->name,
                'examStartTime' => $this->exam->start_time,
                'examEndTime' => $this->exam->end_time,
                'examURL' => env('APP_URL') . "/exam/{$this->exam->id}?token={$this->token}",
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
