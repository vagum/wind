<?php

namespace App\Listeners\Tag;

use App\Events\Tag\StoredTagEvent;
use App\Models\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class WriteLogListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(StoredTagEvent $event): void
    {
        $modelName = class_basename($event->tag);
        $eventName = class_basename($this);

        $changedAttributes = $event->tag->getDirty();
        $originalAttributes = $event->tag->getOriginal();

        // Исключаем временные метки из обоих массивов
        $excludedAttributes = ['created_at', 'updated_at', 'deleted_at'];

        // Фильтрация изменённых атрибутов
        $changedAttributes = array_diff_key($changedAttributes, array_flip($excludedAttributes));
        $originalAttributes = array_diff_key($originalAttributes, array_flip($excludedAttributes));

        // Запись в лог
        Log::create([
            'model_name' => $modelName,
            'event_name' => $eventName.'@store',
            'old_column' => !empty($changedAttributes) ? json_encode($changedAttributes) : null,
            'new_column' => !empty($originalAttributes) ? json_encode($originalAttributes) : null,
        ]);

        echo "Tag info stored to log!\n";
        dump($event);
    }
}
