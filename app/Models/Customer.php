<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Customer extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'date_of_birth',
        'gender',
        'address',
        'city',
        'state',
        'emergency_contact_name',
        'emergency_contact_phone',
        'preferences',
        'is_active',
        'last_activity_at',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'datetime',
            'preferences' => 'array',
            'is_active' => 'boolean',
            'last_activity_at' => 'datetime',
        ];
    }

    /**
     * Get the activity log options for the model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'phone', 'address', 'city', 'state', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get the user that this customer profile belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the orders for this customer.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Scope active customers.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Update last activity timestamp.
     */
    public function updateLastActivity(): void
    {
        $this->update(['last_activity_at' => now()]);
    }

    /**
     * Check if customer has recent activity (within last 30 days).
     */
    public function hasRecentActivity(): bool
    {
        return $this->last_activity_at && $this->last_activity_at->gt(now()->subDays(30));
    }

    /**
     * Get customer's age based on date of birth.
     */
    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth ? $this->date_of_birth->diffInYears(now()) : null;
    }
}
