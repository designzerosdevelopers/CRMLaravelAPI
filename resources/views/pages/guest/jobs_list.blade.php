@extends('layouts.guest')
@section('content')

@if(session('error'))
<div class="alert alert-danger">
    {{ session('error') }}
</div>
@endif
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

                        <!-- Content -->
                        <div class="container mt-4">
                            <div class="row">
                                @foreach ($jobs as $job)
                                <div class="col-md-8">
                                    <div class="card mb-4">
                                        <div class="card-body">
                                            <h5 class="card-title">Job Title: {{ $job->job_title }}</h5>
                                            <h6 class="card-subtitle mb-2 text-muted">Organization Name: {{ $job->organization_name }}</h6>
                                            <p class="card-text">Website: {{ $job->website }}</p>
                                            <p class="card-text">Skill Required: {{ $job->skill}}</p>
                                            <p class="card-text">Experince Required: {{ $job->experience}} Years</p>
                                            <p class="card-text">Posted: {{ $job->created_at->format('F d, Y') }}</p>
                                            <a href="{{ route('apply', ['job_id' => $job->id]) }}" class="btn-theme btn-two">Apply Now</a>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <!-- Content -->

    @stop
