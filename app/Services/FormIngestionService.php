<?php

namespace App\Services;

use App\Models\Domain;
use App\Models\Submission;
use App\Jobs\ClassifySubmissionJob;
use App\Jobs\RouteSubmissionJob;
use App\Jobs\SendNotificationJob;
use Illuminate\Http\Request;

class FormIngestionService
{
    public function __construct(
        protected FieldNormalizationService $normalizer,
        protected SpamDetectionService $spamDetector,
    ) {}

    /**
     * Process an incoming form submission
     */
    public function process(Domain $domain, array $payload, Request $request): Submission
    {
        // Normalize the raw payload into standard fields
        $normalized = $this->normalizer->normalize($payload);

        // Run spam detection (deterministic rules first)
        $spamResult = $this->spamDetector->analyze($normalized, $payload, $request);

        // Build the submission
        $submission = Submission::create([
            'workspace_id' => $domain->brand->workspace_id,
            'brand_id' => $domain->brand_id,
            'domain_id' => $domain->id,
            'form_source_id' => $domain->formSources()->first()?->id,

            // Normalized contact fields
            'full_name' => $normalized['full_name'] ?? null,
            'first_name' => $normalized['first_name'] ?? null,
            'last_name' => $normalized['last_name'] ?? null,
            'email' => $normalized['email'] ?? null,
            'phone' => $normalized['phone'] ?? null,
            'company' => $normalized['company'] ?? null,
            'subject' => $normalized['subject'] ?? null,
            'message' => $normalized['message'] ?? null,
            'budget' => $normalized['budget'] ?? null,

            // Tracking
            'page_url' => $normalized['page_url'] ?? $request->header('Referer'),
            'source_url' => $normalized['source_url'] ?? null,
            'referrer' => $normalized['referrer'] ?? $request->header('Referer'),
            'utm_source' => $normalized['utm_source'] ?? null,
            'utm_medium' => $normalized['utm_medium'] ?? null,
            'utm_campaign' => $normalized['utm_campaign'] ?? null,

            // Raw data
            'raw_payload' => $payload,
            'normalized_payload' => $normalized,

            // Request metadata
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),

            // Spam results
            'spam_score' => $spamResult['score'],
            'is_spam' => $spamResult['is_spam'],
            'honeypot_triggered' => $spamResult['honeypot_triggered'],
            'status' => $spamResult['is_spam'] ? 'archived' : 'new',
        ]);

        // Dispatch async jobs for AI classification and routing (only for non-spam)
        if (!$submission->is_spam) {
            ClassifySubmissionJob::dispatch($submission)->onQueue('classification');
            SendNotificationJob::dispatch($submission, 'new_submission')->onQueue('notifications');
        }

        return $submission;
    }
}
