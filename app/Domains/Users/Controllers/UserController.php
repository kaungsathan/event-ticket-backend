<?php

namespace App\Domains\Users\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Domains\Users\Services\UserService;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request) : JsonResponse {
        $users = $this->userService->getUserList($request->all());

        return response()->json([
            'message' => 'Users fetched successfully',
            'data' => $users,
        ]);
    }

    public function show(int $id) : JsonResponse {
        $user = $this->userService->getUserById($id);

        return response()->json([
            'message' => 'User fetched successfully',
            'data' => $user,
        ]);
    }

    public function update(int $id, UpdateUserRequest $request) : JsonResponse {
        $user = $this->userService->update($id, $request->validated());

        return response()->json([
            'message' => 'User updated successfully',
            'data' => $user,
        ]);
    }

    public function store(CreateUserRequest $request)
    {
        $user = $this->userService->store($request->validated());

        return response()->json([
            'message' => 'User created successfully',
            'data' => $user,
        ], 201);
    }

    public function destroy(int $id)
    {
        $this->userService->destroy($id);

        return response()->json([
            'message' => 'User deleted successfully',
        ]);
    }
}
