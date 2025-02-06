<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AggregateFullStatsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stats:aggregate-all-dates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Агрегирует статистику по датам и сохраняет в таблицу stats';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Начало агрегирования статистики...');

        // Получаем уникальные даты из таблиц, исключая записи с NULL в created_at
        $datesPosts = DB::table('posts')
            ->whereNotNull('created_at')
            ->select(DB::raw('DATE(created_at) as date'))
            ->distinct()
            ->pluck('date')
            ->toArray();

        $datesComments = DB::table('comments')
            ->whereNotNull('created_at')
            ->select(DB::raw('DATE(created_at) as date'))
            ->distinct()
            ->pluck('date')
            ->toArray();

        $datesViews = DB::table('post_profile_views')
            ->whereNotNull('created_at')
            ->select(DB::raw('DATE(created_at) as date'))
            ->distinct()
            ->pluck('date')
            ->toArray();

        $datesLikes = DB::table('likeables')
            ->whereNotNull('created_at')
            ->select(DB::raw('DATE(created_at) as date'))
            ->distinct()
            ->pluck('date')
            ->toArray();

        // Объединяем даты и убираем дубли
        $allDates = array_unique(array_merge($datesPosts, $datesComments, $datesViews, $datesLikes));

        // Если дат нет – выходим
        if (empty($allDates)) {
            $this->info('Нет данных для агрегации.');
            return 0;
        }

        // Сортируем даты (по возрастанию)
        sort($allDates);

        // Текущая метка времени для created_at / updated_at
        $now = Carbon::now();

        foreach ($allDates as $date) {

            // Подсчёт количества постов, созданных в эту дату
            $postsCount = DB::table('posts')
                ->whereNotNull('created_at')
                ->whereDate('created_at', $date)
                ->count();

            // Подсчёт количества корневых комментариев (без parent_id) за эту дату
            $commentsCount = DB::table('comments')
                ->whereNotNull('created_at')
                ->whereDate('created_at', $date)
                ->whereNull('parent_id')
                ->count();

            // Подсчёт количества ответов (comments с ненулевым parent_id) за эту дату
            $repliesCount = DB::table('comments')
                ->whereNotNull('created_at')
                ->whereDate('created_at', $date)
                ->whereNotNull('parent_id')
                ->count();

            // Подсчёт количества просмотров постов за эту дату
            $viewsCount = DB::table('post_profile_views')
                ->whereNotNull('created_at')
                ->whereDate('created_at', $date)
                ->count();

            // Подсчёт лайков для постов за эту дату (likeable_type = 'App\Models\Post')
            $likesForPosts = DB::table('likeables')
                ->whereNotNull('created_at')
                ->whereDate('created_at', $date)
                ->where('likeable_type', 'App\Models\Post')
                ->count();

            // Подсчёт лайков для комментариев за эту дату (likeable_type = 'App\Models\Comment')
            $likesForComments = DB::table('likeables')
                ->whereNotNull('created_at')
                ->whereDate('created_at', $date)
                ->where('likeable_type', 'App\Models\Comment')
                ->count();

            // Общее количество лайков (сумма лайков для постов и комментариев)
            $likesCount = $likesForPosts + $likesForComments;

            // Вычисляем отношения (если делитель равен нулю, то ставим 0)
            $likesViews = $viewsCount > 0 ? $likesCount / $viewsCount : 0;
            $likesComments = $commentsCount > 0 ? $likesCount / $commentsCount : 0;

            // Собираем данные для вставки
            $data = [
                'date'           => $date,
                'posts_count'    => $postsCount,
                'comments_count' => $commentsCount,
                'replies_count'  => $repliesCount,
                'views_count'    => $viewsCount,
                'likes_count'    => $likesCount,   // Новое поле с общим количеством лайков
                'likes_views'    => $likesViews,
                'likes_comments' => $likesComments,
                'created_at'     => $now,
                'updated_at'     => $now,
            ];

            // Вставляем или обновляем запись в таблице stats для данной даты.
            // Здесь предполагается, что в таблице stats поле date уникально.
            DB::table('stats')->updateOrInsert(
                ['date' => $date],
                $data
            );

            $this->info("Статистика за {$date} сохранена.");
        }

        $this->info('Агрегация статистики завершена.');

        return 0;
    }
}
