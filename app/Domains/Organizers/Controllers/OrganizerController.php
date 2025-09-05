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
        $organizers = $this->organizerService->getOrganizerList($request->all());

        return response()->json([
            'message' => 'Organizers fetched successfully',
            'data' => $organizers
        ]);
    }

    /**
     * Store a newly created organizer.
     */
    public function store(StoreOrganizerRequest $request)
    {
        $this->authorize('create', Organizer::class);

        $organizer = $this->organizerService->createOrganizer($request->validated(), auth()->user());

        return response()->json([
            'message' => 'Organizer created successfully',
            'data' => $organizer
        ]);
    }

    /**
     * Display the specified organizer.
     */
    public function show(int $id)
    {
        $this->authorize('viewAny', Organizer::class);

        $organizer = $this->organizerService->getOrganizer($id);

        return response()->json([
            'message' => 'Organizer fetched successfully',
            'data' => $organizer
        ]);
    }

    /**
     * Update the specified organizer.
     */
    public function update(UpdateOrganizerRequest $request, int $id)
    {
        $organizer = $this->organizerService->updateOrganizer($id, $request->validated(), auth()->user());

        return response()->json([
            'message' => 'Organizer updated successfully',
            'data' => $organizer
        ]);
    }

    /**
     * Remove the specified organizer.
     */
    public function destroy(int $id)
    {
        try {
            $this->organizerService->deleteOrganizer($id, auth()->user());

            return response()->json([
                'message' => 'Organizer deleted successfully',
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
