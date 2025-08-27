<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Role;
use Illuminate\Http\JsonResponse;

class OptionController extends Controller
{
    public function getRoles(): JsonResponse
    {
        $roles = Role::all();
        return response()->json([
            'message' => 'All roles are fetched',
            'data' => $roles,
        ]);
    }
}
