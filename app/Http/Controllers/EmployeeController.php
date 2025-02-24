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

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $creator = auth()->user();
            Log::info("Fetching employees for creator ID: {$creator->id}");

            $employees = Employee::where('creator_id', $creator->id)
                ->with('user')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $employees
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error("Error fetching employees: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve employees'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Log::info('Employee creation request', $request->all());

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        if ($validator->fails()) {
            Log::warning('Validation failed', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ])->assignRole('employee');

            Employee::create([
                'creator_id' => auth()->id(),
                'user_id' => $user->id
            ]);

            Log::info("Employee created successfully. User ID: {$user->id}");

            return response()->json([
                'success' => true,
                'message' => 'Employee created successfully.',
                'data' => $user->load('employees')
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            Log::error("Employee creation failed: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create employee'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $employee = Employee::with('user')
                ->where('user_id', $id)
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => $employee
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error("Employee not found: {$id} - " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Employee not found'
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        Log::info("Updating employee ID: {$id}", $request->all());

        try {
            $user = User::findOrFail($id);
            $employee = Employee::where('user_id', $id)->firstOrFail();

            $validator = Validator::make($request->all(), [
                'name' => ['sometimes', 'string', 'max:255'],
                'email' => ['sometimes', 'string', 'email', 'max:255', 'unique:users,email,'.$id],
                'password' => ['sometimes', 'confirmed', Rules\Password::defaults()],
                'phone_number' => ['nullable', 'string', 'max:255'],
                'gender' => ['nullable', 'in:Male,Female,Other'],
                'birth_date' => ['nullable', 'date'],
                'address' => ['nullable', 'string', 'max:255'],
            ]);

            if ($validator->fails()) {
                Log::warning('Update validation failed', $validator->errors()->toArray());
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $user->update($request->only(['name', 'email']));

            if ($request->has('password')) {
                $user->password = Hash::make($request->password);
                $user->save();
            }

            $employee->update($request->except(['name', 'email', 'password']));

            Log::info("Employee updated successfully. User ID: {$id}");
            return response()->json([
                'success' => true,
                'message' => 'Employee updated successfully.',
                'data' => $user->load('employee')
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error("Update failed for ID {$id}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update employee'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            Employee::where('user_id', $id)->delete();
            $user->deleteWithRolesAndPermissions();

            Log::info("Employee deleted successfully. User ID: {$id}");
            return response()->json([
                'success' => true,
                'message' => 'Employee deleted successfully.'
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error("Delete failed for ID {$id}: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete employee'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Admin-specific endpoints
     */
    public function adminIndex($creatorId)
    {
        try {
            Log::info("Admin fetching employees for creator ID: {$creatorId}");

            $employees = Employee::where('creator_id', $creatorId)
                ->with('user')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $employees
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            Log::error("Admin employee fetch failed: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve employees'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
