<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Organization;
use App\Models\Candidate;
use App\Models\Degree;
use App\Models\User;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

use Illuminate\Support\Facades\Log;
class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request)
    {
        $user = $request->user();
        $profile = null;
        $data = ['user' => $user];

        if ($user->hasRole('employee')) {
            $profile = Employee::where('user_id', $user->id)->firstOrFail();
            $data['profile'] = $profile;
        } elseif ($user->hasRole('organization')) {
            $profile = Organization::where('user_id', $user->id)->firstOrFail();
            $data['profile'] = $profile;
        } elseif ($user->hasRole('candidate')) {
            $profile = Candidate::where('user_id', $user->id)->firstOrFail();
            $data['profile'] = $profile;
            $data['degree'] = Degree::find($profile->degree_id);
            $data['degrees'] = Degree::where('id', '!=', $profile->degree_id)->get();
        }
        // For super-admin or other roles, we only send the user

        // Check if request expects a JSON response
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json($data);
        }

        return view('profile.edit', $data);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        Log::info("data to update" ,$request->all());
        $user = $request->user();
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $rules = [
            'resume'                => 'nullable|mimes:pdf|max:2048',
            'organization_name'     => 'nullable|string|max:255',
            'website'               => 'nullable|string|max:255',
            'experience'            => 'nullable|string|max:255',
            'skill'                 => 'nullable|string|max:255',
            'profession'            => 'nullable|string|max:255',
            'address'               => 'nullable|string|max:255',
            'phone_number'          => 'nullable|string|max:255',
            'gender'                => 'nullable|in:Male,Female,Other',
            'birth_date'            => 'nullable|date',
            'zipcode'               => 'nullable|string|max:10',
            'degree_id'             => 'nullable',
            'latest_university'     => 'nullable|string|max:255',
            'current_organization'  => 'nullable|string|max:255',
            'current_department'    => 'nullable|string|max:255',
            'current_position'      => 'nullable|string|max:255',
            'description'           => 'nullable|string|max:500',
        ];

        $validatedData = $request->validate($rules);

        if ($user->hasRole('employee')) {
            $profile = Employee::where('user_id', $user->id)->firstOrFail();
        } elseif ($user->hasRole('organization')) {
            $profile = Organization::where('user_id', $user->id)->firstOrFail();
        } elseif ($user->hasRole('candidate')) {
            $profile = Candidate::where('user_id', $user->id)->firstOrFail();
        } elseif ($user->hasRole('super-admin')) {
            $profile = User::where('id', $user->id)->firstOrFail();
        } else {
            // Optionally handle unknown roles
            return Redirect::back()->withErrors(['role' => 'Invalid user role.']);
        }

        $profile->update($validatedData);

        // Return JSON response if requested, else redirect
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json(['status' => 'profile-updated',
        'user'=> $user
    ]);
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Handle profile image upload.
     */
    public function upload(Request $request)
    {

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:1000',
        ]);

        $user = User::where('id', $request->user()->id)->firstOrFail();

        $imageName = time() . '.' . $request->image->extension();
        $request->image->move(public_path('uploads'), $imageName);

        $user->update([
            'image' => $imageName,
        ]);

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => 'Image uploaded successfully.',
                'image' => asset('storage/' . $imageName),
                'user' => $user
            ]);
        }

        return back()->with('success', 'Image uploaded successfully.');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json(['status' => 'account-deleted']);
        }

        return Redirect::to('/');
    }
}
