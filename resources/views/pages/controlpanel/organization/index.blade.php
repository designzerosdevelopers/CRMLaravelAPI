@extends('layouts.controlpanel')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <x-page-title menu='Organization' page='index'/>
     <a href="{{route('organization.create')}}"> <button type="button" class="btn rounded-pill btn-primary">Create</button></a>
    </div>
  
      <!-- Hoverable Table rows -->
      <div class="card">

        @if (session('success'))
          <div class="alert alert-success">
              {{ session('success') }}
          </div>
        @endif
        <h5 class="card-header">organizations list</h5>
        <div class="table-responsive text-nowrap">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Name</th>
                <th>Employees</th>
                <th>Jobs</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody class="table-border-bottom-0">
             @foreach( $organizations as $index=>$organization)
              <tr>
                <td><strong>{{$organization->organization_name}}</strong></td>
                <td>
                  <ul class="list-unstyled users-list m-0 avatar-group d-flex align-items-center">
                    <span class="badge bg-label-primary me-1"> {{$counts[$index]}} </span>
                  </ul>
                </td>
                <td>
                  <ul class="list-unstyled users-list m-0 avatar-group d-flex align-items-center">
                   <span class="badge bg-label-primary me-1">{{$jobcount[$index]}}</span>
                  </ul>
                </td>
                <td>
                  <div class="dropdown">
                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                      <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <div class="dropdown-menu">
                      <a class="dropdown-item" href="{{ route('organization.show', ['organization' => $organization->user_id]) }}"
                        ><i class="fas fa-file me-2"></i> View</a
                      >
                      <a class="dropdown-item" href="{{ route('org-employees', ['id' => $organization->user_id]) }}"
                        ><i class="fas fa-user me-2"></i> Employee Index</a
                      >
                      <a class="dropdown-item" href="{{ route('org-jobs', ['id' => $organization->user_id]) }}"
                        ><i class="fas fa-file-invoice me-2"></i> Job Index</a
                      >
                      <a class="dropdown-item" href="{{ route('organization.edit', ['organization' => $organization->user_id]) }}"
                        ><i class="fas fa-edit me-2"></i> Edit</a
                      >
                      <form method="POST" action="{{ route('organization.destroy', ['organization' => $organization->user_id]) }}" class="delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="dropdown-item">
                            <i class="fas fa-trash me-2"></i> Delete
                        </button>
                      </form>
                    
                    </div>
                  </div>
                </td>
              </tr>
             @endforeach
            </tbody>
          </table>
        </div>
      </div>
      <!--/ Hoverable Table rows -->
    </div>
  </div>
  <!-- / Content -->

@stop