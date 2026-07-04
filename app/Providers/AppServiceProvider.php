<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\OrderPlaced;
use App\Events\OrderStatusUpdated;
use App\Events\ExpertAdvicePosted;
use App\Listeners\SendOrderConfirmationListener;
use App\Listeners\SendStatusUpdateListener;
use App\Listeners\SendExpertAdviceListener;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Event::listen(OrderPlaced::class, SendOrderConfirmationListener::class);
        Event::listen(OrderStatusUpdated::class, SendStatusUpdateListener::class);
        Event::listen(ExpertAdvicePosted::class, SendExpertAdviceListener::class);
    }
}
