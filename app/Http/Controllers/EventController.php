<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventRequest;
use App\Models\Event;
use App\Services\ImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EventController extends Controller
{
    protected ImageService $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    /**
     * Display a listing of events.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Event::with(['organizer', 'creator'])
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->filled('organizer_id'), function ($query) use ($request) {
                $query->where('organizer_id', $request->organizer_id);
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
            })
            ->when($request->filled('date_from'), function ($query) use ($request) {
                $query->where('start_date', '>=', $request->date_from);
            })
            ->when($request->filled('date_to'), function ($query) use ($request) {
                $query->where('end_date', '<=', $request->date_to);
            });

        // Only show published events to non-authenticated users
        if (!Auth::check()) {
            $query->where('is_published', true);
        }

        $events = $query->orderBy('start_date')->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $events,
        ]);
    }

    /**
     * Store a newly created event.
     */
    public function store(EventRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $eventData = $request->validated();
            $eventData['created_by'] = Auth::id();

            // Generate slug from title
            $eventData['slug'] = Str::slug($request->title);

            // Handle featured image upload
            if ($request->hasFile('featured_image')) {
                $featuredImage = $request->file('featured_image');

                if ($this->imageService->validateImage($featuredImage)) {
                    $eventData['featured_image'] = $this->imageService->uploadFeaturedImage(
                        $featuredImage,
                        $eventData['slug']
                    );
                }
            }

            // Handle gallery images upload
            if ($request->hasFile('gallery_images')) {
                $galleryImages = $request->file('gallery_images');
                $uploadedPaths = $this->imageService->uploadGalleryImages($galleryImages, $eventData['slug']);
                $eventData['gallery_images'] = $uploadedPaths;
            }

            $event = Event::create($eventData);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Event created successfully!',
                'data' => $event->load(['organizer', 'creator']),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            // Clean up uploaded images if event creation fails
            if (isset($eventData['featured_image'])) {
                Storage::disk('public')->delete($eventData['featured_image']);
            }
            if (isset($eventData['gallery_images'])) {
                foreach ($eventData['gallery_images'] as $imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to create event: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified event.
     */
    public function show(Event $event): JsonResponse
    {
        // Check if event is published for non-authenticated users
        if (!Auth::check() && !$event->is_published) {
            return response()->json([
                'success' => false,
                'message' => 'Event not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $event->load(['organizer', 'creator', 'orders']),
        ]);
    }

    /**
     * Update the specified event.
     */
    public function update(EventRequest $request, Event $event): JsonResponse
    {
        try {
            DB::beginTransaction();

            $eventData = $request->validated();

            // Handle featured image update
            if ($request->hasFile('featured_image')) {
                $featuredImage = $request->file('featured_image');

                if ($this->imageService->validateImage($featuredImage)) {
                    // Delete old featured image
                    if ($event->featured_image) {
                        Storage::disk('public')->delete($event->featured_image);
                    }

                    $eventData['featured_image'] = $this->imageService->uploadFeaturedImage(
                        $featuredImage,
                        $event->slug ?? Str::slug($event->title)
                    );
                }
            }

            // Handle gallery images update
            if ($request->hasFile('gallery_images')) {
                $galleryImages = $request->file('gallery_images');
                $uploadedPaths = $this->imageService->uploadGalleryImages(
                    $galleryImages,
                    $event->slug ?? Str::slug($event->title)
                );

                // Merge with existing gallery images if any
                $existingImages = $event->gallery_images ?? [];
                $eventData['gallery_images'] = array_merge($existingImages, $uploadedPaths);
            }

            $event->update($eventData);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Event updated successfully!',
                'data' => $event->load(['organizer', 'creator']),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to update event: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified event.
     */
    public function destroy(Event $event): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Delete associated images
            if ($event->slug) {
                $this->imageService->deleteEventImages($event->slug);
            }

            $event->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Event deleted successfully!',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete event: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove a specific image from gallery
     */
    public function removeGalleryImage(Request $request, Event $event): JsonResponse
    {
        $request->validate([
            'image_path' => 'required|string',
        ]);

        $galleryImages = $event->gallery_images ?? [];

        if (in_array($request->image_path, $galleryImages)) {
            // Remove from array
            $galleryImages = array_diff($galleryImages, [$request->image_path]);

            // Delete file from storage
            Storage::disk('public')->delete($request->image_path);

            $event->update(['gallery_images' => array_values($galleryImages)]);

            return response()->json([
                'success' => true,
                'message' => 'Image removed successfully!',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Image not found in gallery.',
        ], 404);
    }

    /**
     * Toggle event publication status
     */
    public function togglePublish(Event $event): JsonResponse
    {
        $event->update(['is_published' => !$event->is_published]);

        return response()->json([
            'success' => true,
            'message' => $event->is_published ? 'Event published successfully!' : 'Event unpublished successfully!',
            'data' => $event,
        ]);
    }
}
