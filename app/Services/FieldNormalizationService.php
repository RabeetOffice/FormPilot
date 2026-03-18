<?php

namespace App\Services;

class FieldNormalizationService
{
    /**
     * Maps common form field names to our normalized schema
     */
    protected array $fieldMappings = [
        'full_name' => ['full_name', 'fullname', 'your-name', 'your_name', 'name', 'contact_name', 'customer_name'],
        'first_name' => ['first_name', 'firstname', 'fname', 'first'],
        'last_name' => ['last_name', 'lastname', 'lname', 'last', 'surname'],
        'email' => ['email', 'email_address', 'your-email', 'your_email', 'mail', 'e-mail', 'contact_email'],
        'phone' => ['phone', 'phone_number', 'your-phone', 'your_phone', 'tel', 'telephone', 'mobile', 'cell'],
        'company' => ['company', 'company_name', 'organization', 'org', 'business', 'business_name'],
        'subject' => ['subject', 'your-subject', 'your_subject', 'topic', 'inquiry_type', 'reason'],
        'message' => ['message', 'your-message', 'your_message', 'comments', 'comment', 'body', 'description', 'details', 'inquiry', 'note', 'notes', 'text'],
        'budget' => ['budget', 'project_budget', 'estimated_budget', 'price_range', 'investment'],
        'page_url' => ['page_url', 'pageurl', 'page', 'landing_page', 'form_url'],
        'source_url' => ['source_url', 'sourceurl', 'source'],
        'referrer' => ['referrer', 'referer', 'ref'],
        'utm_source' => ['utm_source'],
        'utm_medium' => ['utm_medium'],
        'utm_campaign' => ['utm_campaign'],
    ];

    /**
     * Normalize incoming payload to standard field names
     */
    public function normalize(array $payload): array
    {
        $normalized = [];

        // Remove common honeypot/system fields
        $excluded = ['_token', '_method', 'csrf_token', '_fp_hp', '_fp_t', 'g-recaptcha-response', 'h-captcha-response'];
        $payload = array_diff_key($payload, array_flip($excluded));

        // Map known fields
        foreach ($this->fieldMappings as $standardField => $aliases) {
            foreach ($aliases as $alias) {
                $key = $this->findKey($payload, $alias);
                if ($key !== null && !empty($payload[$key])) {
                    $normalized[$standardField] = trim($payload[$key]);
                    break;
                }
            }
        }

        // Auto-split full_name into first_name + last_name if not already set
        if (!empty($normalized['full_name']) && empty($normalized['first_name'])) {
            $parts = $this->splitName($normalized['full_name']);
            $normalized['first_name'] = $parts['first_name'];
            $normalized['last_name'] = $parts['last_name'];
        }

        // Build full_name from parts if not set
        if (empty($normalized['full_name']) && !empty($normalized['first_name'])) {
            $normalized['full_name'] = trim(
                ($normalized['first_name'] ?? '') . ' ' . ($normalized['last_name'] ?? '')
            );
        }

        // Clean email
        if (!empty($normalized['email'])) {
            $normalized['email'] = strtolower(trim($normalized['email']));
        }

        // Clean phone
        if (!empty($normalized['phone'])) {
            $normalized['phone'] = preg_replace('/[^\d+\-\(\)\s]/', '', $normalized['phone']);
        }

        return $normalized;
    }

    /**
     * Case-insensitive key lookup supporting both snake_case and kebab-case
     */
    protected function findKey(array $payload, string $target): ?string
    {
        $targetLower = strtolower($target);
        foreach ($payload as $key => $value) {
            if (strtolower($key) === $targetLower) {
                return $key;
            }
        }
        return null;
    }

    /**
     * Split a full name into first and last parts
     */
    protected function splitName(string $fullName): array
    {
        $parts = preg_split('/\s+/', trim($fullName), 2);
        return [
            'first_name' => $parts[0] ?? '',
            'last_name' => $parts[1] ?? '',
        ];
    }
}
