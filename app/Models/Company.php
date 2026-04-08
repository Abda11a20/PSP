<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Company extends Authenticatable implements JWTSubject
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
        'plan_id',
        'role',
        'two_factor_enabled',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'telegram_enabled',
        'telegram_bot_token',
        'telegram_chat_id',
    ];

    protected $casts = [
        'password' => 'hashed',
        'two_factor_enabled' => 'boolean',
        'two_factor_recovery_codes' => 'array',
        'telegram_enabled' => 'boolean',
    ];
    
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'telegram_bot_token',
    ];

    /**
     * Get the campaigns for the company
     */
    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    /**
     * Get the users for the company
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the payments for the company
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the plan that the company belongs to
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
