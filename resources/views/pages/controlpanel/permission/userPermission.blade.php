@extends('layouts.controlpanel')

@section('content')
    <div class="container mt-4">
        <x-page-title menu='Permission' page='Edit'/>

        <div class="card">
            @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <div class="card-body">
                <h4>User and Permissions</h4>
                <form method="POST" action="{{ route('user-permission-set', ['id' => $user->id]) }}">
                    @csrf
                    @method('PUT') {{-- Change 'your.update.route' to your actual update route --}}
            
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Permissions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>{{ $user->name }}</strong></td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @foreach ($allpermission as $permission)
                                        <label>
                                            <input type="checkbox" name="user_permissions[]" value="{{ $permission->id }}" {{ in_array($permission->id, $permissions->pluck('id')->toArray()) ? 'checked' : '' }}>
                                            {{ $permission->name }}
                                        </label>
                                        <br>
                                    @endforeach
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="row justify-content-end mt-3">
                        <div class="col">
                            <button type="submit" class="btn btn-primary">Update Permissions</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        
@endsection
