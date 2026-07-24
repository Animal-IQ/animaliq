<?php

namespace App\Mail;

use App\Models\Quiz;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewQuizPublishedNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Quiz $quiz,
        public string $recipientName
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'New Quiz: ' . $this->quiz->title);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new_quiz',
            with: [
                'quiz' => $this->quiz,
                'recipientName' => $this->recipientName,
            ],
        );
    }
}
