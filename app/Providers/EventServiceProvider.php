<?php

namespace App\Providers;

use App\Events\ExpertAdvicePosted;
use App\Events\OrderPlaced;
use App\Events\OrderStatusUpdated;
use App\Jobs\SendExpertAdviceNotificationJob;
use App\Jobs\SendOrderConfirmationJob;
use App\Jobs\SendStatusUpdateJob;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        OrderPlaced::class => [
            \App\Listeners\SendOrderConfirmationListener::class,
        ],
        OrderStatusUpdated::class => [
            \App\Listeners\SendStatusUpdateListener::class,
        ],
        ExpertAdvicePosted::class => [
            \App\Listeners\SendExpertAdviceListener::class,
        ],
    ];
}
