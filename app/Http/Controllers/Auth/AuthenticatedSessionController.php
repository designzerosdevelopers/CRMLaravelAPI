<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Symfony\Component\HttpFoundation\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle regular user login (non-candidate).
     */
    public function store(LoginRequest $request)
    {
        return $this->authenticateUser($request, ['candidate']);
    }

    /**
     * Handle candidate login.
     */
    public function storeCandidate(LoginRequest $request)
    {
        return $this->authenticateUser($request, [], ['candidate']);
    }

    /**
     * Common authentication logic for different user types.
     *
     * @param LoginRequest $request
     * @param array $deniedRoles Roles that are not allowed to login via this endpoint
     * @param array $allowedRoles If specified, only these roles can login
     */
    protected function authenticateUser(
        LoginRequest $request,
        array $deniedRoles = [],
        array $allowedRoles = []
    ) {
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
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You are not allowed to log in here',
                    ], 403);
                }
                return redirect()->back()->withErrors('You are not allowed to log in here');
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
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You are not authorized to access this route',
                    ], 403);
                }
                return redirect()->back()->withErrors('You are not authorized to access this route');
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

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'user'  => $user->only(['id', 'name', 'email']),
                    'roles' => $roles,
                    'token' => $token,
                ],
                'message' => 'Login successful',
            ]);
        } else {
            // For non-JSON requests, log the user in using the session and redirect
            auth()->login($user);
            return redirect()->intended('/dashboard')->with('success', 'Login successful');
        }
    }

    /**
     * Show the login view.
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Logout user by revoking the current access token.
     *
     * For API requests, this returns a JSON response.
     * For web requests, it redirects to the login page.
     */
    // public function destroy(Request $request)
    // {
    //     if (!$request->user()) {
    //         if ($request->expectsJson()) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Unauthenticated',
    //             ], 401);
    //         }
    //         return redirect()->route('login')->withErrors('Unauthenticated');
    //     }

    //     // Check if the user has an active access token before deleting.
    //     if ($token = $request->user()->currentAccessToken()) {
    //         $token->delete();
    //     }

    //     Log::info("User logged out: {$request->user()->email}");

    //     if ($request->expectsJson()) {
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Logout successful',
    //         ]);
    //     }
    //     return redirect()->route('login')->with('success', 'Logout successful');
    // }


public function destroy(Request $request)
{
    // Get the authenticated user (if any)
    $user = Auth::user();

    // Logout and clear session
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    // Check if the request expects JSON (API request)
    if ($request->expectsJson()) {
        return response()->json(['message' => 'Logged out successfully.'], Response::HTTP_OK);
    }

    // For normal web requests, redirect based on role
    if ($user && $user->hasRole('candidate')) {
        return redirect()->route('user-login');
    } else {
        return redirect()->intended(RouteServiceProvider::HOME);
    }
}

}
