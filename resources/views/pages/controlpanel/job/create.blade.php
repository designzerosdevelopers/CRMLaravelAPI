@extends('layouts.controlpanel')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
  <x-page-title menu='Job' page='Create'/>    
      @if ($errors->any())
      <div class="alert alert-danger">
          <ul>
              @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
              @endforeach
          </ul>
      </div>
    @endif
    <!-- Basic Layout & Basic with Icons -->
    <div class="row">
      <!-- Basic Layout -->
      <div class="col-xxl">
        <div class="card mb-4">
          <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">Create Form</h5>
            <small class="text-muted float-end">Fill all the fields</small>
          </div>
          <div class="card-body">
            @if(!empty($org_id))
            <form method="post" action="{{route('org-job-store')}}">
            @else
            <form method="post" action="{{route('job.store')}}">
            @endif
              @csrf
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label" for="basic-default-name">Job Title</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" value="Learning Menagment System (LMS)" name="job_title" id="basic-default-name" />
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label" for="basic-default-company">Address</label>
                <div class="col-sm-10">
                  <input
                    type="text"
                    name="address"
                    class="form-control"
                    id="basic-default-company"
                    value="TRC Enterprice,Avanue Road, 3rd Block, Lahore"
                  />
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label" for="basic-default-company">Job category</label>
                <div class="col-sm-4">
                  <select name="category_id" id="category"  class="form-select">
                    <option disabled selected> -- Select an option -- </option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->cat_name }}</option>
                    @endforeach
                </select>
                </div>
                <label class="col-sm-2 col-form-label" for="basic-default-company">Degree</label>
                <div class="col-sm-4">
                  <select name="degree_id" id="degree" class="form-select">
                    <option disabled selected> -- Select an option -- </option>
                    @foreach($degrees as $degree)
                        <option value="{{ $degree->id }}">{{ $degree->degree_title }}</option>
                    @endforeach
                </select>
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label" for="basic-default-company">Zip Code</label>
                <div class="col-sm-4">
                  <input
                    type="number"
                    value="98766"
                    name='zipcode'
                    class="form-control"
                    id="basic-default-company"
                  />
                </div>
                
                <div class="col-sm-2"> <!-- Adjusted column classes -->
                  <label class="col-form-label" for="basic-default-company">Is Remote</label>
                </div>
                <div class="col-sm-4"> <!-- Adjusted column classes -->
                  <select name="is_remote" class="form-select" id="basic-default-company">
                    <option disabled selected> -- Select an option -- </option>
                    <option value="Remote">Remote</option>
                    <option value="On-site">On-site</option>
                    <option value="Hybrid">Hybrid</option>
                  </select>
                </div>
              </div>
              
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label" for="basic-default-company">Required Skill</label>
                <div class="col-sm-10">
                  <input
                    type="text"
                    class="form-control"
                    name="skill"
                    id="basic-default-company"
                    value="HTML, CSS, JS, Vue, Laravel, Php"
                  />
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label" for="basic-default-company">Experience Years</label>
                <div class="col-sm-4">
                  <input
                    type="number"
                    class="form-control"
                    name="experience"
                    id="basic-default-company"
                    value="5"
                  />
                </div>
                <label class="col-sm-2 col-form-label" for="basic-default-company">Budget</label>
                <div class="col-sm-4">
                  <input
                    type="text"
                    class="form-control"
                    name="budget"
                    id="basic-default-company"
                    value="8743"
                  />
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label" for="basic-default-company">Bid Closing</label>
                <div class="col-sm-4">
                  <input
                    type="date"
                    name="bid_close"
                    class="form-control"
                    id="basic-default-company"
                    value="2023-03-09"
                  />
                </div>
                
                <label class="col-sm-2 col-form-label pe-2" for="basic-default-company">Deadline</label>
                <div class="col-sm-4">
                  <input
                    type="date"
                    name="deadline"
                    class="form-control"
                    id="basic-default-company"
                    value="2023-03-09"
                  />
                </div>
              </div>
                       
                <div class="row mb-3">
                  <label class="col-sm-2 col-form-label" for="basic-default-message">Description</label>
                  <div class="col-sm-10">
                    <textarea
                      id="basic-default-message"
                      class="form-control"
                      name="description"
                      placeholder="Tell us about the project!"
                      aria-label="Tell us about the project!"
                      aria-describedby="basic-icon-default-message2"
                    >The Learning System Application is For a University.</textarea>
                  </div>
                </div>
                @if(!empty($org_id))
                <input type="hidden" name="creator" value="{{$org_id}}">
                @endif
                <div class="row justify-content-end">
                  <div class="col-sm-10">
                    <button type="submit" class="btn btn-primary">Send</button>
                  </div>
                </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- / Content -->

@stop