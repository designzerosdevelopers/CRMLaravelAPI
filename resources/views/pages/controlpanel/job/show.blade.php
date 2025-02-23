@extends('layouts.controlpanel')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
  <x-page-title menu='Job' page='View'/>
    <!-- Basic Layout & Basic with Icons -->
    <div class="row">
      <!-- Basic Layout -->
      <div class="col-xxl">
        <div class="card mb-4">
          <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">View Form</h5>
            <small class="text-muted float-end">View all the fields</small>
          </div>
          <div class="card-body">
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label" for="basic-default-name">Job Title</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" value="{{$job->job_title}}" id="basic-default-name" readonly/>
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label" for="basic-default-company">Address</label>
                <div class="col-sm-10">
                  <input
                    type="text"
                    class="form-control"
                    id="basic-default-company"
                    value="{{$job->address}}"
                    readonly
                  />
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label" for="basic-default-company">Zip Code</label>
                <div class="col-sm-4">
                  <input
                    type="number"
                    value="{{$job->zipcode}}"
                    class="form-control"
                    id="basic-default-company"
                    readonly
                  />
                </div>
                
                <div class="col-sm-2 offset-sm-1"> <!-- Adjusted column classes -->
                  <label class="col-form-label" for="basic-default-company">Job Type</label>
                </div>
                <div class="col-sm-3"> <!-- Adjusted column classes -->
                  <input
                    type="number"
                    value="{{$job->is_remote}}"
                    class="form-control"
                    id="basic-default-company"
                    readonly
                  />
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
                    value="{{$job->skill}}"
                    readonly
                  />
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label" for="basic-default-company">Required Experience</label>
                <div class="col-sm-10">
                  <input
                    type="text"
                    class="form-control"
                    name="experience"
                    id="basic-default-company"
                    value="{{$job->experience}}"
                    readonly
                  />
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label" for="basic-default-company">Required Education</label>
                <div class="col-sm-10">
                  <input
                    type="text"
                    class="form-control"
                    name="education"
                    id="basic-default-company"
                    value="{{$job->education}}"
                    readonly
                  />
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label" for="basic-default-company">Budget</label>
                <div class="col-sm-10">
                  <input
                    type="text"
                    class="form-control"
                    name="budget"
                    id="basic-default-company"
                    value="{{$job->budget}}"
                    readonly
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
                    value="{{$job->bid_close}}"
                    readonly
                  />
                </div>
                
                <label class="col-sm-2 col-form-label pe-2" for="basic-default-company">Deadline</label>
                <div class="col-sm-4">
                  <input
                    type="date"
                    name="deadline"
                    class="form-control"
                    id="basic-default-company"
                    value="{{$job->deadline}}"
                    readonly
                  />
                </div>
              </div>
                       
                <div class="row mb-3">
                  <label class="col-sm-2 col-form-label" for="basic-default-message">Description</label>
                  <div class="col-sm-10">
                    <textarea
                      id="basic-default-message"
                      class="form-control"
                      aria-label="Tell us about the project!"
                      aria-describedby="basic-icon-default-message2"
                    readonly>{{$job->description}}</textarea>
                  </div>
                </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- / Content -->

@stop