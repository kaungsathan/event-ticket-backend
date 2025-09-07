<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AttributeController extends Controller
{
    /**
     * Get all attributes
     * @param string $attribute
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $page = $request->get('page') ?? 1;
        $perPage = $request->get('per_page') ?? 5;
        $status = $request->get('status') ?? '';
        $attribute = $request->get('type');

        $allowedAttributes = ['category', 'tag', 'type'];

        if (!in_array($attribute, $allowedAttributes)) {
            return response()->json(['error' => 'Invalid attribute'], 400);
        }

        $model = 'App\\Models\\' . ucfirst($attribute);

        if ($status) {
            $query = $model::where('status', $status);
        } else {
            $query = $model::query();
        }

        $query->orderBy('created_at', 'desc');


        $data = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'message' => 'Fetching ' . $attribute . ' successfully',
            'data' => $data,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:category,tag,type',
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $type = $request->get('type');
        $name = $request->get('name');
        $status = $request->get('status');

        $model = 'App\\Models\\' . ucfirst($type);

        $data = $model::create([
            'name' => $name,
            'status' => $status,
        ]);

        return response()->json([
            'message' => 'Creating ' . $type . ' successfully',
            'data' => $data,
        ]);
    }

    public function show(string $attribute,int $id)
    {
        $model = 'App\\Models\\' . ucfirst($attribute);

        $data = $model::find($id);

        return response()->json([
            'message' => 'Fetching ' . $attribute . ' successfully',
            'data' => $data,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'type' => 'required|in:category,tag,type',
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $type = $request->get('type');
        $name = $request->get('name');
        $status = $request->get('status');

        $model = 'App\\Models\\' . ucfirst($type);

        $data = $model::find($id);

        $data->update([
            'name' => $name,
            'status' => $status,
        ]);

        return response()->json([
            'message' => 'Updating ' . $type . ' successfully',
            'data' => $data,
        ]);
    }

    public function destroy(string $attribute, int $id)
    {
        $model = 'App\\Models\\' . ucfirst($attribute);

        $data = $model::find($id);

        $data->delete();

        return response()->json([
            'message' => 'Deleting ' . $attribute . ' successfully',
            'data' => $data,
        ]);
    }


}
