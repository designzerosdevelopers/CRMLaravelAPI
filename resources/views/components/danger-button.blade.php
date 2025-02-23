<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-danger deactivate-account']) }}>
    {{ $slot }}
</button>
