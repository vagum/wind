<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StoredUniversalMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Экземпляр модели (Post, Comment, Like и т.д.).
     */
    public mixed $model;

    /**
     * Текст темы письма.
     */
    public string $subjectText;

    /**
     * Дополнительные данные, если нужно.
     */
    public array $additionalData;

    /**
     * @param mixed  $model         Модель, которую вы хотите передать (Post, Comment, Like и т.д.)
     * @param string $subjectText   Тема письма.
     * @param array  $additionalData Дополнительные данные (если необходимо).
     */
    public function __construct($model, string $subjectText, array $additionalData = [])
    {
        $this->model = $model;
        $this->subjectText = $subjectText;
        $this->additionalData = $additionalData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectText,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.universal',
            // Передаём в шаблон универсальную модель и, возможно, дополнительные данные.
            with: [
                'model'          => $this->model,
                'additionalData' => $this->additionalData,
            ]
        );
    }
}
