<?php

namespace App\Listeners\Tag;

use App\Events\Tag\BeforeStoreTagEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class BeforeWriteLogListener
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
    public function handle(BeforeStoreTagEvent $event): void
    {
       echo "Are you ready to write log?\n";
       dump($event);
    }
}
