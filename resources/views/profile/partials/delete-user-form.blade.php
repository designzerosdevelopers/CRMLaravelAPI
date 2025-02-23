<section >
    <h5 class="card-header">Delete Account</h5>

    <div class="card-body">
        <div class="mb-3 col-12">
            <div class="alert alert-warning">
              <h6 class="alert-heading fw-bold mb-1">Are you sure you want to delete your account?</h6>
              <p class="mb-0">Once you delete your account, there is no going back. Please be certain.</p>
            </div>
          </div>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="form-control"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div >

                <x-danger-button class="ml-3 mt-2">
                    {{ __('Delete Account') }}
                </x-danger-button>
            </div>
        </form>

    </div>

</section>
