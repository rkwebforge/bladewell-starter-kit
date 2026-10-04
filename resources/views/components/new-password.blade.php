@props([
    // What it submits as.
    'name' => 'password',
    // Shown above the field.
    'label' => 'Password',
    // Which error bag to read the error from.
    'bag' => 'default',
])

{{-- A password field for choosing a new password. Its checklist shows the rules from config/security.php, the same
     ones Password::defaults() checks on the server. --}}
<x-widget.password
    :name="$name"
    :label="$label"
    :bag="$bag"
    new
    :min="config('security.passwords.min')"
    :mixed-case="config('security.passwords.mixed_case')"
    :numbers="config('security.passwords.numbers')"
    :symbols="config('security.passwords.symbols')"
    {{ $attributes }}
/>
