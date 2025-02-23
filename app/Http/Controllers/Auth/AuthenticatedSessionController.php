<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle regular user login (non-candidate).
     */
    public function store(LoginRequest $request): JsonResponse
    {
        return $this->authenticateUser($request, ['candidate']);
    }

    /**
     * Handle candidate login.
     */
    public function storeCandidate(LoginRequest $request): JsonResponse
    {
        return $this->authenticateUser($request, [], ['candidate']);
    }

    /**
     * Common authentication logic for different user types.
     *
     * @param LoginRequest $request
     * @param array $deniedRoles Roles that are not allowed to login via this endpoint
     * @param array $allowedRoles If specified, only these roles can login
     * @return JsonResponse
     */
    protected function authenticateUser(
        LoginRequest $request,
        array $deniedRoles = [],
        array $allowedRoles = []
    ): JsonResponse {
        // Validate incoming credentials
        $credentials = $request->validated();

        // Retrieve the user by email
        $user = User::where('email', $credentials['email'])->first();

        // Verify user exists and password is correct
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            Log::warning('Failed login attempt for email: ' . $credentials['email']);
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        // Check for any denied roles
        foreach ($deniedRoles as $role) {
            if ($user->hasRole($role)) {
                Log::info("Denied login for {$role} role: {$user->email}");
                return response()->json([
                    'success' => false,
                    'message' => 'You are not allowed to log in here',
                ], 403);
            }
        }

        // If allowed roles are specified, check if the user has at least one
        if (!empty($allowedRoles)) {
            $hasAllowedRole = false;
            foreach ($allowedRoles as $role) {
                if ($user->hasRole($role)) {
                    $hasAllowedRole = true;
                    break;
                }
            }

            if (!$hasAllowedRole) {
                Log::info("Unauthorized role access: {$user->email}");
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to access this route',
                ], 403);
            }
        }

        // Determine token name based on the user's role
        $tokenName = $user->hasRole('candidate') ? 'Candidate-Token' : 'CRM-Token';

        // OPTIONAL: Revoke any existing token with the same name (if you want one active token per user)
        // $user->tokens()->where('name', $tokenName)->delete();

        // Create a new Sanctum token and get the plain text token
        $token = $user->createToken($tokenName)->plainTextToken;

        // Retrieve the user's roles (assuming Spatie Laravel Permission is used)
        $roles = $user->getRoleNames();

        Log::info("User logged in: {$user->email}");

        return response()->json([
            'success' => true,
            'data' => [
                'user'  => $user->only(['id', 'name', 'email']),
                'roles' => $roles, // roles included for frontend navigation/authorization
                'token' => $token, // plain text token to be used in Authorization header
            ],
            'message' => 'Login successful',
        ]);
    }


    public function create()
    {
        // Return your login view here.
        // If you are using Blade:
        return view('auth.login');
    }

    /**
     * Logout user by revoking the current access token.
     *
     * This method is protected by the auth:sanctum middleware.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Request $request): JsonResponse
    {
        // Check if the request is authenticated
        if (!$request->user()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        // Revoke the current token (so it can no longer be used)
        $request->user()->currentAccessToken()->delete();
        Log::info("User logged out: {$request->user()->email}");

        return response()->json([
            'success' => true,
            'message' => 'Logout successful',
        ]);
    }
}
