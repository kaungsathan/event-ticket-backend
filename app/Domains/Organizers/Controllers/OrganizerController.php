<?php

namespace App\Domains\Organizers\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Organizers\Services\OrganizerService;
use App\Domains\Organizers\Requests\StoreOrganizerRequest;
use App\Domains\Organizers\Requests\UpdateOrganizerRequest;
use App\Models\Organizer;
use App\Shared\Traits\HasApiResponse;
use Illuminate\Http\Request;

class OrganizerController extends Controller
{
    use HasApiResponse;

    protected OrganizerService $organizerService;

    public function __construct(OrganizerService $organizerService)
    {
        $this->organizerService = $organizerService;
    }

    /**
     * Display a listing of organizers with filtering, sorting, and including.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Organizer::class);

        $organizers = $this->organizerService->getOrganizers([
            'per_page' => $request->get('per_page', 15)
        ]);

        return response()->json($organizers);
    }

    /**
     * Store a newly created organizer.
     */
    public function store(StoreOrganizerRequest $request)
    {
        $this->authorize('create', Organizer::class);

        $organizer = $this->organizerService->createOrganizer($request->validated(), auth()->user());

        return response()->json($organizer, 201);
    }

    /**
     * Display the specified organizer.
     */
    public function show(Organizer $organizer)
    {
        $this->authorize('view', $organizer);

        $organizer = $this->organizerService->getOrganizer($organizer);

        return response()->json($organizer);
    }

    /**
     * Update the specified organizer.
     */
    public function update(UpdateOrganizerRequest $request, Organizer $organizer)
    {
        $this->authorize('update', $organizer);

        $organizer = $this->organizerService->updateOrganizer($organizer, $request->validated(), auth()->user());

        return response()->json($organizer);
    }

    /**
     * Remove the specified organizer.
     */
    public function destroy(Organizer $organizer)
    {
        $this->authorize('delete', $organizer);

        try {
            $this->organizerService->deleteOrganizer($organizer, auth()->user());

            return response()->json(['message' => 'Organizer deleted successfully']);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Verify an organizer.
     */
    public function verify(Organizer $organizer)
    {
        $this->authorize('verify', $organizer);

        try {
            $organizer = $this->organizerService->verifyOrganizer($organizer, auth()->user());

            return response()->json([
                'message' => 'Organizer verified successfully',
                'organizer' => $organizer
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Activate/deactivate an organizer.
     */
    public function toggleStatus(Organizer $organizer)
    {
        $this->authorize('toggleStatus', $organizer);

        $organizer = $this->organizerService->toggleOrganizerStatus($organizer, auth()->user());
        $status = $organizer->is_active ? 'activated' : 'deactivated';

        return response()->json([
            'message' => "Organizer {$status} successfully",
            'organizer' => $organizer
        ]);
    }

    /**
     * Get events for a specific organizer.
     */
    public function events(Request $request, Organizer $organizer)
    {
        $this->authorize('view', $organizer);

        $events = $this->organizerService->getOrganizerEvents($organizer, [
            'per_page' => $request->get('per_page', 15)
        ]);

        return response()->json($events);
    }
}
