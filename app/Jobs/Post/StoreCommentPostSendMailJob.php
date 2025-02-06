<?php

namespace App\Jobs\Post;

use App\Mail\StoredUniversalMail;
use App\Models\Post;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class StoreCommentPostSendMailJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private Post $post, private $comment, private $commenterProfile, private $commenterEmail)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Отправляем уведомление владельцу поста о новом комментарии
        Mail::to($this->post->user->email)->send(
            new StoredUniversalMail(
                $this->post, // Модель поста
                'Новый комментарий к вашему посту', // Тема письма
                [
                    'action'    => 'comment',
                    'comment'   => $this->comment,
                    'commenter' => $this->commenterProfile,
                ]
            )
        );

        // Отправляем уведомление автору комментария (подтверждение)
        // Если автор комментария и владелец поста не совпадают
        if ($this->commenterEmail !== $this->post->user->email) {
            Mail::to($this->commenterEmail)->send(
                new StoredUniversalMail(
                    $this->post,
                    'Ваш комментарий успешно отправлен',
                    [
                        'action'  => 'comment_confirmation',
                        'comment' => $this->comment,
                    ]
                )
            );
        }
    }
}
