<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Application_form;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Jobs\PdfLabeler;
use App\Models\Candidate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class JobFrontController extends Controller
{
    /**
     * Display the front job list.
     */
    public function FrontJobList(Request $request)
    {
        Log::info('Fetching jobs');

        // Fetch jobs with organization info using a left join and eager loading
        $jobs = Job::leftJoin('organizations', 'jobs.organization_id', '=', 'organizations.user_id')
            ->select('jobs.*', 'organizations.organization_name', 'organizations.website')
            ->with('organization')
            ->get();

        Log::info('Jobs fetched', ['jobs' => $jobs]);

        if ($request->expectsJson()) {
            return response()->json(['jobs' => $jobs], 200);
        }

        return view('pages.guest.jobs_list', ['jobs' => $jobs]);
    }

    /**
     * Show the details for applying to a job.
     */
    public function apply(Request $request, $id)
    {
        $job = Job::findOrFail($id);

        if ($request->expectsJson()) {
            return response()->json(['job' => $job], 200);
        }

        // For guest users or users without the candidate role
        if (!Auth::check() || !auth()->user()->hasRole('candidate')) {
            $existing = Application_form::where('user_ip', $request->ip())->first();
            if (!empty($existing->user_ip)) {
                return view('pages.guest.job_detail', ['cv' => $existing->cv, 'job' => $job]);
            }
            return view('pages.guest.job_detail', ['job' => $job]);
        } else {
            // For logged-in candidate users
            $existing = Candidate::where('user_id', Auth()->user()->id)->first();
            return view('pages.guest.job_detail', ['cv' => $existing->cv, 'job' => $job]);
        }
    }

    /**
     * Handle application submission for a logged-in candidate.
     */
    public function user_apply(Request $request, $id)
    {
        $request->validate([
            'description' => 'required|max:1000',
            'cv' => $request->input('use_old_cv') ? [] : 'required|mimes:pdf|max:1024',
        ]);

        // Check if the user has already applied
        $existingApplication = Application_form::where('user_id', auth()->user()->id)
            ->where('job_id', $id)
            ->first();

        if ($existingApplication) {
            $errorMessage = 'You have already applied for this job.';
            if ($request->expectsJson()) {
                return response()->json(['error' => $errorMessage], 422);
            }
            return redirect()->back()->with('error', $errorMessage);
        }

        if (!$request->input('use_old_cv')) {
            $cvFile = $request->file('cv');
            $destinationPath = public_path('cv');
            $destinationFileName = time() . '_' . $cvFile->getClientOriginalName();
            $cvFile->move($destinationPath, $destinationFileName);
            $pathname = $destinationPath . DIRECTORY_SEPARATOR . $destinationFileName;

            // Dispatch the job to label the PDF
            PdfLabeler::dispatch($pathname, Auth()->user());

            // Update candidate record with the new CV path
            Candidate::where('user_id', Auth()->user()->id)
                ->update(['cv' => $pathname]);
        } else {
            $pathname = $request->input('use_old_cv');
        }

        // Create the application
        Application_form::create([
            'user_id'       => auth()->user()->id,
            'job_id'        => $id,
            'description'   => $request->description,
            'is_registered' => 1,
            'cv'            => $pathname,
            'status'        => 'In Process',
        ]);

        $successMessage = 'Application submitted successfully.';
        if ($request->expectsJson()) {
            return response()->json(['success' => $successMessage], 201);
        }
        return redirect()->route('frontjoblist')->with('success', $successMessage);
    }

    /**
     * Handle application submission for a guest (non-registered user).
     */
    public function guest_apply(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'position'    => 'required|string|max:255',
            'email'       => 'required|email|max:255',
            'description' => 'required|max:1000',
            'cv'          => $request->input('use_old_cv') ? [] : 'required|mimes:pdf',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Check if an application already exists for this email and job
        $existingApplication = Application_form::where('job_id', $id)
            ->where('email', $request->input('email'))
            ->first();

        if ($existingApplication) {
            $errorMessage = 'You have already applied for this job with the same email.';
            if ($request->expectsJson()) {
                return response()->json(['error' => $errorMessage], 422);
            }
            return redirect()->back()->with('error', $errorMessage);
        }

        $user = auth()->user();

        if (!$request->input('use_old_cv')) {
            $cvFile = $request->file('cv');
            $destinationPath = public_path('cv');
            $destinationFileName = time() . '_' . $cvFile->getClientOriginalName();
            $cvFile->move($destinationPath, $destinationFileName);
            $pathname = $destinationPath . DIRECTORY_SEPARATOR . $destinationFileName;

            PdfLabeler::dispatch($pathname);

            $candidateData = ['cv' => $pathname];
            if ($user) {
                $candidateData['user_id'] = $user->id;
            }
            Candidate::create($candidateData);
        } else {
            $pathname = $request->input('use_old_cv');
        }

        Application_form::create([
            'job_id'        => $id,
            'name'          => $request->input('name'),
            'position'      => $request->input('position'),
            'email'         => $request->input('email'),
            'description'   => $request->input('description'),
            'status'        => 'In Process',
            'is_registered' => 0,
            'cv'            => $pathname,
            'user_ip'       => $request->ip(),
        ]);

        $responseMessage = 'Application submitted successfully.';
        if ($request->expectsJson()) {
            return response()->json(['success' => $responseMessage], 201);
        }
        return redirect()->route('frontjoblist')->with('success', $responseMessage);
    }

    /**
     * Display the list of jobs the user has applied to.
     */
    public function view_applied(Request $request)
    {
        $user = auth()->user();
        $jobIds = $user->application_form->pluck('job_id')->all();

        $applied_jobs = Job::whereIn('jobs.id', $jobIds)
            ->join('application_form', 'jobs.id', '=', 'application_form.job_id')
            ->select('jobs.*', 'application_form.id as form_id', 'application_form.status')
            ->where('application_form.user_id', $user->id)
            ->get();

        if ($request->expectsJson()) {
            return response()->json(['applied_jobs' => $applied_jobs], 200);
        }
        return view('pages.guest.applied_jobs', ['applied_jobs' => $applied_jobs]);
    }

    /**
     * Delete an application.
     */
    public function applied_distroy(Request $request, $id)
    {
        $applied = Application_form::findOrFail($id);
        $applied->delete();

        $successMessage = 'Application deleted successfully.';
        if ($request->expectsJson()) {
            return response()->json(['success' => $successMessage], 200);
        }
        return redirect()->route('view-applied')->with('success', $successMessage);
    }
}
