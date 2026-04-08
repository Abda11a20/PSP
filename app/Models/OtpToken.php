<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class OtpToken extends Model
{
    protected $fillable = [
        'email',
        'otp',
        'type',
        'expires_at',
        'used',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used' => 'boolean',
    ];

    /**
     * Check if OTP is valid and not expired
     */
    public function isValid(): bool
    {
        return !$this->used && $this->expires_at->isFuture();
    }

    /**
     * Mark OTP as used
     */
    public function markAsUsed(): void
    {
        $this->update(['used' => true]);
    }

    /**
     * Clean up expired OTPs
     */
    public static function cleanupExpired(): void
    {
        static::where('expires_at', '<', now())->delete();
    }
}
