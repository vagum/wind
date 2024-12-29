<?php

namespace App\Traits;

use App\Models\Log;

trait LogChanges
{
    protected static function bootLogChanges()
    {
        // Используем динамичное имя модели
        $modelName = class_basename(static::class);

        // Логирование события создания
        static::created(function ($model) use ($modelName) {
            $model->logChanges("{$modelName}HasLogBooted@created");
        });

        // Логирование события обновления
        static::updated(function ($model) use ($modelName) {
            $model->logChanges("{$modelName}HasLogBooted@updated");
        });

        // Логирование события удаления
        static::deleted(function ($model) use ($modelName) {
            $model->logChanges("{$modelName}HasLogBooted@deleted");
        });

        // Логирование события получения
        static::retrieved(function ($model) use ($modelName) {
            $model->logChanges("{$modelName}HasLogBooted@retrieved");
        });
    }

    public function logChanges(string $eventName): void
    {
        $modelName = class_basename($this);

        $eventPart = explode('@', $eventName);
        $eventPart = end($eventPart);

        // Если событие 'retrieved', записываем null для старых и новых значений
        if ($eventPart === 'retrieved') {
            $oldValues = null;
            $newValues = null;
        } else {
            // Получаем измененные атрибуты и оригинальные значения
            $changedAttributes = $this->getDirty();
            $originalAttributes = $this->getOriginal();

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
        }

        // Запись в лог
        Log::create([
            'model_name' => $modelName,
            'event_name' => $eventName,
            'old_column' => !empty($oldValues) ? json_encode($oldValues) : null,
            'new_column' => !empty($newValues) ? json_encode($newValues) : null,
        ]);
    }
}
