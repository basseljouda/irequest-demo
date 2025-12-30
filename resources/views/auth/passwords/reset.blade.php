@extends('layouts.auth')

@section('content')
<form class="form-horizontal" method="POST" action="{{ route('password.request') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <div class="form-group row">
        <div class="col-md-12">
            <label for="email" class="col-form-label">{{ __('E-Mail Address') }}</label>

            <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ $email ?? old('email') }}" required autofocus>

            @if ($errors->has('email'))
            <span class="invalid-feedback">
                <strong>{{ $errors->first('email') }}</strong>
            </span>
            @endif
        </div>
    </div>

    <div class="form-group row">
        <div class="col-md-12">
            <label for="password" class="col-form-label">{{ __('Password') }}</label>

            <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>

            @if ($errors->has('password'))
            <span class="invalid-feedback">
                <strong>{{ $errors->first('password') }}</strong>
            </span>
            @endif
        </div>
    </div>

    <div class="form-group row">
        <div class="col-md-12">
            <label for="password-confirm" class="col-form-label">{{ __('Confirm Password') }}</label>

            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
        </div>
    </div>
    <div class="d-grid my-4">
        <button class="btn btn-primary" type="submit">{{ __('Reset Password') }}</button>
    </div>
    <div class="form-group text-center m-t-20" style="margin:0">
        <div class="col-xs-12">
            <p><a href="{{ route('login') }}" class="text-primary m-l-5"><b>{{ __('Back to Login') }}</b></a></p>
        </div>
    </div>
</form>
@endsection
