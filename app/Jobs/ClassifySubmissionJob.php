<?php

namespace App\Jobs;

use App\Models\Submission;
use App\Services\AiClassificationService;
use App\Services\RoutingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ClassifySubmissionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public Submission $submission) {}

    public function handle(AiClassificationService $classifier, RoutingService $router): void
    {
        // Step 1: Classify the submission
        $classification = $classifier->classify($this->submission);

        // Step 2: Route based on classification
        $router->route($this->submission);
    }
}
