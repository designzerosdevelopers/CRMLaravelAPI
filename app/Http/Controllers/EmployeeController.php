<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $creator = auth()->user();
            Log::info("Fetching employees for creator ID: {$creator->id}");

            $employees = Employee::where('creator_id', $creator->id)
                ->with('user')
                ->get();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data'    => $employees
                ], Response::HTTP_OK);
            }

            // Fallback: return a view for web users.
            return view('pages.controlpanel.employee.index', compact('employees'));
        } catch (\Exception $e) {
            Log::error("Error fetching employees: " . $e->getMessage());
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to retrieve employees'
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            return redirect()->back()->withErrors('Failed to retrieve employees');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Log::info('Employee creation request', $request->all());

        $validator = Validator::make($request->all(), [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        if ($validator->fails()) {
            Log::warning('Validation failed', $validator->errors()->toArray());
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors'  => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ])->assignRole('employee');

            Employee::create([
                'creator_id' => auth()->id(),
                'user_id'    => $user->id
            ]);

            Log::info("Employee created successfully. User ID: {$user->id}");

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Employee created successfully.',
                    'data'    => $user->load('employees')
                ], Response::HTTP_CREATED);
            }
            return redirect()->route('employees.index')
                ->with('success', 'Employee created successfully.');
        } catch (\Exception $e) {
            Log::error("Employee creation failed: " . $e->getMessage());
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create employee'
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            return redirect()->back()->withErrors('Failed to create employee');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        try {
            $employee = Employee::with('user')
                ->where('user_id', $id)
                ->firstOrFail();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data'    => $employee
                ], Response::HTTP_OK);
            }
            return view('employees.show', compact('employee'));
        } catch (\Exception $e) {
            Log::error("Employee not found: {$id} - " . $e->getMessage());
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee not found'
                ], Response::HTTP_NOT_FOUND);
            }
            return redirect()->back()->withErrors('Employee not found');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        Log::info("Update method called with ID: {$id}");
        Log::info('User details', $request->all());

        try {
            $user = User::findOrFail($id);
            Log::info("User found with ID: {$id}");

            $employee = Employee::where('user_id', $id)->firstOrFail();
            Log::info("Employee found for user ID: {$id}");

            $validator = Validator::make($request->all(), [
                'name'               => ['sometimes', 'string', 'max:255'],
                'email'              => ['sometimes', 'string', 'email', 'max:255', 'unique:users,email,' . $id],
                'password'           => ['sometimes', 'confirmed', Rules\Password::defaults()],
                'phone_number'       => ['nullable', 'string', 'max:255'],
                'gender'             => ['nullable', 'in:Male,Female,Other'],
                'birth_date'         => ['nullable', 'date'],
                'address'            => ['nullable', 'string', 'max:255'],
                'zipcode'            => ['nullable', 'string', 'max:10'],
                'latest_degree'      => ['nullable', 'string', 'max:255'],
                'latest_university'  => ['nullable', 'string', 'max:255'],
                'current_organization'=> ['nullable', 'string', 'max:255'],
                'current_department' => ['nullable', 'string', 'max:255'],
                'current_position'   => ['nullable', 'string', 'max:255'],
                'avatar'             => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            ]);

            if ($validator->fails()) {
                Log::warning('Update validation failed', $validator->errors()->toArray());
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors'  => $validator->errors()
                    ], Response::HTTP_UNPROCESSABLE_ENTITY);
                }
                return redirect()->back()->withErrors($validator)->withInput();
            }

            // Update basic user fields
            $user->update($request->only(['name', 'email']));
            Log::info("User updated: {$id}");

            if ($request->has('password')) {
                $user->password = Hash::make($request->password);
                $user->save();
                Log::info("Password updated for user ID: {$id}");
            }

            // Update employee-specific fields (excluding name, email, password)
            $employee->update($request->except(['name', 'email', 'password']));

            // Handle avatar upload if provided
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
                $employee->picture = $avatarPath;
                $employee->save();
                Log::info("Avatar updated for user ID: {$id}");
            }

            Log::info("Employee updated successfully. User ID: {$id}");

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Employee updated successfully.',
                    'data'    => $user->load('employees')
                ], Response::HTTP_OK);
            }
            return redirect()->route('employees.show', $id)
                ->with('success', 'Employee updated successfully.');
        } catch (\Exception $e) {
            Log::error("Update failed for ID {$id}: " . $e->getMessage());
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update employee'
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            return redirect()->back()->withErrors('Failed to update employee');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            Employee::where('user_id', $id)->delete();
            $user->deleteWithRolesAndPermissions();

            Log::info("Employee deleted successfully. User ID: {$id}");
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Employee deleted successfully.'
                ], Response::HTTP_OK);
            }
            return redirect()->route('employees.index')
                ->with('success', 'Employee deleted successfully.');
        } catch (\Exception $e) {
            Log::error("Delete failed for ID {$id}: " . $e->getMessage());
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete employee'
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            return redirect()->back()->withErrors('Failed to delete employee');
        }
    }

    /**
     * Admin-specific endpoint: Display a listing of employees for a given creator.
     */
    public function adminIndex(Request $request, $creatorId)
    {
        try {
            Log::info("Admin fetching employees for creator ID: {$creatorId}");

            $employees = Employee::where('creator_id', $creatorId)
                ->with('user')
                ->get();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data'    => $employees
                ], Response::HTTP_OK);
            }

            return view('pages.controlpanel.employee.index', compact('employees'));
        } catch (\Exception $e) {
            Log::error("Admin employee fetch failed: " . $e->getMessage());
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to retrieve employees'
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            return redirect()->back()->withErrors('Failed to retrieve employees');
        }
    }
}
