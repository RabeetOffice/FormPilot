<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiClassification extends Model
{
    protected $fillable = [
        'submission_id', 'lead_temperature', 'service_type',
        'spam_probability', 'urgency', 'sentiment',
        'summary', 'routing_recommendation',
        'raw_response', 'model_used',
    ];

    protected $casts = [
        'spam_probability' => 'decimal:4',
        'raw_response' => 'array',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }
}
