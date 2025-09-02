<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ImageService
{
    /**
     * Upload and process a featured image for an event
     *
     * @param UploadedFile $image
     * @param string $eventSlug
     * @return string
     */
    public function uploadFeaturedImage(UploadedFile $image, string $eventSlug): string
    {
        $filename = $this->generateFilename($image, $eventSlug, 'featured');

        // Store original image
        $path = $image->storeAs("events/{$eventSlug}/featured", $filename, 'public');

        // Create optimized versions
        $this->createOptimizedVersions($image, $eventSlug, $filename);

        return $path;
    }

    /**
     * Upload multiple gallery images for an event
     *
     * @param array $images
     * @param string $eventSlug
     * @return array
     */
    public function uploadGalleryImages(array $images, string $eventSlug): array
    {
        $uploadedPaths = [];

        foreach ($images as $image) {
            if ($image instanceof UploadedFile) {
                $filename = $this->generateFilename($image, $eventSlug, 'gallery');
                $path = $image->storeAs("events/{$eventSlug}/gallery", $filename, 'public');

                // Create optimized versions
                $this->createOptimizedVersions($image, $eventSlug, $filename, 'gallery');

                $uploadedPaths[] = $path;
            }
        }

        return $uploadedPaths;
    }

    /**
     * Create optimized versions of uploaded images
     *
     * @param UploadedFile $image
     * @param string $eventSlug
     * @param string $filename
     * @param string $type
     * @return void
     */
    private function createOptimizedVersions(UploadedFile $image, string $eventSlug, string $filename, string $type = 'featured'): void
    {
        $imageInstance = Image::make($image);

        // Create thumbnail (300x300)
        $thumbnail = $imageInstance->fit(300, 300, function ($constraint) {
            $constraint->upsize();
        });

        $thumbnailPath = "events/{$eventSlug}/{$type}/thumbnails/" . $filename;
        Storage::disk('public')->put($thumbnailPath, $thumbnail->encode('jpg', 80));

        // Create medium size (800x600)
        $medium = $imageInstance->fit(800, 600, function ($constraint) {
            $constraint->upsize();
        });

        $mediumPath = "events/{$eventSlug}/{$type}/medium/" . $filename;
        Storage::disk('public')->put($mediumPath, $medium->encode('jpg', 85));

        // Create large size (1200x900) for featured images only
        if ($type === 'featured') {
            $large = $imageInstance->fit(1200, 900, function ($constraint) {
                $constraint->upsize();
            });

            $largePath = "events/{$eventSlug}/{$type}/large/" . $filename;
            Storage::disk('public')->put($largePath, $large->encode('jpg', 90));
        }
    }

    /**
     * Generate a unique filename for uploaded images
     *
     * @param UploadedFile $image
     * @param string $eventSlug
     * @param string $type
     * @return string
     */
    private function generateFilename(UploadedFile $image, string $eventSlug, string $type): string
    {
        $extension = $image->getClientOriginalExtension();
        $timestamp = now()->format('Y-m-d_H-i-s');
        $random = Str::random(8);

        return "{$eventSlug}_{$type}_{$timestamp}_{$random}.{$extension}";
    }

    /**
     * Delete event images when event is deleted
     *
     * @param string $eventSlug
     * @return void
     */
    public function deleteEventImages(string $eventSlug): void
    {
        Storage::disk('public')->deleteDirectory("events/{$eventSlug}");
    }

    /**
     * Get image URL with fallback
     *
     * @param string|null $path
     * @param string $size
     * @return string
     */
    public function getImageUrl(?string $path, string $size = 'original'): string
    {
        if (!$path) {
            return $this->getDefaultImageUrl($size);
        }

        // Check if optimized version exists
        $optimizedPath = $this->getOptimizedPath($path, $size);

        if (Storage::disk('public')->exists($optimizedPath)) {
            return Storage::disk('public')->url($optimizedPath);
        }

        // Fallback to original
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path);
        }

        return $this->getDefaultImageUrl($size);
    }

    /**
     * Get optimized image path for different sizes
     *
     * @param string $path
     * @param string $size
     * @return string
     */
    private function getOptimizedPath(string $path, string $size): string
    {
        $pathInfo = pathinfo($path);
        $directory = $pathInfo['dirname'];
        $filename = $pathInfo['basename'];

        switch ($size) {
            case 'thumbnail':
                return str_replace('/featured/', '/featured/thumbnails/', $path);
            case 'medium':
                return str_replace('/featured/', '/featured/medium/', $path);
            case 'large':
                return str_replace('/featured/', '/featured/large/', $path);
            default:
                return $path;
        }
    }

    /**
     * Get default image URL for different sizes
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
     * Validate image file
     *
     * @param UploadedFile $image
     * @return bool
     */
    public function validateImage(UploadedFile $image): bool
    {
        $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        return in_array($image->getMimeType(), $allowedMimes) &&
               $image->getSize() <= $maxSize;
    }
}
