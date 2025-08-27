<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Payment extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'code',
        'description',
        'type',
        'is_active',
        'processing_fee_percentage',
        'processing_fee_fixed',
        'sort_order',
        'settings',
        'icon',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'processing_fee_percentage' => 'decimal:2',
        'processing_fee_fixed' => 'decimal:2',
        'settings' => 'array',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'settings', // Hide sensitive settings like API keys
    ];

        /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            // Auto-generate code from name if not provided
            if (empty($payment->code) && !empty($payment->name)) {
                $payment->code = strtolower(str_replace(' ', '_', $payment->name));
            }
        });
    }

    /**
     * Get the orders that use this payment method.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'payment_method', 'code');
    }

    /**
     * Scope: Filter by active status.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Filter by inactive status.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope: Filter by type.
     */
    public function scopeType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope: Order by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Scope: Get digital payment methods.
     */
    public function scopeDigital($query)
    {
        return $query->where('type', 'digital');
    }

    /**
     * Scope: Get cash payment methods.
     */
    public function scopeCash($query)
    {
        return $query->where('type', 'cash');
    }

        /**
     * Check if payment method is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Check if payment method is inactive.
     */
    public function isInactive(): bool
    {
        return !$this->is_active;
    }

    /**
     * Check if this is a digital payment method.
     */
    public function isDigital(): bool
    {
        return $this->type === 'digital';
    }

    /**
     * Check if this is a cash payment method.
     */
    public function isCash(): bool
    {
        return $this->type === 'cash';
    }

    /**
     * Activate the payment method.
     */
    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    /**
     * Deactivate the payment method.
     */
    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Calculate processing fee for given amount.
     */
    public function calculateProcessingFee(float $amount): float
    {
        $percentageFee = ($amount * $this->processing_fee_percentage) / 100;
        return $percentageFee + $this->processing_fee_fixed;
    }

    /**
     * Calculate total amount including processing fee.
     */
    public function calculateTotalWithFee(float $baseAmount): float
    {
        return $baseAmount + $this->calculateProcessingFee($baseAmount);
    }
}
