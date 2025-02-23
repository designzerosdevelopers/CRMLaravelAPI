@extends('layouts.controlpanel')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <x-page-title menu='Candidate' page='View'/>
               
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
            <h5 class="mb-0">Detail Form</h5>
            <small class="text-muted float-end">View all the fields</small>
          </div>
          <div class="card-body">
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label" for="basic-default-name">Name</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" value="{{$user->name}}" id="basic-default-name" readonly/>
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label" for="basic-default-name">Email</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" value="{{$user->email}}" id="basic-default-name" readonly/>
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label" for="basic-default-name">Phone Number</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" value="{{$user->phone_number}}" id="basic-default-name" readonly/>
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label" for="basic-default-name">Gender</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" value="{{$user->gender}}" id="basic-default-name" readonly/>
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label" for="basic-default-name">Birth Date</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" value="{{$user->birth_date}}" id="basic-default-name" readonly/>
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
                    value="{{$user->address}}"
                    readonly
                  />
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label" for="basic-default-company">Zip Code</label>
                <div class="col-sm-10">
                  <input
                    type="number"
                    value="{{$user->zipcode}}"
                    name='zipcode'
                    class="form-control"
                    id="basic-default-company"
                    readonly
                  />
                </div>
              </div>
              
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label" for="basic-default-company">Skill</label>
                <div class="col-sm-10">
                  <input
                    type="text"
                    class="form-control"
                    name="skill"
                    id="basic-default-company"
                    value="{{$user->skill}}"
                    readonly
                  />
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label" for="basic-default-company">Latest Degree</label>
                <div class="col-sm-10">
                  <input
                    type="text"
                    class="form-control"
                    name="experience"
                    id="basic-default-company"
                    value="{{$user->degree}}"
                    readonly
                  />
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label" for="basic-default-company">Lastest University</label>
                <div class="col-sm-10">
                  <input
                    type="text"
                    class="form-control"
                    name="education"
                    id="basic-default-company"
                    value="{{$user->latest_university}}"
                    readonly
                  />
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label" for="basic-default-company">Current Organization</label>
                <div class="col-sm-10">
                  <input
                    type="text"
                    class="form-control"
                    name="education"
                    id="basic-default-company"
                    value="{{$user->current_organization}}"
                    readonly
                  />
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label" for="basic-default-company">Current Department</label>
                <div class="col-sm-10">
                  <input
                    type="text"
                    class="form-control"
                    name="education"
                    id="basic-default-company"
                    value="{{$user->current_department}}"
                    readonly
                  />
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label" for="basic-default-company">Current Position</label>
                <div class="col-sm-10">
                  <input
                    type="text"
                    class="form-control"
                    name="education"
                    id="basic-default-company"
                    value="{{$user->current_position}}"
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
                      name="description"
                      placeholder="Tell us about the project!"
                      aria-label="Tell us about the project!"
                      aria-describedby="basic-icon-default-message2"
                      readonly
                    >{{$user->description}}</textarea>
                  </div>
                </div>
                <div class="row justify-content-end">
                  <div class="col-sm-10">
                    <form method="post" action="{{route('select_candidate',['id'=>$user->id])}}">
                      @csrf
                      @method('PUT')
                      <input type='hidden' name="job_id" value="{{$user->job_id}}">
                    <button type="submit" name='status' value="Selected" class="btn btn-primary">Select</button>
                    <button type="submit" name='status' value="Rejected" class="btn btn-danger">Reject</button>
                    </form>
                  </div>
                </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- / Content -->
@stop