<?php

namespace App\Http\Controllers;

use Illuminate\Support\Collection;
use App\Models\Degree;
use App\Models\Job;
use App\Models\Application_form;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        $job =  Job::where('id', $id)->first();

        $applications1 = Application_form::join('candidates', 'application_form.user_id', '=', 'candidates.user_id')
            ->join('users', 'candidates.user_id', '=', 'users.id')
            ->where('application_form.job_id', $id)
            ->select('application_form.*', 'users.name', 'users.email', 'candidates.skill', 'candidates.experience', 'candidates.degree_id', 'candidates.experience', 'candidates.profession')
            ->get();

        foreach ($applications1 as $application) {

            $skillScore = $this->stringmatch($job->skill, $application->skill);
            $educationScore = ($application->degree_id == $job->degree_id) ? 3 : 0;
            $experienceScore = ($job->experience > 0) ? ($application->experience / $job->experience) * 5 : 0;
            $application->match_score = $skillScore + $educationScore + $experienceScore;
            $application->match_score = number_format($application->match_score, 2);
        }

        $sortedApplications = $applications1->sortByDesc('match_score');

        $applications2 = Application_form::where('user_id', NULL)->get();
        $mergedApplications = $sortedApplications->concat($applications2);

        return view('pages.controlpanel.candidate.index', ['applications' => $mergedApplications]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function view($form_id)
    {

        // Run the first query when user_id is not NULL
        $user = Application_form::join('candidates', 'application_form.user_id', '=', 'candidates.user_id')
            ->join('users', 'candidates.user_id', '=', 'users.id')
            ->where('application_form.id', $form_id)
            ->select(
                'application_form.*',
                'candidates.resume',
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
                'users.email',
            )->first();

        $degree = Degree::where('id', $user->degree_id)->pluck('degree_title')->first();
        $user->degree = $degree;
        if (is_null($user)) {
            $user = Application_form::where('application_form.id', $form_id)
                ->first();
        }


        return view('pages.controlpanel.candidate.view', ['user' => $user]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function select(Request $request, string $id)
    {
        $job_id = $request->job_id;
        Application_form::where('id', $id)
            ->update([
                'status' => $request->status,
            ]);


        return redirect()->route('applier_candidates', ['id' => $job_id]);
    }


    public function destroy(string $id)
    {
        //
    }

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

        // Count the total number of words
        $totalWordsCount = count($jobWords);

        // Calculate the percentage of matching words
        $percentageMatch = ($totalWordsCount > 0) ? ($matchingWordsCount / $totalWordsCount) * 10 : 0;

        return $percentageMatch;
    }
}
