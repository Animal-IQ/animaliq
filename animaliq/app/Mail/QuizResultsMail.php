<?php

namespace App\Mail;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuizResultsMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @param  array<int, array{number:int,prompt:string,status:string,your_answer:string,correct_answer:string,points:int}>  $reviewRows
     */
    public function __construct(
        public Quiz $quiz,
        public QuizAttempt $attempt,
        public string $recipientName,
        public array $reviewRows = [],
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your quiz results: ' . $this->quiz->title . ' (' . number_format((float) $this->attempt->percentage, 0) . '%)'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.quiz_results',
            with: [
                'quiz' => $this->quiz,
                'attempt' => $this->attempt,
                'recipientName' => $this->recipientName,
                'reviewRows' => $this->reviewRows,
            ],
        );
    }
}
