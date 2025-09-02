<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Facades\Storage;

class Event extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'title',
        'description',
        'featured_image',
        'gallery_images',
        'image_alt_text',
        'start_date',
        'end_date',
        'location',
        'price',
        'max_attendees',
        'is_published',
        'created_by',
        'organizer_id',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'price' => 'decimal:2',
            'is_published' => 'boolean',
            'gallery_images' => 'array',
        ];
    }

    /**
     * Get the activity log options for the model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'description', 'start_date', 'end_date', 'location', 'price', 'max_attendees', 'is_published', 'organizer_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get the user that created the event.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the organizer of this event.
     */
    public function organizer(): BelongsTo
    {
        return $this->belongsTo(Organizer::class);
    }

    /**
     * Get the orders for this event.
     */
    public function orders()
    {
        return $this->hasMany(\App\Models\Order::class);
    }

    /**
     * Get the featured image URL
     *
     * @param string $size
     * @return string
     */
    public function getFeaturedImageUrlAttribute(string $size = 'original'): string
    {
        if (!$this->featured_image) {
            return $this->getDefaultImageUrl($size);
        }

        return Storage::disk('public')->url($this->featured_image);
    }

    /**
     * Get the featured image URL for different sizes
     *
     * @param string $size
     * @return string
     */
    public function getFeaturedImageUrl(string $size = 'original'): string
    {
        return $this->getFeaturedImageUrlAttribute($size);
    }

    /**
     * Get gallery images URLs
     *
     * @param string $size
     * @return array
     */
    public function getGalleryImageUrls(string $size = 'original'): array
    {
        if (!$this->gallery_images || !is_array($this->gallery_images)) {
            return [];
        }

        return array_map(function ($imagePath) use ($size) {
            return Storage::disk('public')->url($imagePath);
        }, $this->gallery_images);
    }

    /**
     * Get default image URL
     *
     * @param string $size
     * @return string
     */
    private function getDefaultImageUrl(string $size): string
    {
        $defaultImages = [
            'thumbnail' => '/images/defaults/event-thumbnail.jpg',
            'medium' => '/images/defaults/event-medium.jpg',
            'large' => '/images/defaults/event-large.jpg',
            'original' => '/images/defaults/event-default.jpg',
        ];

        return $defaultImages[$size] ?? $defaultImages['original'];
    }

    /**
     * Check if event has a featured image
     *
     * @return bool
     */
    public function hasFeaturedImage(): bool
    {
        return !empty($this->featured_image);
    }

    /**
     * Check if event has gallery images
     *
     * @return bool
     */
    public function hasGalleryImages(): bool
    {
        return !empty($this->gallery_images) && is_array($this->gallery_images);
    }

    /**
     * Get the number of gallery images
     *
     * @return int
     */
    public function getGalleryImageCountAttribute(): int
    {
        return $this->hasGalleryImages() ? count($this->gallery_images) : 0;
    }
}
