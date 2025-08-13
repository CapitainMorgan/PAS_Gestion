<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ArticleEndReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $articles;

    /**
     * Create a new message instance.
     */
    public function __construct($articles)
    {
        $this->articles = $articles;
    }

 
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Rappel : Fin de dépôt pour vos articles')
                    ->bcc('info@pret-a-seduire.ch')
                    ->view('emails.articles_end_reminder')
                    ->with(['articles' => $this->articles]);
    }
    
}
