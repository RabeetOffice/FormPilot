<?php

namespace App\Services;

use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SpamDetectionService
{
    protected array $disposableEmailDomains = [
        'mailinator.com', 'guerrillamail.com', 'throwaway.email', 'temp-mail.org',
        'yopmail.com', 'sharklasers.com', 'trashmail.com', 'tempmail.com',
        'fakeinbox.com', 'maildrop.cc', 'dispostable.com', '10minutemail.com',
    ];

    protected array $spamKeywords = [
        'buy now', 'click here', 'limited offer', 'act now', 'congratulations',
        'winner', 'free money', 'earn money', 'work from home', 'nigerian prince',
        'crypto', 'bitcoin', 'investment opportunity', 'guaranteed income',
        'seo services', 'link building', 'backlinks', 'casino', 'viagra',
    ];

    /**
     * Analyze a submission for spam signals
     */
    public function analyze(array $normalized, array $rawPayload, Request $request): array
    {
        $score = 0.0;
        $reasons = [];
        $honeypotTriggered = false;

        // 1. Honeypot check (highest confidence)
        if ($this->checkHoneypot($rawPayload)) {
            $score += 5.0;
            $reasons[] = 'honeypot_triggered';
            $honeypotTriggered = true;
        }

        // 2. Disposable email check
        if ($this->isDisposableEmail($normalized['email'] ?? '')) {
            $score += 3.0;
            $reasons[] = 'disposable_email';
        }

        // 3. Missing critical fields
        if (empty($normalized['email']) && empty($normalized['phone'])) {
            $score += 2.0;
            $reasons[] = 'no_contact_info';
        }

        // 4. Spam keywords in message
        $keywordScore = $this->checkSpamKeywords($normalized['message'] ?? '');
        if ($keywordScore > 0) {
            $score += $keywordScore;
            $reasons[] = 'spam_keywords';
        }

        // 5. Excessive links in message
        $linkCount = $this->countLinks($normalized['message'] ?? '');
        if ($linkCount > 2) {
            $score += min($linkCount * 0.5, 3.0);
            $reasons[] = 'excessive_links';
        }

        // 6. Rate limiting check (same IP within 5 minutes)
        if ($this->isRateLimited($request->ip())) {
            $score += 2.0;
            $reasons[] = 'rate_limited';
        }

        // 7. Duplicate submission check (same email+message within 5 min)
        if ($this->isDuplicate($normalized)) {
            $score += 4.0;
            $reasons[] = 'duplicate_submission';
        }

        // 8. Invalid email format
        if (!empty($normalized['email']) && !filter_var($normalized['email'], FILTER_VALIDATE_EMAIL)) {
            $score += 2.0;
            $reasons[] = 'invalid_email';
        }

        return [
            'score' => min($score, 10.0), // Cap at 10
            'is_spam' => $score >= 5.0,
            'honeypot_triggered' => $honeypotTriggered,
            'reasons' => $reasons,
        ];
    }

    protected function checkHoneypot(array $payload): bool
    {
        // FormPilot honeypot field name
        $honeypotFields = ['_fp_hp', '_hp_field', 'website_url_confirm', 'fax_number'];
        foreach ($honeypotFields as $field) {
            if (!empty($payload[$field])) {
                return true;
            }
        }
        return false;
    }

    protected function isDisposableEmail(string $email): bool
    {
        if (empty($email)) return false;
        $domain = strtolower(substr($email, strpos($email, '@') + 1));
        return in_array($domain, $this->disposableEmailDomains);
    }

    protected function checkSpamKeywords(string $text): float
    {
        if (empty($text)) return 0;
        $textLower = strtolower($text);
        $count = 0;
        foreach ($this->spamKeywords as $keyword) {
            if (str_contains($textLower, $keyword)) {
                $count++;
            }
        }
        return min($count * 0.5, 3.0);
    }

    protected function countLinks(string $text): int
    {
        return preg_match_all('/https?:\/\/|www\./i', $text);
    }

    protected function isRateLimited(string $ip): bool
    {
        $key = 'form_submit_' . md5($ip);
        $count = Cache::get($key, 0);
        Cache::put($key, $count + 1, now()->addMinutes(5));
        return $count >= 10; // More than 10 submissions from same IP in 5 minutes
    }

    protected function isDuplicate(array $normalized): bool
    {
        if (empty($normalized['email'])) return false;

        return Submission::where('email', $normalized['email'])
            ->where('message', $normalized['message'] ?? '')
            ->where('created_at', '>=', now()->subMinutes(5))
            ->exists();
    }
}
