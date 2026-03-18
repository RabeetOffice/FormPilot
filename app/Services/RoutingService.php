<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\RoutingRule;
use App\Models\Submission;

class RoutingService
{
    /**
     * Route a submission based on workspace routing rules
     */
    public function route(Submission $submission): ?Assignment
    {
        $rules = RoutingRule::where('workspace_id', $submission->workspace_id)
            ->where('is_active', true)
            ->orderBy('priority')
            ->get();

        foreach ($rules as $rule) {
            if ($this->matches($rule, $submission)) {
                return $this->assign($submission, $rule);
            }
        }

        // Try fallback rule
        $fallback = $rules->where('type', 'fallback')->first();
        if ($fallback) {
            return $this->assign($submission, $fallback);
        }

        return null;
    }

    protected function matches(RoutingRule $rule, Submission $submission): bool
    {
        if ($rule->type === 'fallback') return false; // handled separately

        $conditions = $rule->conditions ?? [];

        return match ($rule->type) {
            'service_type' => $this->matchServiceType($conditions, $submission),
            'brand' => $this->matchBrand($conditions, $submission),
            'budget' => $this->matchBudget($conditions, $submission),
            'spam_score' => $this->matchSpamScore($conditions, $submission),
            'country' => $this->matchCountry($conditions, $submission),
            default => false,
        };
    }

    protected function matchServiceType(array $conditions, Submission $submission): bool
    {
        $serviceType = $submission->aiClassification?->service_type;
        return $serviceType && ($conditions['value'] ?? null) === $serviceType;
    }

    protected function matchBrand(array $conditions, Submission $submission): bool
    {
        return ($conditions['value'] ?? null) == $submission->brand_id;
    }

    protected function matchBudget(array $conditions, Submission $submission): bool
    {
        if (empty($submission->budget)) return false;
        $budgetNum = (int) preg_replace('/[^0-9]/', '', $submission->budget);
        $threshold = (int) ($conditions['value'] ?? 0);
        $operator = $conditions['operator'] ?? 'gte';

        return match ($operator) {
            'gte' => $budgetNum >= $threshold,
            'lte' => $budgetNum <= $threshold,
            'gt' => $budgetNum > $threshold,
            'lt' => $budgetNum < $threshold,
            default => false,
        };
    }

    protected function matchSpamScore(array $conditions, Submission $submission): bool
    {
        $threshold = (float) ($conditions['value'] ?? 5);
        return $submission->spam_score < $threshold; // Route if NOT spam
    }

    protected function matchCountry(array $conditions, Submission $submission): bool
    {
        return $submission->country && ($conditions['value'] ?? null) === $submission->country;
    }

    protected function assign(Submission $submission, RoutingRule $rule): Assignment
    {
        // Mark previous active assignments as reassigned
        Assignment::where('submission_id', $submission->id)
            ->where('status', 'active')
            ->update(['status' => 'reassigned']);

        return Assignment::create([
            'submission_id' => $submission->id,
            'assigned_to' => $rule->target_user_id,
            'reason' => "Routing rule: {$rule->name}",
            'status' => 'active',
            'assigned_at' => now(),
        ]);
    }
}
