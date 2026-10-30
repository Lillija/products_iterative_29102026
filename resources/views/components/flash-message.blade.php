{{--
    Flash message for successful actions.

    Displays the 'status' flash session key set by the product
    controller after successful create, update and delete actions.
--}}

@if (session('status'))
    <div class="alert alert-success" role="status" data-auto-dismiss>
        {{ session('status') }}
    </div>
@endif
