<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'employee_limit',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * Get price as float for comparisons
     */
    public function getPriceFloat(): float
    {
        $value = $this->getRawOriginal('price');
        if ($value === null || $value === '' || !is_numeric($value)) {
            return 0.0;
        }
        return (float) $value;
    }

    /**
     * Override the price accessor to handle invalid values safely
     */
    protected function castAttribute($key, $value)
    {
        if ($key === 'price') {
            // Handle null, empty, or non-numeric values
            if ($value === null || $value === '' || !is_numeric($value)) {
                return '0.00';
            }
        }
        
        return parent::castAttribute($key, $value);
    }

    /**
     * Get the companies for the plan
     */
    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }

    /**
     * Get the payments for the plan
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
