<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Resources\LoginResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    /**
     * Login a user and return a token for authentication.
     *
     * @param LoginRequest $req
     * @return LoginResource|JsonResponse
     */
    public function login(LoginRequest $req): LoginResource|JsonResponse
    {
        $credentials = $req->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return $this->error('Invalid credentials', [], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('api-token')->plainTextToken;
        return (new LoginResource([
            'token' => $token,
            'user' => $user,
        ]));
    }


    /**
     * Logout a user by deleting their current access token.
     *
     * @param Request $req
     * @return JsonResponse
     */
    public function logout(Request $req): JsonResponse
    {
        $req->user()->currentAccessToken()->delete();
        return $this->success(
            null,
            'Logged out successfully'
        );
    }
}
