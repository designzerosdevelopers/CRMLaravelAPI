@extends('layouts.controlpanel')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
      @if(!empty($creator))
      <x-page-title menu='Organization Employee' page='index'/>
      <a href="{{route('org-employee-create', $creator)}}"> <button type="button" class="btn rounded-pill btn-primary">Create</button></a>
      @else
      <x-page-title menu='Employee' page='index'/>
      <a href="{{route('employee.create')}}"> <button type="button" class="btn rounded-pill btn-primary">Create</button></a>
      @endif

    </div>
  
      <!-- Hoverable Table rows -->
      <div class="card">
        @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif
        <h5 class="card-header">employees list</h5>
        <div class="table-responsive text-nowrap">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Name</th>
                <th>Email</th>
                <th>User id</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody class="table-border-bottom-0">
             @foreach( $employees as $employee)
             @php
             $user = $users->where('id', $employee->user_id)->first();
             @endphp
              <tr>
                <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>{{$user->name}}</strong></td>
                <td>{{$user->email}}</td>
                <td>
                  <ul class="list-unstyled users-list m-0 avatar-group d-flex align-items-center">
                    <span class="badge bg-label-primary me-1">{{$employee->user_id}}</span>
                  </ul>
                </td>
                <td>
                  <div class="dropdown">
                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                      <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <div class="dropdown-menu">
                      <a class="dropdown-item" href="{{ route('employee.show', ['employee' => $employee->user_id]) }}"
                        ><i class="fas fa-file me-2"></i> View</a>
                      
                      @if(!empty($creator))
                        <a class="dropdown-item" href="{{ route('org-employee-edit', ['emp_id' => $employee->user_id, 'id'=> $employee->creator_id]) }}"
                          ><i class="fas fa-edit me-2"></i> Edit</a>
                            
                        <form method="POST" action="{{ route('org-employee-destroy', ['emp_id' => $employee->user_id, 'id'=> $employee->creator_id]) }}" class="delete-form">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="dropdown-item">
                              <i class="fas fa-trash me-2"></i> Delete
                          </button>
                        </form>
                      @else
                        <a class="dropdown-item" href="{{ route('employee.edit', ['employee' => $employee->user_id]) }}"
                          ><i class="fas fa-edit me-2"></i> Edit</a>
                            
                        <form method="POST" action="{{ route('employee.destroy', ['employee' => $employee->user_id]) }}" class="delete-form">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="dropdown-item">
                              <i class="fas fa-trash me-2"></i> Delete
                          </button>
                        </form>
                      @endif
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