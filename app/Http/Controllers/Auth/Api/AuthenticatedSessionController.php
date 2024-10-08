<?php

namespace App\Http\Controllers\Auth\Api;

use App\Attributes\LoginOperation;
use App\Attributes\LogoutOperation;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    #[LoginOperation]
    public function store(LoginRequest $request): JsonResponse
    {
        $request->authenticate();

        $token = $request->user()->createToken($request->email)->plainTextToken;

        $user_resource = new UserResource($request->user()->load('roles'));

        return response()->json([
            'token' => $token,
            'expires' => 24 * 60 * 60 * 1000,
            ...$user_resource->toArray($request),
        ]);
    }

    /**
     * Destroy a token from database.
     */
    #[LogoutOperation]
    public function destroy(Request $request): Response
    {
        $request->user()->currentAccessToken()->delete();

        return response()->noContent();
    }
}
