<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Submission;
use App\Models\Workspace;

class NotificationService
{
    /**
     * Create notifications for a new submission
     */
    public function notifyNewSubmission(Submission $submission): void
    {
        $workspace = $submission->workspace;
        $brand = $submission->brand;

        // Notify all workspace admins and owners
        $users = $workspace->users()
            ->wherePivotIn('role', ['owner', 'admin'])
            ->get();

        foreach ($users as $user) {
            Notification::create([
                'workspace_id' => $workspace->id,
                'user_id' => $user->id,
                'type' => 'new_submission',
                'title' => 'New Lead from ' . ($brand?->name ?? 'Unknown'),
                'body' => ($submission->getDisplayName()) . ' submitted a form via ' . ($submission->domain?->domain ?? 'unknown'),
                'data' => [
                    'submission_id' => $submission->id,
                    'brand_name' => $brand?->name,
                    'email' => $submission->email,
                ],
            ]);
        }
    }

    /**
     * Notify when a submission is assigned
     */
    public function notifyAssignment(Submission $submission, int $assignedToId, string $reason = ''): void
    {
        Notification::create([
            'workspace_id' => $submission->workspace_id,
            'user_id' => $assignedToId,
            'type' => 'assignment',
            'title' => 'New Lead Assigned to You',
            'body' => "Lead from {$submission->getDisplayName()} has been assigned to you. {$reason}",
            'data' => [
                'submission_id' => $submission->id,
                'brand_name' => $submission->brand?->name,
            ],
        ]);
    }

    /**
     * Send a spam alert notification
     */
    public function notifySpamAlert(Submission $submission): void
    {
        $workspace = $submission->workspace;
        $admins = $workspace->users()
            ->wherePivotIn('role', ['owner', 'admin'])
            ->get();

        foreach ($admins as $admin) {
            Notification::create([
                'workspace_id' => $workspace->id,
                'user_id' => $admin->id,
                'type' => 'spam_alert',
                'title' => 'Spam Detected',
                'body' => "A submission from {$submission->email} was flagged as spam (score: {$submission->spam_score})",
                'data' => [
                    'submission_id' => $submission->id,
                    'spam_score' => $submission->spam_score,
                ],
            ]);
        }
    }
}
