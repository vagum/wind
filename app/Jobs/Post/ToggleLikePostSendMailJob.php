<?php

namespace App\Jobs\Post;

use App\Mail\StoredUniversalMail;
use App\Models\Post;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class ToggleLikePostSendMailJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private Post $post, private $likerProfile, private $likerEmail)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Здесь передаём модель поста, тему и дополнительные данные, например, кто поставил лайк
        Mail::to($this->post->user->email)->send(
            new StoredUniversalMail(
                $this->post, // Модель поста
                'Ваш пост получил новый лайк',
                [
                    'action' => 'like',
                    'liker' => $this->likerProfile,
                ]
            )
        );

        // Отправляем уведомление инициатору лайка (подтверждение), если он не является владельцем поста
        if ($this->likerEmail !== $this->post->user->email) {
            Mail::to($this->likerEmail)->send(
                new StoredUniversalMail(
                    $this->post, // Передаём модель поста
                    'Вы поставили лайк', // Тема письма для инициатора лайка
                    [
                        'action' => 'like_confirmation',
                        'target' => $this->post,
                    ]
                )
            );
        }
    }
}
