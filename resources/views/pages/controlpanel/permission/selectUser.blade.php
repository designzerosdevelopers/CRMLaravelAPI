@extends('layouts.controlpanel')

@section('content')
    <div class="container mt-4">
        <x-page-title menu='Permission' page='Select'/>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

    <!-- Basic Layout & Basic with Icons -->
    <div class="row">
        <!-- Basic Layout -->
        <div class="col-xxl">
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Permission Form</h5>
                    <small class="text-muted float-end">Select user to set permission</small>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('user-permission') }}">
                        @csrf
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="user_id">Select User:</label>
                                    <select class="form-select" name="user_id" id="user_id">
                                        <option disabled selected>-- select user --</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->user_id }}">{{ $user->email }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="user_email">Input User Email:</label>
                                    <input type='text' class="form-control" placeholder="Find Email to Set Permission" name="user_email" id="user_email">
                                </div>
                            </div> 
                        </div>     
                        <div class="row justify-content-end mt-3">
                            <div class="col">
                                <button type="submit" class="btn btn-primary">Select User</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


@endsection
