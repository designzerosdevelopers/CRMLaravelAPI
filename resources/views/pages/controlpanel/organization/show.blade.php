@extends('layouts.controlpanel')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <x-page-title menu='Organization' page='View'/>
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
                <label class="col-sm-2 col-form-label" for="basic-default-name">Email</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" value="{{$org->email}}" id="basic-default-name" readonly/>
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label" for="basic-default-name">Organization Name</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" value="{{$org->organization_name}}" id="basic-default-name" readonly/>
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label" for="basic-default-name">Website</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" value="{{$org->website}}" id="basic-default-name" readonly/>
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
                    value="{{$org->address}}"
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
                    >{{$org->description}}</textarea>
                  </div>
                </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- / Content -->
@stop