<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CampaignTarget extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'name',
        'email',
    ];

    /**
     * Get the campaign that owns the target
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    /**
     * Get the interactions for this target
     */
    public function interactions(): HasMany
    {
        return $this->hasMany(Interaction::class, 'email', 'email')
            ->where('campaign_id', $this->campaign_id);
    }
}
