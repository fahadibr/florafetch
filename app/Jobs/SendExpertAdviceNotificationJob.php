<?php

namespace App\Jobs;

use App\Mail\ExpertAdviceMail;
use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendExpertAdviceNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Review $review) {}

    public function handle(): void
    {
        $user = $this->review->user;

        if ($user->email) {
            Mail::to($user->email)->send(new ExpertAdviceMail($this->review));
        }
    }
}
