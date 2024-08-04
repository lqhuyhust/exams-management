<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PrizeNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $examName;
    public $prize;
    public $address;

    /**
     * Create a new message instance.
     */
    public function __construct($examName, $prize, $address)
    {
        $this->examName = $examName;
        $this->prize = $prize;
        $this->address = $address;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Prize Notification Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.prize-notification',
            with: [
                'examName' => $this->examName,
                'prizeTitle' => $this->prize->title,
                'prizeAmount' => $this->prize->amount,
                'address' => $this->address,
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
