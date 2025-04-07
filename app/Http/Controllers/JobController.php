<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Degree;
use App\Models\Employee;
use App\Models\Categories;
use App\Models\Organization;
use App\Models\Application_form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->user()->hasRole('employee')) {
            $org = Employee::where('user_id', auth()->user()->id)->first();
            $jobs = Job::where('organization_id', $org->creator_id)
                ->withCount('application_form')
                ->get();
        } elseif ($request->user()->hasRole('organization')) {
            $org = Organization::where('user_id', auth()->user()->id)->first();
            $jobs = Job::where('organization_id', $org->user_id)
                ->withCount('application_form')
                ->get();
        } else {
            $jobs = collect();
        }

        if ($request->expectsJson()) {
            return response()->json(['jobs' => $jobs], Response::HTTP_OK);
        }
        return view('pages.controlpanel.job.index', ['jobs' => $jobs]);
    }

    /**
     * Get data (categories and degrees).
     */
    public function getdata(Request $request)
    {
        try {
            $categories = Categories::all();
            $degrees = Degree::all();

            if ($request->expectsJson()) {
                return response()->json([
                    'success'    => true,
                    'message'    => 'Data fetched successfully.',
                    'categories' => $categories,
                    'degrees'    => $degrees
                ], Response::HTTP_OK);
            }
            return view('pages.controlpanel.job.data', compact('categories', 'degrees'));
        } catch (\Exception $e) {
            Log::error("Data fetching failed: " . $e->getMessage());
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch data'
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            return redirect()->back()->withErrors(['message' => 'Failed to fetch data']);
        }
    }

    /**
     * Display jobs for an admin view.
     */
    public function indexForAdmin(Request $request, $id)
    {
        $org = Organization::where('user_id', $id)->first();
        $jobs = Job::where('organization_id', $org->user_id)
            ->withCount('application_form')
            ->get();


            $count1 = Application_form::join('candidates', 'application_form.user_id', '=', 'candidates.user_id')
            ->join('users', 'candidates.user_id', '=', 'users.id')
            ->where('application_form.job_id', $id)
            ->count();

        $count2 = Application_form::whereNull('user_id')->count();

        $totalCount = $count1 + $count2;


        if ($request->expectsJson()) {
            return response()->json(['jobs' => $jobs, 'creator' => $id], Response::HTTP_OK);
        }
        return view('pages.controlpanel.job.index', ['jobs' => $jobs, 'creator' => $id]);
    }


    // public function indexForAdmin(Request $request, $id)
    // {
    //     // Get the organization by its user id
    //     $org = Organization::where('user_id', $id)->first();

    //     // Get jobs for the organization and include counts for two types of applications:
    //     // 1. Registered applications (joined with candidates and users)
    //     // 2. Unregistered applications (where user_id is null)
    //     $jobs = Job::where('organization_id', $org->user_id)
    //         ->withCount([
    //             // Count registered applications with join conditions
    //             'application_form as registered_count' => function ($query) {
    //                 $query->join('candidates', 'application_form.user_id', '=', 'candidates.user_id')
    //                       ->join('users', 'candidates.user_id', '=', 'users.id');
    //             },
    //             // Count unregistered applications
    //             'application_form as unregistered_count' => function ($query) {
    //                 $query->whereNull('user_id');
    //             }
    //         ])
    //         ->get();

    //     // Optionally, if you want a total count field on each job,
    //     // you can loop through the jobs and add it:
    //     foreach ($jobs as $job) {
    //         $job->total_count = $job->registered_count + $job->unregistered_count;
    //     }

    //     if ($request->expectsJson()) {
    //         return response()->json([
    //             'jobs'    => $jobs,
    //             'creator' => $id
    //         ], Response::HTTP_OK);
    //     }

    //     return view('pages.controlpanel.job.index', [
    //         'jobs'    => $jobs,
    //         'creator' => $id
    //     ]);
    // }



    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $this->hasPermission('job-create');
        $categories = Categories::all();
        $degrees = Degree::all();

        if ($request->expectsJson()) {
            return response()->json([
                'message'    => 'Provide job creation data',
                'categories' => $categories,
                'degrees'    => $degrees,
            ], Response::HTTP_OK);
        }
        return view('pages.controlpanel.job.create', ['categories' => $categories, 'degrees' => $degrees]);
    }

    /**
     * Show the form for creating a new resource for admin.
     */
    public function adminCreate(Request $request, $id)
    {
        $categories = Categories::all();
        $degrees = Degree::all();

        if ($request->expectsJson()) {
            return response()->json([
                'message'    => 'Provide job creation data for admin',
                'categories' => $categories,
                'degrees'    => $degrees,
                'org_id'     => $id,
            ], Response::HTTP_OK);
        }
        return view('pages.controlpanel.job.create', ['categories' => $categories, 'degrees' => $degrees, 'org_id' => $id]);
    }

    /**
     * Store a newly created resource for admin.
     */
    public function adminStore(Request $request)
    {
        $rules = [
            'job_title'                => 'nullable|string|max:255',
            'category_id'              => 'required',
            'degree_id'                => 'required',
            'description'              => 'nullable|string',
            'address'                  => 'nullable|string|max:255',
            'zipcode'                  => 'nullable|string|max:20',
            'status'                   => 'in:Active,Inactive',
            'is_remote'                => 'required',
            'skill'                    => 'nullable|string',
            'experience'               => 'nullable|string',
            'budget'                   => 'nullable|string',
            'bid_close'                => 'nullable|date',
            'deadline'                 => 'nullable|date',
            'career_page_url'          => 'nullable|url',
            'is_pinned_in_career_page' => 'nullable|boolean',
        ];

        $validatedData = $request->validate($rules);
        $validatedData['organization_id'] = $request->creator;
        $validatedData['user_id'] = $request->creator;

        Job::create($validatedData);

        if ($request->expectsJson()) {
             return response()->json([
                'success' => true,
                'message' => 'Job created successfully.'
            ], Response::HTTP_CREATED);
        }
        return redirect()->route('org-jobs', ['id' => $request->creator])
            ->with('success', 'Job created successfully.');
    }

    /**
     * Store a newly created resource.
     */
    public function store(Request $request)
    {
        if ($request->user_role == 'employee') {
            $org = Employee::where('user_id', $request->user['id'])->first();
            $creator = $org->creator_id;
        } elseif ($request->user_role == 'organization') {
            $org = Organization::where('user_id', $request->user['id'])->first();
            $creator = $org->user_id;
        } else {
            $creator = null;
        }

        $rules = [
            'job_title'                => 'nullable|string|max:255',
            'category_id'              => 'nullable',
            'degree_id'                => 'nullable',
            'description'              => 'nullable|string',
            'address'                  => 'nullable|string|max:255',
            'zipcode'                  => 'nullable|string|max:20',
            'status'                   => 'in:Active,Inactive',
            'is_remote'                => 'nullable',
            'skill'                    => 'nullable|string',
            'experience'               => 'nullable|string',
            'budget'                   => 'nullable|string',
            'bid_close'                => 'nullable|date',
            'deadline'                 => 'nullable|date',
            'career_page_url'          => 'nullable|url',
            'is_pinned_in_career_page' => 'nullable|boolean',
        ];

        $validatedData = $request->validate($rules);
        $validatedData['organization_id'] = $creator;
        $validatedData['user_id'] = $request->user['id'];

        Job::create($validatedData);

        if ($request->expectsJson()) {
            return response()->json(['response' => 'Job created successfully'], Response::HTTP_CREATED);
        }
        return redirect()->route('job.index')
            ->with('success', 'Job created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $this->hasPermission('job-view');
        $job = Job::where('jobs.id', $id)->first();
        $degrees = Degree::all();

        if ($request->expectsJson()) {
            return response()->json(['job' => $job], Response::HTTP_OK);
        }
        return view('pages.controlpanel.job.show', ['job' => $job, 'degrees' => $degrees]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id)
    {
        $this->hasPermission('job-edit');
        $job = Job::find($id);
        $category = Categories::where('id', $job->category_id)->first();
        $categories = Categories::where('id', '!=', $job->category_id)->get();
        $degree = Degree::where('id', $job->degree_id)->first();
        $degrees = Degree::where('id', '!=', $job->degree_id)->get();

        if ($request->expectsJson()) {
            return response()->json([
                'job'        => $job,
                'cat'        => $category,
                'categories' => $categories,
                'degree'     => $degree,
                'degrees'    => $degrees
            ], Response::HTTP_OK);
        }
        return view('pages.controlpanel.job.edit', [
            'job'        => $job,
            'cat'        => $category,
            'categories' => $categories,
            'degree'     => $degree,
            'degrees'    => $degrees
        ]);
    }

    /**
     * Show the form for editing the specified resource for admin.
     */
    public function adminEdit(Request $request, $job_id, $id)
    {
        $job = Job::find($job_id);
        $category = Categories::where('id', $job->category_id)->first();
        $categories = Categories::where('id', '!=', $job->category_id)->get();
        $degree = Degree::where('id', $job->degree_id)->first();
        $degrees = Degree::where('id', '!=', $job->degree_id)->get();

        if ($request->expectsJson()) {
            return response()->json([
                'job'        => $job,
                'category'   => $category,
                'categories' => $categories,
                'degree'     => $degree,
                'degrees'    => $degrees,
                'org_id'     => $id
            ], Response::HTTP_OK);
        }
        return view('pages.controlpanel.job.edit', [
            'job'        => $job,
            'cat'        => $category,
            'categories' => $categories,
            'degree'     => $degree,
            'degrees'    => $degrees,
            'org_id'     => $id
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $rules = [
            'job_title'                => 'nullable|string|max:255',
            'category_id'              => 'nullable',
            'degree_id'                => 'nullable',
            'description'              => 'nullable|string',
            'address'                  => 'nullable|string|max:255',
            'zipcode'                  => 'nullable',
            'status'                   => 'in:Active,Inactive',
            'is_remote'                => 'nullable',
            'skill'                    => 'nullable|string',
            'experience'               => 'nullable|string',
            'budget'                   => 'nullable|string',
            'bid_close'                => 'nullable|date',
            'deadline'                 => 'nullable|date',
            'career_page_url'          => 'nullable|url',
            'is_pinned_in_career_page' => 'nullable|boolean',
        ];

        try {
            $validatedData = $request->validate($rules);
            Job::findOrFail($id)->update($validatedData);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Job updated successfully.'
                ], Response::HTTP_OK);
            }
            return redirect()->route('job.index')
                ->with('success', 'Job updated successfully.');
        } catch (\Exception $e) {
            Log::error("Updating job failed: " . $e->getMessage());
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update job'
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            return redirect()->back()->withErrors(['message' => 'Failed to update job']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $this->hasPermission('job-delete');
        $job = Job::findOrFail($id);
        $job->delete();

        if ($request->expectsJson()) {
            return response()->json(['response' => 'Job deleted successfully'], Response::HTTP_OK);
        }
        return redirect()->route('job.index')->with('success', 'Job deleted successfully.');
    }

    /**
     * Remove the specified resource from storage for admin.
     */
    public function adminDestroy(Request $request, $job_id, $id)
    {
        $job = Job::findOrFail($job_id);
        $job->delete();

        if ($request->expectsJson()) {
            return response()->json(['response' => 'Job deleted successfully'], Response::HTTP_OK);
        }
        return redirect()->route('org-jobs', $id)->with('success', 'Job deleted successfully.');
    }

    /**
     * Check if the current user has the given permission.
     */
    private function hasPermission($permissionName)
    {
        if (!auth()->user()->hasPermissionTo($permissionName)) {
            abort(403);
        }
    }
}
