<?php

namespace App\Http\Controllers;

use App\Models\Application_form;
use App\Models\Employee;
use App\Models\Job;
use App\Models\User;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class OrganizationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Retrieve organizations by joining users and organizations tables.
        $orgsdata = User::join('organizations', 'users.id', '=', 'organizations.user_id')
            ->select('organizations.*', 'users.name as user_name', 'users.email as user_email', 'users.id as user_id')
            ->get();

        // For each organization record, calculate employee and job counts.
        $organizations = $orgsdata->map(function ($org) {
            $empCount = Employee::where('creator_id', $org->user_id)->count();
            $jobCount = Job::where('organization_id', $org->user_id)->count();
            $org->employee_count = $empCount;
            $org->job_count = $jobCount;
            return $org;
        });

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data'    => $organizations,
            ], Response::HTTP_OK);
        }
        return view('pages.controlpanel.organization.index', ['organizations' => $organizations]);
    }

    /**
     * Show creation instructions.
     */
    public function create(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Provide organization details to create a new organization.'
            ], Response::HTTP_OK);
        }
        return view('organization.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Log::info("Reached OrganizationController@store");

        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        try {
            // Create the user and assign the "organization" role.
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ])->assignRole('organization');

            // Build organization data. Adjust fields as needed.
            $orgData = [
                'user_id'           => $user->id,
                'organization_name' => $request->name,
                // Additional fields like 'website', 'address', or 'description' can be added here.
            ];

            Organization::create($orgData);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Organization created successfully.',
                    'data'    => $user->load('organization')
                ], Response::HTTP_CREATED);
            }
            return redirect()->route('organization.index')
                ->with('success', 'Organization created successfully.');
        } catch (\Exception $e) {
            Log::error("Organization creation failed: " . $e->getMessage());
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create organization'
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            return redirect()->back()->withErrors('Failed to create organization');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $org = User::join('organizations', 'users.id', '=', 'organizations.user_id')
            ->where('users.id', $id)
            ->select('users.email', 'organizations.*')
            ->first();

        if (!$org) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Organization not found'
                ], Response::HTTP_NOT_FOUND);
            }
            return redirect()->route('organization.index')->withErrors('Organization not found');
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data'    => $org
            ], Response::HTTP_OK);
        }
        return view('organization.show', ['organization' => $org]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id)
    {
        $org = User::join('organizations', 'users.id', '=', 'organizations.user_id')
            ->where('users.id', $id)
            ->select('users.email', 'organizations.*')
            ->first();

        if (!$org) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Organization not found'
                ], Response::HTTP_NOT_FOUND);
            }
            return redirect()->route('organization.index')->withErrors('Organization not found');
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data'    => $org
            ], Response::HTTP_OK);
        }
        return view('organization.edit', ['organization' => $org]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'email'             => 'required|email',
            'organization_name' => 'required|string|max:255',
            'address'           => 'required|string|max:255',
            'description'       => 'required|string|max:1000',
            'website'           => 'nullable|max:255',
        ]);

        try {
            // Update the user email.
            User::where('id', $id)->update(['email' => $request->email]);

            // Update the organization details.
            Organization::where('user_id', $id)->update([
                'organization_name' => $request->organization_name,
                'address'           => $request->address,
                'description'       => $request->description,
                'website'           => $request->website,
            ]);

            // Retrieve the updated organization record.
            $org = User::join('organizations', 'users.id', '=', 'organizations.user_id')
                ->where('users.id', $id)
                ->select('users.email', 'organizations.*')
                ->first();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Organization updated successfully.',
                    'data'    => $org
                ], Response::HTTP_OK);
            }
            return redirect()->route('organizations.show', $id)
                ->with('success', 'Organization updated successfully.');
        } catch (\Exception $e) {
            Log::error("Organization update failed: " . $e->getMessage());
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update organization'
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            return redirect()->back()->withErrors('Failed to update organization');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $user = User::find($id);

        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Organization not found'
                ], Response::HTTP_NOT_FOUND);
            }
            return redirect()->route('organization.index')->withErrors('Organization not found');
        }

        try {
            // Delete associated organization, employees, jobs and their applications.
            Organization::where('user_id', $id)->delete();
            Employee::where('creator_id', $id)->delete();
            $jobs = Job::where('organization_id', $id)->get();

            foreach ($jobs as $job) {
                Application_form::where('job_id', $job->id)->delete();
            }
            $jobs->each->delete();
            $user->delete();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Organization deleted successfully.'
                ], Response::HTTP_OK);
            }
            return redirect()->route('organizations.index')
                ->with('success', 'Organization deleted successfully.');
        } catch (\Exception $e) {
            Log::error("Organization deletion failed: " . $e->getMessage());
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete organization'
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            return redirect()->back()->withErrors('Failed to delete organization');
        }
    }
}
