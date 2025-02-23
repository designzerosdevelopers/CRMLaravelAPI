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


<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-white text-center">
                    <h4 class="mb-0">Job Details</h4>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><strong>Title:</strong> {{$job->job_title}}</li>
                    <li class="list-group-item"><strong>Address:</strong> {{$job->address}}, {{$job->zipcode}}</li>
                    <li class="list-group-item"><strong>Status:</strong> {{$job->status}}</li>
                    <li class="list-group-item"><strong>Type:</strong> {{$job->is_remote}}</li>
                    <li class="list-group-item"><strong>Skill:</strong> {{$job->skill}}</li>
                    <li class="list-group-item"><strong>Experience:</strong> {{$job->experience}}</li>
                    <li class="list-group-item"><strong>Budget:</strong> {{$job->budget}}</li>
                    <li class="list-group-item"><strong>Bid Close:</strong> {{$job->bid_close}}</li>
                    <li class="list-group-item"><strong>Deadline:</strong> {{$job->deadline}}</li>
                    <li class="list-group-item"><strong>Created At:</strong> {{$job->created_at}}</li>
                </ul>
            </div>
        </div>
    </div>


    @auth
        @role('candidate')
        <div class="row justify-content-center my-5">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h4 class="mb-2">Apply Form </h4>
                        <p class="mb-4">Write a short description that, what do you understan about project. </p>
                        <form method="POST" action="{{ route('user_apply',['job_id'=> $job->id]) }}" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="cv" class="form-label">{{ __('CV')}}</label>
                                <input
                                    type="file"
                                    class="form-control"
                                    id="cv"
                                    name="cv"
                                    accept=".pdf"
                                />
                                <x-input-error :messages="$errors->get('cv')" class="mt-2" />
                            </div>
                                @if (!empty($cv))
                                    <div class="mb-3">
                                        <label for="use_existing_cv" class="form-label">{{ __('Use existing CV')}}</label>
                                        <input
                                            type="checkbox"
                                            id="use_existing_cv"
                                            name="use_old_cv"
                                            value="{{$cv}}"
                                        />
                                    </div>
                                @endif  
                            <div class="mb-3">
                                <label for="name" class="form-label">{{ __('Description')}}</label>
                                <textarea
                                type="text"
                                class="form-control"
                                id="description"
                                name="description"
                                placeholder="Keep it under 100 words"
                                required 
                                />{{Old('description')}}</textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>             
                            <div class="mb-3">
                                <button class="btn-theme btn-two d-grid w-100" type="submit">{{ __('Submit') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
                
            </div>
        </div>

        @endrole
    @else
        <div class="row justify-content-center my-5">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h4 class="mb-2">Apply Form</h4>
                        <p class="mb-4">Registering on the portal would  increase 70-80%  more chance to get hired quickly.  </p>
                        <form method="POST" action="{{ route('guest_apply',['job_id'=> $job->id]) }}" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label">{{ __('Name')}}</label>
                                <input
                                type="text"
                                class="form-control"
                                id="name"
                                name="name"
                                placeholder="Enter your name"
                                value="{{Old('name')}}"
                                required 
                                />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">{{ __('Email')}}</label>
                                <input
                                type="email"
                                class="form-control"
                                id="email"
                                name="email"
                                placeholder="Enter your Email"
                                value="{{Old('email')}}"
                                required 
                                />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                            <div class="mb-3">
                                <label for="position" class="form-label">{{ __('Position')}}</label>
                                <input
                                type="text"
                                class="form-control"
                                id="position"
                                name="position"
                                placeholder="Enter your Position"
                                value="{{Old('position')}}"
                                required 
                                />
                                <x-input-error :messages="$errors->get('position')" class="mt-2" />
                            </div>
                            <div class="mb-3">
                                <label for="cv" class="form-label">{{ __('CV')}}</label>
                                <input
                                    type="file"
                                    class="form-control"
                                    id="cv"
                                    name="cv"
                                    accept=".pdf"
                                />
                                <x-input-error :messages="$errors->get('cv')" class="mt-2" />
                            </div>
                                @if (!empty($cv))
                                    <div class="mb-3">
                                        <label for="use_existing_cv" class="form-label">{{ __('Use Old CV')}}</label>
                                        <input
                                            type="checkbox"
                                            id="use_existing_cv"
                                            name="use_old_cv"
                                            value="{{$cv}}"
                                        />
                                    </div>
                                @endif  
                                <div class="mb-3">
                                    <label for="name" class="form-label">{{ __('Description')}}</label>
                                    <textarea
                                    type="text"
                                    class="form-control"
                                    id="description"
                                    name="description"
                                    placeholder="Keep it under 100 words"
                                    required 
                                    />{{Old('description')}}</textarea>
                                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                </div>               
                            <div class="mb-3">
                                <button class="btn-theme btn-two d-grid w-100" type="submit">{{ __('Submit') }}</button>
                            </div>
                        </form>

                        <p class="text-center">
                            <span>Have account?</span>
                        <a href="{{route('user-login')}}">
                            <span>Login Here</span>
                        </a>
                        <span> Or</span>
                        <a href="{{route('user-register')}}">
                            <span>Create an account</span>
                        </a>
                        </p>
                        
                    </div>
                </div>
                
            </div>
        </div>
    @endauth
   

    @stop
