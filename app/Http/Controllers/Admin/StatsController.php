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
//        dd($data);
        $stats = Stats::filter($data)->orderBy('date','desc')->paginate($data['per_page'],'*','page',$data['page']);
        $stats = StatsResource::collection($stats);

        // Массив с фильтрами и их типами
        $active_filters = [
            'date_from' => 'date',
            'posts_count_from' => 'number',
            'comments_count_from' => 'number',
            'likes_count_from' => 'number',
            'replies_count_from' => 'number',
            'views_count_from' => 'number',
        ];

        if(Request::wantsJson()){
            return $stats;
        }

        // Передаем массив $active_filters вместе с $posts
        return inertia('Admin/Stats/Index', [
            'stats'          => $stats,
            'active_filters' => $active_filters,
        ]);
    }
}
