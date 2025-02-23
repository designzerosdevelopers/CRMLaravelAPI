@extends('layouts.controlpanel')

@section('content')
    <div class="container mt-4">
        <x-page-title menu='Permission' page='Select'/>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

            <!-- Basic Layout & Basic with Icons -->
    <div class="row">
        <!-- Basic Layout -->
        <div class="col-md-6 col-sm-12">
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Permission Form</h5>
                    <small class="text-muted float-end">Select role to set permission</small>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('role-permission') }}">
                        @csrf
                        <div class="form-group">
                            <label for="role_id">Select Role:</label>
                            <select class="form-select" name="role_id" id="role_id">
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row justify-content-end mt-3">
                            <div class="col">
                              <button type="submit" class="btn btn-primary">Check Permissions</button>
                            </div>
                          </div>
                    </form>
                </div>
            </div>
          </div>
        </div>
      </div>

@endsection
