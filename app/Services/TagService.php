<?php

namespace App\Services;

use App\Models\Tag;

class TagService
{
    public static function storeBatch(array $data): array
    {
        $tagIds = [];
        foreach ($data as $tagTitle) {
            // Обрезаем пробелы по краям и приводим к нижнему регистру
            $normalizedTitle = strtolower(trim($tagTitle));

            // Если после обрезания тега он пустой, пропускаем его
            if ($normalizedTitle === '') {
                continue;
            }

            $tagIds[] = Tag::firstOrCreate([
                'title' => $normalizedTitle,
            ])->id;
        }

        return $tagIds;
    }
}
