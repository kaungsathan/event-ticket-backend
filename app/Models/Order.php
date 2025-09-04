<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'event_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'quantity',
        'total_amount',
        'payment_status',
        'payment_method',
        'status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'total_amount' => 'decimal:2',
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
