<?php

namespace App\Jobs;

use App\Models\Submission;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public Submission $submission,
        public string $type = 'new_submission',
    ) {}

    public function handle(NotificationService $notificationService): void
    {
        match ($this->type) {
            'new_submission' => $notificationService->notifyNewSubmission($this->submission),
            'spam_alert' => $notificationService->notifySpamAlert($this->submission),
            default => null,
        };
    }
}
