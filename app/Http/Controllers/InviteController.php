<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\TheMail;
use App\Models\Invitation;
use App\Models\Employee;
use App\Models\User;
use App\Models\Candidate;
use App\Models\Organization;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use App\Providers\RouteServiceProvider;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Spatie\Permission\Models\Role;

class InviteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $invitations = Invitation::all();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data'    => $invitations,
            ], Response::HTTP_OK);
        }

        return view('pages.controlpanel.invite.index', compact('invitations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (request()->expectsJson()) {
            // For API calls, you might simply return a message or needed data
            return response()->json([
                'message' => 'Provide invitation data',
            ], Response::HTTP_OK);
        }

        return view('pages.controlpanel.invite.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $token = Str::random(40);

        try {
            Invitation::create([
                'creator_id' => Auth::user()->id,
                'email'      => $request->email,
                'token'      => $token,
            ]);

            Mail::to($request->email)->send(new TheMail($token));

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Email sent successfully.',
                    'data'    => $request->email,
                ], Response::HTTP_CREATED);
            } else {
                return redirect()->route('invite.index')->with('status', 'Email sent successfully.');
            }
        } catch (\Exception $e) {
            Log::error("Email sending failed: " . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send email',
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            } else {
                return redirect()->back()->withErrors(['message' => 'Failed to send email']);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Implement this method as needed
    }

    /**
     * Show the invitation acceptance.
     */
    public function accept_invitation($token)
    {
        Log::info('Token received', ['token' => $token]);
        $invitation = Invitation::where('token', $token)->first();
        Log::info('Invitation found', ['invitation' => $invitation]);

        if (!$invitation) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired token.',
                ], Response::HTTP_NOT_FOUND);
            } else {
                return redirect()->route('invite.create')->withErrors(['message' => 'Invalid or expired token.']);
            }
        }

        if (request()->expectsJson()) {
            return response()->json([
                'success'    => true,
                'invitation' => $invitation,
            ], Response::HTTP_OK);
        } else {
            return view('pages.controlpanel.invite.reg-invitation', compact('invitation'));
        }
    }

    /**
     * Handle registration for an invitation.
     */
    public function store_invitation(Request $request)
    {
        Log::info('Registration data', $request->all());

        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = null;

        if ($request->has('creator_id')) {
            // Invited user flow (Employee)
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ])->assignRole('employee');

            Employee::create([
                'creator_id' => $request->creator_id,
                'user_id'    => $user->id,
            ]);
        } elseif ($request->has('is_candidate')) {
            // Candidate flow
            $pathname = null;
            if ($request->hasFile('cv')) {
                $cvFile = $request->file('cv');
                $destinationPath = public_path('cv');
                $destinationFileName = time() . '_' . $cvFile->getClientOriginalName();
                $cvFile->move($destinationPath, $destinationFileName);
                $pathname = 'cv/' . $destinationFileName;
            }

            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'image'    => 'noImage.jpg',
            ])->assignRole('candidate');

            Candidate::create([
                'user_id'    => $user->id,
                'cv'         => $pathname,
                'profession' => $request->profession ?? null,
            ]);
        } else {
            // Organization flow
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ])->assignRole('organization');

            Organization::create([
                'user_id'           => $user->id,
                'organization_name' => $request->organization_name ?? null,
                'website'           => $request->website ?? null,
            ]);

            // Optionally, assign additional permissions to the organization role
            $orgRole = Role::findByName('organization');
            $orgRole->givePermissionTo(['job-delete', 'job-edit', 'job-create', 'job-view']);
        }

        event(new Registered($user));
        Auth::login($user);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Registration successful',
                'user'    => $user,
                'token'   => $user->createToken('auth_token')->plainTextToken,
            ], Response::HTTP_CREATED);
        } else {
            return redirect(RouteServiceProvider::HOME)->with('status', 'Registration successful');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Implement update logic as needed
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Implement deletion logic as needed
    }
}
