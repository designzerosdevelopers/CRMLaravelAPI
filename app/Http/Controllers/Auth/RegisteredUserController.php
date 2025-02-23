<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\User;
use App\Models\Organization;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;
use Illuminate\View\View;
use App\Jobs\PdfLabeler;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {

        return view('auth.register');
    }

    public function create_candidate(): View
    {

        return view('auth.candidate.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
{
    // Debugging - Check if the function is being reached
    // dd("hello registration"); // REMOVE THIS

    // Validate request
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
    ]);

    // Determine if registering as candidate or organization
    if ($request->has('is_candidate')) {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'image' => 'noImage.jpg',
        ])->assignRole('candidate');

        $pathname = null;
        if ($request->hasFile('cv')) {
            $cvFile = $request->file('cv');
            $destinationPath = public_path('cv');
            $destinationFileName = time() . '_' . $cvFile->getClientOriginalName();
            $cvFile->move($destinationPath, $destinationFileName);
            $pathname = 'cv/' . $destinationFileName; // Save relative path
            PdfLabeler::dispatch($pathname, $user);
        }

        Candidate::create([
            'user_id' => $user->id,
            'cv' => $pathname,
            'profession' => $request->profession,
        ]);
    } else {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ])->assignRole('organization');

        Organization::create([
            'user_id' => $user->id,
            'organization_name' => $request->organization_name,
            'website' => $request->website,
        ]);

        $orgRole = Role::findByName('organization');
        $orgRole->givePermissionTo(['job-delete', 'job-edit', 'job-create', 'job-view']);
    }

    event(new Registered($user));
    Auth::login($user);

    // Return JSON response for React frontend
    return response()->json([
        'success' => true,
        'message' => 'Registration successful',
        'user' => $user,
        'token' => $user->createToken('auth_token')->plainTextToken, // If using Laravel Sanctum
    ], 201);
}

}
