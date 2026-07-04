<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Jobs\SendOrderConfirmationJob;

class SendOrderConfirmationListener
{
    public function handle(OrderPlaced $event): void
    {
        SendOrderConfirmationJob::dispatch($event->order);
    }
}
