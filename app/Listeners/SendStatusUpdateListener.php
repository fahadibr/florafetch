<?php

namespace App\Listeners;

use App\Events\OrderStatusUpdated;
use App\Jobs\SendStatusUpdateJob;

class SendStatusUpdateListener
{
    public function handle(OrderStatusUpdated $event): void
    {
        SendStatusUpdateJob::dispatch($event->order);
    }
}
