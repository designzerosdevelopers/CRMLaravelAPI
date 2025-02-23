@extends('layouts.guest')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

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
      
      <!-- Hoverable Table rows -->
      <div class="card mt-4 w-75 mx-auto">
        <h5 class="card-header">Applied jobs list</h5>
        <div class="table-responsive text-nowrap">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Project Title</th>
                <th>Budget</th>
                <th>Bid Closing</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody class="table-border-bottom-0">
             @foreach( $applied_jobs as $applied_job)
              <tr >
                <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>{{$applied_job->job_title}}</strong></td>
                <td>${{$applied_job->budget}}</td>
                <td>
                  <ul class="list-unstyled users-list m-0 avatar-group d-flex align-items-center">
                    {{$applied_job->bid_close}}
                  </ul>
                </td>
                <td>
                  <ul class="list-unstyled bg-success users-list m-0 avatar-group d-flex align-items-center">
                  <span class="badge {{ $applied_job->status === 'Selected' ? 'bg-label-success' : ($applied_job->status === 'Rejected' ? 'bg-label-danger' : 'bg-label-info') }}">
                  {{ $applied_job->status }}
              </span>
                  </ul>
                </td>
                <td>
                  <form method="POST" action="{{ route('applied_destroy', ['id' => $applied_job->form_id]) }}" class="list-unstyled users-list m-0 avatar-group d-flex align-items-center">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" style="padding: 0.05rem 0.5rem; font-size: 0.775rem;">
                        Delete
                    </button>
                  </form>
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