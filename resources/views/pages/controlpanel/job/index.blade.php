@extends('layouts.controlpanel')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <x-page-title menu='Job' page='Index'/>
      @if(!empty($creator))
      <a href="{{route('org-job-create', $creator)}}"> <button type="button" class="btn rounded-pill btn-primary">Create</button></a>
      @else
      @can('job-create') <a href="{{route('job.create')}}"> <button type="button" class="btn rounded-pill btn-primary">Create</button></a>@endcan
      @endif

    </div>
      <!-- Hoverable Table rows -->
      <div class="card">
        @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif
        <h5 class="card-header">jobs list</h5>
        <div class="table-responsive text-nowrap">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Project Title</th>
                <th>Budget</th>
                <th>Bid Closing</th>
                <th>Candidates</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody class="table-border-bottom-0">
             @foreach( $jobs as $job)
              <tr >
                <td> <strong>{{$job->job_title}}</strong></td>
                <td>${{$job->budget}}</td>
                <td>
                  <ul class="list-unstyled users-list m-0 avatar-group d-flex align-items-center">
                    {{$job->bid_close}}
                  </ul>
                </td>
                <td><span class="badge bg-label-primary me-1">{{$job->application_form_count}}</span></td>
                <td>
                  <div class="dropdown">
                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                      <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <div class="dropdown-menu">
                      <a class="dropdown-item"  href="{{ route('applier_candidates', ['id' => $job->id]) }}"
                        ><i class="fas fa-user me-2"></i> Candidates</a
                      >
                      @if(!empty($creator))
                        <a class="dropdown-item"  href="{{ route('job.show', ['job' => $job->id]) }}"
                          ><i class="fas fa-file me-2"></i> View</a
                        >
                        <a class="dropdown-item" href="{{ route('org-job-edit', ['job_id' => $job->id, 'id'=> $job->organization_id]) }}">
                          <i class="fas fa-edit me-2"></i> Edit
                      </a>
                      <form method="POST" action="{{ route('org-job-destroy', ['job_id' => $job->id, 'id'=> $job->organization_id]) }}" class="delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="dropdown-item">
                            <i class="fas fa-trash me-2"></i> Delete
                        </button>
                      </form>
                      @else
                        @can('job-view')
                          <a class="dropdown-item"  href="{{ route('job.show', ['job' => $job->id]) }}"
                            ><i class="fas fa-file me-2"></i> View</a
                          >
                        @endcan
                        @can('job-edit')
                          <a class="dropdown-item" href="{{ route('job.edit', ['job' => $job->id]) }}">
                              <i class="fas fa-edit me-2"></i> Edit
                          </a>
                        @endcan
                        @can('job-delete')
                            <form method="POST" action="{{ route('job.destroy', ['job' => $job->id]) }}" class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-trash me-2"></i> Delete
                                </button>
                            </form>
                        @endcan
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