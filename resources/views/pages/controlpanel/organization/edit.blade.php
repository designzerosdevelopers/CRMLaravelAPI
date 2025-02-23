@extends('layouts.controlpanel')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <x-page-title menu='Organization' page='Edit'/>


    <!-- Basic Layout & Basic with Icons -->
    <div class="row">
      <!-- Basic Layout -->
      <div class="col-xxl">
        <div class="card mb-4">
          @if ($errors->any())
          <div class="alert alert-danger">
              <ul>
                  @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                  @endforeach
              </ul>
          </div>
          @endif
          <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">Edit Form</h5>
            <small class="text-muted float-end">Edit all the fields</small>
          </div>
          <div class="card-body">
            <form method="post" action="{{ route('organization.update', ['organization' => $org->user_id]) }}">
              @csrf
              @method('PUT')
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label" for="basic-default-name">Email</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" value="{{$org->email}}" name="email" id="basic-default-name" />
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label" for="basic-default-name">Organization Name</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" value="{{$org->organization_name}}" name="organization_name" id="basic-default-name"/>
                </div>
              </div>
              <div class="row mb-3">
                <label class="col-sm-2 col-form-label" for="basic-default-name">Website</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" value="{{$org->website}}" name="website" id="basic-default-name"/>
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
                    name="address"
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
                    >{{$org->description}}</textarea>
                  </div>
                </div>
                <div class="row justify-content-end">
                  <div class="col-sm-10">
                    <button type="submit" class="btn btn-primary">Update</button>
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