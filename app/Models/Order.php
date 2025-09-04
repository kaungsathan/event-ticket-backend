<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Order extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'event_id',
        'amount',
        'payment_method',
        'status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
    ];

    /**
     * Boot the model.
     */
    // protected static function boot()
    // {
    //     parent::boot();

    //     static::creating(function ($order) {
    //         if (empty($order->id)) {
    //             $order->id = static::generateOrderId();
    //         }
    //     });
    // }

    // /**
    //  * Generate a unique order number.
    //  */
    // public static function generateOrderId(): string
    // {
    //     do {
    //         $orderNumber = 'ORD-' . strtoupper(uniqid());
    //     } while (static::where('id', $orderNumber)->exists());

    //     return $orderNumber;
    // }

    /**
     * Get the user that owns the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the event for this order.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
