<?php

namespace App\Providers;

use App\Events\Post\StoredPostEvent;
use App\Listeners\Post\SendNotifyListener;
use App\Listeners\Post\WriteLogListener;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::unguard();
    }
}
