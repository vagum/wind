<?php

namespace App\Jobs\Comment;

use App\Mail\StoredUniversalMail;
use App\Models\Comment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class StoreCommentReplySendMailJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private Comment $comment, private $reply, private $replierProfile, private $replierEmail)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Отправляем уведомление владельцу комментария о новом ответе
        Mail::to($this->comment->user->email)->send(
            new StoredUniversalMail(
                $this->comment, // Передаем исходный комментарий, к которому добавлен ответ
                // (можно также передать сам ответ, если так удобнее)
                'Новый ответ на ваш комментарий', // Тема письма
                [
                    'action'   => 'reply',
                    'reply'    => $this->reply,
                    'replier'  => $this->replierProfile,
                ]
            )
        );

        // Отправляем уведомление инициатору ответа (подтверждение)
        // Если инициатор и владелец комментария не совпадают
        if ($this->replierEmail !== $this->comment->user->email) {
            Mail::to($this->replierEmail)->send(
                new StoredUniversalMail(
                    $this->comment,
                    'Ваш ответ успешно отправлен',
                    [
                        'action' => 'reply_confirmation',
                        'reply'  => $this->reply,
                    ]
                )
            );
        }
    }
}
