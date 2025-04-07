<?php

namespace App\Http\Controllers;

use Illuminate\Support\Collection;
use App\Models\Degree;
use App\Models\Job;
use App\Models\Organization;
use App\Models\Application_form;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
class ApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $id)
    {
        Log::info('Received job ID', ['id' => $id]);

        $job = Job::find($id);
        $applied = Application_form::where('job_id', $id)->first();

        Log::info('Job details', ['job' => $job]);
        Log::info('First application for job', ['application' => $applied]);

        if (!$job) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Job not found'
                ], Response::HTTP_NOT_FOUND);
            }
            return redirect()->back()->withErrors('Job not found');
        }

        // Retrieve applications with registered users
        $applications1 = Application_form::join('candidates', 'application_form.user_id', '=', 'candidates.user_id')
            ->join('users', 'candidates.user_id', '=', 'users.id')
            ->where('application_form.job_id', $id)
            ->select(
                'application_form.*',
                'users.name',
                'users.email',
                'candidates.skill',
                'candidates.experience',
                'candidates.degree_id',
                'candidates.profession'
            )
            ->get();

        Log::info('Applications for job (registered users)', ['applications' => $applications1]);

        // Calculate match score for each application
        foreach ($applications1 as $application) {
            $skillScore      = $this->stringmatch($job->skill, $application->skill);
            $educationScore  = ($application->degree_id == $job->degree_id) ? 3 : 0;
            $experienceScore = ($job->experience > 0) ? ($application->experience / $job->experience) * 5 : 0;
            $application->match_score = number_format($skillScore + $educationScore + $experienceScore, 2);
        }

        // Sort applications by match score
        $sortedApplications = $applications1->sortByDesc('match_score');

        // Retrieve applications with no registered user (guest applications)
        $applications2 = Application_form::whereNull('user_id')
            ->where('job_id', $id)
            ->select('application_form.*') // Add additional fields if stored in application_form (e.g., name, email)
            ->get();

        // Merge both collections
        $mergedApplications = $sortedApplications->concat($applications2);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data'    => $mergedApplications
            ], Response::HTTP_OK);
        }

        return view('pages.controlpanel.candidate.index', ['applications' => $mergedApplications]);
    }




    public function indexForAdmin(Request $request, $id)
{
    // Get the organization by its user id
    $org = Organization::where('user_id', $id)->first();

    // Get jobs for the organization including the two counts
    $jobs = Job::where('organization_id', $org->user_id)
        ->withCount([
            // Count registered applications with join conditions
            'application_form as registered_count' => function ($query) {
                $query->join('candidates', 'application_form.user_id', '=', 'candidates.user_id')
                      ->join('users', 'candidates.user_id', '=', 'users.id');
            },
            // Count unregistered applications
            'application_form as unregistered_count' => function ($query) {
                $query->whereNull('user_id');
            }
        ])
        ->get();

    if ($request->expectsJson()) {
        return response()->json([
            'jobs'    => $jobs,
            'creator' => $id
        ], Response::HTTP_OK);
    }

    return view('pages.controlpanel.job.index', [
        'jobs'    => $jobs,
        'creator' => $id
    ]);
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Not implemented (or add logic if needed)
    }

    /**
     * Display detailed information for a given application.
     */
    public function view(Request $request, $form_id)
    {
        // Attempt to retrieve application details with candidate and user data
        $user = Application_form::join('candidates', 'application_form.user_id', '=', 'candidates.user_id')
            ->join('users', 'candidates.user_id', '=', 'users.id')
            ->where('application_form.id', $form_id)
            ->select(
                'application_form.*',
                'candidates.cv',
                'candidates.phone_number',
                'candidates.gender',
                'candidates.birth_date',
                'candidates.address',
                'candidates.zipcode',
                'candidates.degree_id',
                'candidates.latest_university',
                'candidates.current_organization',
                'candidates.current_department',
                'candidates.current_position',
                'candidates.description',
                'candidates.skill',
                'users.name',
                'users.email'
            )->first();

        if (!$user) {
            // Fallback if no joined record is found
            $user = Application_form::where('application_form.id', $form_id)->first();
        } else {
            // If found, attach the degree title
            $degree = Degree::where('id', $user->degree_id)->pluck('degree_title')->first();
            $user->degree = $degree;
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data'    => $user
            ], Response::HTTP_OK);
        }
        return view('pages.controlpanel.candidate.view', ['user' => $user]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Not implemented (or add logic if needed)
    }

    /**
     * Update the specified resource in storage (e.g. update application status).
     */
    public function select(Request $request, string $id)
    {
        $job_id = $request->job_id;
        Application_form::where('id', $id)
            ->update([
                'status' => $request->status,
            ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Application status updated successfully.'
            ], Response::HTTP_OK);
        }
        return redirect()->route('applier_candidates', ['id' => $job_id])
            ->with('success', 'Application status updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Not implemented (or add deletion logic if needed)
    }

    /**
     * A helper function to calculate matching score based on string similarity.
     */
    private function stringmatch($jobData, $candidateData)
    {
        // Extract words from job and candidate data
        preg_match_all('/\b\w+\b/', strtolower($jobData), $jobWords);
        preg_match_all('/\b\w+\b/', strtolower($candidateData), $candidateWords);

        // Flatten the arrays of words
        $jobWords = $jobWords[0];
        $candidateWords = $candidateWords[0];

        // Count the number of matching words
        $matchingWordsCount = count(array_intersect($jobWords, $candidateWords));

        // Count the total number of words in the job data
        $totalWordsCount = count($jobWords);

        // Calculate the percentage of matching words scaled to 10
        $percentageMatch = ($totalWordsCount > 0) ? ($matchingWordsCount / $totalWordsCount) * 10 : 0;

        return $percentageMatch;
    }
}
