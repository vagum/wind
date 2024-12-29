<?php

namespace App\Listeners\Post;

use App\Events\Post\StoredPostEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendNotifyListener
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
    public function handle(StoredPostEvent $event): void
    {
        dump(222222222);
    }
}
