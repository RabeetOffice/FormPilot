<?php

namespace App\Services;

use App\Models\AiClassification;
use App\Models\Submission;

class AiClassificationService
{
    /**
     * Rule-based classification (MVP — swappable with OpenAI later)
     */
    public function classify(Submission $submission): AiClassification
    {
        $data = [
            'lead_temperature' => $this->classifyTemperature($submission),
            'service_type' => $this->detectServiceType($submission),
            'spam_probability' => $this->calculateSpamProbability($submission),
            'urgency' => $this->detectUrgency($submission),
            'sentiment' => $this->detectSentiment($submission),
            'summary' => $this->generateSummary($submission),
            'routing_recommendation' => $this->suggestRouting($submission),
            'model_used' => 'rule_based_v1',
        ];

        return AiClassification::updateOrCreate(
            ['submission_id' => $submission->id],
            $data
        );
    }

    protected function classifyTemperature(Submission $submission): string
    {
        $score = 0;

        // Has budget → hotter
        if (!empty($submission->budget)) {
            $budgetNum = (int) preg_replace('/[^0-9]/', '', $submission->budget);
            if ($budgetNum > 5000) $score += 3;
            elseif ($budgetNum > 1000) $score += 2;
            else $score += 1;
        }

        // Has phone → more serious
        if (!empty($submission->phone)) $score += 1;

        // Has company → more serious
        if (!empty($submission->company)) $score += 1;

        // Message length indicates effort
        $msgLen = strlen($submission->message ?? '');
        if ($msgLen > 200) $score += 2;
        elseif ($msgLen > 50) $score += 1;

        // Urgency keywords
        $urgentWords = ['asap', 'urgent', 'immediately', 'right away', 'deadline', 'rush'];
        $msgLower = strtolower($submission->message ?? '');
        foreach ($urgentWords as $word) {
            if (str_contains($msgLower, $word)) {
                $score += 2;
                break;
            }
        }

        if ($score >= 5) return 'hot';
        if ($score >= 2) return 'warm';
        return 'cold';
    }

    protected function detectServiceType(Submission $submission): string
    {
        $text = strtolower(($submission->subject ?? '') . ' ' . ($submission->message ?? ''));

        $serviceMap = [
            'web_design' => ['website', 'web design', 'redesign', 'landing page', 'web app', 'frontend'],
            'seo' => ['seo', 'search engine', 'google ranking', 'organic traffic', 'keyword'],
            'marketing' => ['marketing', 'campaign', 'advertising', 'ads', 'social media', 'ppc', 'facebook', 'instagram'],
            'branding' => ['brand', 'logo', 'identity', 'visual identity', 'rebrand'],
            'consulting' => ['consult', 'strategy', 'advice', 'analysis', 'audit'],
            'development' => ['develop', 'programming', 'code', 'software', 'application', 'api'],
            'ecommerce' => ['ecommerce', 'e-commerce', 'online store', 'shopify', 'woocommerce'],
        ];

        foreach ($serviceMap as $service => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($text, $keyword)) {
                    return $service;
                }
            }
        }

        return 'general_inquiry';
    }

    protected function calculateSpamProbability(Submission $submission): float
    {
        return min(($submission->spam_score ?? 0) / 10, 1.0);
    }

    protected function detectUrgency(Submission $submission): string
    {
        $text = strtolower(($submission->subject ?? '') . ' ' . ($submission->message ?? ''));
        $highUrgency = ['asap', 'urgent', 'immediately', 'emergency', 'deadline', 'today', 'tomorrow'];
        $medUrgency = ['soon', 'this week', 'next week', 'within a month', 'quickly'];

        foreach ($highUrgency as $word) {
            if (str_contains($text, $word)) return 'high';
        }
        foreach ($medUrgency as $word) {
            if (str_contains($text, $word)) return 'medium';
        }
        return 'low';
    }

    protected function detectSentiment(Submission $submission): string
    {
        $text = strtolower($submission->message ?? '');

        $positiveWords = ['love', 'great', 'excellent', 'amazing', 'wonderful', 'impressed', 'thank', 'appreciate', 'excited', 'looking forward'];
        $negativeWords = ['disappointed', 'frustrated', 'angry', 'terrible', 'worst', 'complaint', 'issue', 'problem', 'unhappy', 'dissatisfied'];

        $positiveCount = 0;
        $negativeCount = 0;

        foreach ($positiveWords as $word) {
            if (str_contains($text, $word)) $positiveCount++;
        }
        foreach ($negativeWords as $word) {
            if (str_contains($text, $word)) $negativeCount++;
        }

        if ($positiveCount > $negativeCount) return 'positive';
        if ($negativeCount > $positiveCount) return 'negative';
        return 'neutral';
    }

    protected function generateSummary(Submission $submission): string
    {
        $name = $submission->getDisplayName();
        $service = $this->detectServiceType($submission);
        $brand = $submission->brand?->name ?? 'Unknown Brand';

        $summary = "{$name} submitted an inquiry";
        if ($service !== 'general_inquiry') {
            $summary .= " about " . str_replace('_', ' ', $service);
        }
        $summary .= " via {$brand}";

        if (!empty($submission->budget)) {
            $summary .= " with a budget of {$submission->budget}";
        }

        return $summary . '.';
    }

    protected function suggestRouting(Submission $submission): string
    {
        $service = $this->detectServiceType($submission);
        $temp = $this->classifyTemperature($submission);

        if ($temp === 'hot') {
            return "High priority — route to senior team member for {$service}";
        }
        return "Route to {$service} team";
    }
}
