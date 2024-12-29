<?php

namespace App\Traits\Models\Traits;

use Illuminate\Support\Facades\Log;

trait HasLog
{
    /**
     * Boot the HasLog trait for a model.
     *
     * @return void
     */
    public static function bootHasLog(): void
    {
        static::created(function ($model) {
            $model->logAction('created');
        });

        static::updated(function ($model) {
            $model->logAction('updated');
        });

        static::deleted(function ($model) {
            $model->logAction('deleted');
        });

        // нужно закоментировать, если лог включать во всех моделях
        // т.к. вызывает переполнение памяти при залогиненном пользователе
        static::retrieved(function ($model) {
            $model->logAction('retrieved');
        });
    }

    /**
     * Log the action performed on the model.
     *
     * @param string $action
     * @return void
     */
    protected function logAction(string $action): void
    {
        // Получаем имя модели в нижнем регистре
        $modelName = strtolower(class_basename($this));

        $logData = [
            'action' => $action,
            'model' => get_class($this),
            'model_id' => $this->getKey(),
            'attributes' => $this->getAttributes(),
            'performed_at' => now(),
        ];

        // Добавляем changed_attributes только если они не пустые
        $changes = $this->getChanges();
        if (!empty($changes)) {
            $logData['changed_attributes'] = $changes;
        }

        // Добавляем performed_by только если пользователь авторизован
        if (auth()->check()) {
            $logData['performed_by'] = auth()->user();
        }

        // Настраиваем логгер для записи в отдельный файл
        $logPath = storage_path("logs/$modelName/$action.log");

        Log::build([
            'driver' => 'single',
            'path' => $logPath,
        ])->info(json_encode($logData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}
