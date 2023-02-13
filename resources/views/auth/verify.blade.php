@extends('layouts.parent')

@section('auth')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('auth.verify_email') }}</div>

                <div class="card-body">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            {{ __('auth.email_send') }}
                        </div>
                    @endif

                    {{ __('auth.before_confirm') }}
                    {{ __('auth.not_receive') }},
                    <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                        @csrf
                        <button type="submit" class="btn btn-link p-0 m-0 align-baseline">{{ __('auth.another_request') }}</button>.
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
