<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Organizer extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'company_name',
        'description',
        'email',
        'company_phone',
        'company_address',
        'website',
        'address',
        'logo_url',
        'date',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'datetime',
        ];
    }

    /**
     * Get the activity log options for the model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['company_name', 'description', 'email', 'company_phone', 'website', 'address', 'date', 'status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get the user that created the organizer.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the events organized by this organizer.
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}
