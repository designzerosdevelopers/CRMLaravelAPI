<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\TheMail;
use App\Models\Invitation;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use App\Providers\RouteServiceProvider;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class InviteController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.controlpanel.invite.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $token = Str::random(40);
try{
        Invitation::create([
            'creator_id' => Auth()->user()->id,
            'email' => $request->email,
            'token' => $token,
        ]);

        Mail::to($request->email)->send(new TheMail($token));

        return response()->json([
            'success' => true,
            'message' => 'Email sent successfully.',
            'data'=> $request->email
        ], Response::HTTP_CREATED);
    } catch (\Exception $e) {
        Log::error("Email sending failed: " . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to send email '
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function accept_invitation($token)
    {
        //$user= Invitation::where('token',$token)->first();

        return view('pages.controlpanel.invite.reg-invitation');
    }

    public function store_invitation(Request $request)
    {


        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],

        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ])->assignrole('employee');

        $validatedData['creator_id'] = $request->creator_id;
        $validatedData['user_id'] = $user->id;
        // Create the Employee using the validated data
        Employee::create($validatedData);


        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
