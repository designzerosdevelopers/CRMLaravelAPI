@extends('layouts.controlpanel')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
  <x-page-title menu='Category' page='Edit'/>

  <div class="row">
    <div class="col-md-12">
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
        <h5 class="card-header">Edit Category</h5>
        <!-- Account -->
        <hr class="my-0" />
        <div class="card-body">
          <form method="post" action="{{ route('categories.update', ['category' => $cat->id]) }}">
            @csrf
            @method('PUT')
            <div class="row">
              <div class="mb-3 col-md-6">
                <label for="firstName" class="form-label">Name</label>
                <input
                  class="form-control"
                  type="text"
                  id="firstName"
                  name="cat_name"
                  value="{{$cat->cat_name}}"
                  autofocus
                />
              </div>
            
          </div>
            <div class="mt-2">
              <button type="submit" class="btn btn-primary me-2">Save changes</button>
            </div>
          </form>
        </div>
        <!-- /Account -->
      </div>
    </div>
  </div>
</div>
<!-- / Content -->

@stop