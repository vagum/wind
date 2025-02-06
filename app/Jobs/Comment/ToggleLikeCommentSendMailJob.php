<?php

namespace App\Jobs\Comment;

use App\Mail\StoredUniversalMail;
use App\Models\Comment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class ToggleLikeCommentSendMailJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private Comment $comment, private $likerProfile, private $likerEmail)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->comment->user->email)->send(
            new StoredUniversalMail(
                $this->comment, // Передаём модель комментария
                'Ваш комментарий получил новый лайк', // Тема письма
                [
                    'action' => 'like',
                    'liker'  => $this->likerProfile,
                ]
            )
        );
        // Отправляем уведомление инициатору лайка
        // Если инициатор и владелец не совпадают
        if ($this->likerEmail !== $this->comment->user->email) {
            Mail::to($this->likerEmail)->send(
                new StoredUniversalMail(
                    $this->comment, // Модель комментария
                    'Вы поставили лайк', // Тема письма для инициатора
                    [
                        'action' => 'like_confirmation',
                        'target' => $this->comment,
                    ]
                )
            );
        }
    }
}
