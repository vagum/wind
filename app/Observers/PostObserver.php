<?php

namespace App\Observers;

use App\Models\Log;
use App\Models\Post;


class PostObserver
{
    private function logChanges(Post $post, string $modelName, string $eventName, array $changedAttributes = [], array $originalAttributes = []): void
    {
        // Если измененных атрибутов нет (например, при удалении), берем все оригинальные атрибуты
        if (empty($changedAttributes)) {
            $oldValues = $originalAttributes;
            $newValues = null; // При удалении новых значений нет
        } else {
            // Исключаем временные метки из логирования
            $excludedAttributes = ['created_at', 'updated_at'];

            // Формируем массивы для логирования
            $oldValues = [];
            $newValues = [];

            foreach ($changedAttributes as $attribute => $newValue) {
                if (in_array($attribute, $excludedAttributes)) {
                    continue;
                }

                $oldValues[$attribute] = $originalAttributes[$attribute] ?? null;
                $newValues[$attribute] = $newValue;
            }
        }

        Log::create([
                'model_name' => $modelName,
                'event_name' => $eventName,
                'old_column' => !empty($oldValues) ? json_encode($oldValues) : null,
                'new_column' => !empty($newValues) ? json_encode($newValues) : null,
        ]);

    }

    /**
     * Handle the Post "created" event.
     */
    public function created(Post $post): void
    {
        $this->logChanges($post, 'Post', 'PostObserver@created', $post->getAttributes(), []);
    }

    /**
     * Handle the Post "retrieved" event.
     */
    public function retrieved(Post $post): void
    {
        $this->logChanges($post, 'Post', 'PostObserver@retrieved', [], []);
    }

    /**
     * Handle the Post "updated" event.
     */
    public function updated(Post $post): void
    {
        $this->logChanges($post, 'Post', 'PostObserver@updated', $post->getDirty(), $post->getOriginal());
    }

    /**
     * Handle the Post "deleted" event.
     */
    public function deleted(Post $post): void
    {
        $this->logChanges($post, 'Post', 'PostObserver@deleted', [], $post->getOriginal());
    }

}
