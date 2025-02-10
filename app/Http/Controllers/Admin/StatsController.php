<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Stats\IndexRequest;
use App\Http\Resources\Stats\StatsResource;
use App\Models\Stats;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Request;
use Inertia\Response;

class StatsController extends Controller
{
    public function index(IndexRequest $request): AnonymousResourceCollection|Response
    {
        $data = $request->validationData();

        // Разрешённые поля для сортировки
        $allowedSortColumns = [
            'date',
            'posts_count',
            'comments_count',
            'likes_count',
            'replies_count',
            'views_count',
            'likes_views',
            'likes_comments',
        ];

        // Начинаем запрос с учетом фильтров
        $query = Stats::filter($data);

        // Если в запросе присутствует параметр сортировки и он разрешен, применяем его
        if (isset($data['sort_column']) && in_array($data['sort_column'], $allowedSortColumns)) {
            $direction = (isset($data['sort_direction']) && strtolower($data['sort_direction']) === 'desc')
                ? 'desc'
                : 'asc';
            $query->orderBy($data['sort_column'], $direction);
        } else {
            // Если сортировка не задана, сортируем по умолчанию по дате (от новых к старым)
            $query->orderBy('date', 'desc');
        }

        // Выполняем пагинацию
        $stats = $query->paginate($data['per_page'], ['*'], 'page', $data['page']);
        $stats = StatsResource::collection($stats);

        // Массив с фильтрами и их типами (для формы фильтров на фронтенде)
        $active_filters = [
            'date_from'         => 'date',
            'posts_count_from'  => 'number',
            'comments_count_from' => 'number',
            'likes_count_from'  => 'number',
            'replies_count_from'=> 'number',
            'views_count_from'  => 'number',
        ];

        if (Request::wantsJson()) {
            return $stats;
        }

        // Возвращаем данные во фронтенд через Inertia
        return inertia('Admin/Stats/Index', [
            'stats'          => $stats,
            'active_filters' => $active_filters,
        ]);
    }

}
