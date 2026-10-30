{{--
    Validation error summary.

    Displays the errors bag from the session and works on any page
    that renders a form, so validation feedback is consistent.
--}}

@if ($errors->any())
    <div class="alert alert-error" role="alert">
        <strong>Please fix the following {{ Str::plural('error', $errors->count()) }}:</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
