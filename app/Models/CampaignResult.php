<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'target_email',
        'opened_at',
        'clicked_at',
        'submitted_data',
        'risk_level',
        'notes',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'clicked_at' => 'datetime',
        'submitted_data' => 'array',
    ];

    /**
     * Get the campaign that owns the result
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}
