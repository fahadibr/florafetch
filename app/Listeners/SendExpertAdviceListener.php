<?php

namespace App\Listeners;

use App\Events\ExpertAdvicePosted;
use App\Jobs\SendExpertAdviceNotificationJob;

class SendExpertAdviceListener
{
    public function handle(ExpertAdvicePosted $event): void
    {
        SendExpertAdviceNotificationJob::dispatch($event->review);
    }
}
