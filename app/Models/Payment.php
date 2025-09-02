<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'code',
        'status'
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
        return $query->where('status', 'active');
    }

    /**
     * Scope: Filter by inactive status.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope: Filter by type.
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

        /**
     * Check if payment method is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if payment method is inactive.
     */
    public function isInactive(): bool
    {
        return $this->status === 'inactive';
    }

    /**
     * Check if this is a digital payment method.
     */
    public function isDigital(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if this is a cash payment method.
     */
    public function isCash(): bool
    {
        return $this->status === 'inactive';
    }

    /**
     * Activate the payment method.
     */
    public function activate(): void
    {
        $this->update(['status' => 'active']);
    }

    /**
     * Deactivate the payment method.
     */
    public function deactivate(): void
    {
        $this->update(['status' => 'inactive']);
    }

    /**
     * Calculate processing fee for given amount.
     */
    public function calculateProcessingFee(float $amount): float
    {
        return 0;
    }

    /**
     * Calculate total amount including processing fee.
     */
    public function calculateTotalWithFee(float $baseAmount): float
    {
        return $baseAmount;
    }
}
