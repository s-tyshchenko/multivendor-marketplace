@php
    Theme::layout('full-width');
@endphp

<div class="page-content pt-150 pb-150">
    <div class="container">
        <div class="row">
            <div class="col-xl-8 col-lg-10 col-md-12 m-auto">
                <div class="row">
                    <h1>Confirm Your Email</h1>
                    <p>A confirmation has been sent to
                    {{ $email }}
                        Check your email, click the confirmation, and follow instructions to continue.</p>

                    <a href="{{ route('customer.resend_confirmation', $email) }}">Resend email</a>

                </div>
            </div>
        </div>
    </div>
</div>
