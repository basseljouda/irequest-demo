@extends('layouts.auth')

@section('content')
<style>
    #registerform{
        margin-top: -30px;
        font-size: 13px;
    }
    #registerform > div.row{
        margin: 0;
    }
</style>
@if ($registered)
<div>Thank you, our team will review your request, once the information you provided has been verified, you will recieve an email with access data. </div>
@else
<form action="{{ route('login') }}" id="loginform" method="post">
    @csrf
        @if (session('status'))
    <div class="alert alert-success m-t-10">
        {{ session('status') }}
    </div>
    @endif
    <div class="mb-3">
        <label for="email" class="form-label">Email Address</label>
        <input id="email" type="email" name="email" class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}"
               placeholder="{{ __('Email Address') }}" value="{{ old('email') }}" required autofocus>
        @if ($errors->has('email'))
        <span class="invalid-feedback">{{ $errors->first('email') }}</span>
        @endif
    </div>
    <div class="mb-3">
        <label for="loginPassword" class="form-label">Password</label>
        <input id="password" type="password" placeholder="{{ __('Password') }}"
               class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>
        @if ($errors->has('password'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('password') }}</strong>
        </span>
        @endif
    </div>
    <div class="row mt-4">
        <div class="col">
            <!--div class="form-check">
                <input id="remember-me" name="remember" class="form-check-input" type="checkbox">
                <label class="form-check-label" for="remember-me">Remember Me</label>
            </div-->
        </div>
        <div class="col text-end"><a href="#" id="to-recover">Forgot Password ?</a></div>
    </div>
    <div class="d-grid my-4">
        <button class="btn btn-primary" type="submit">Login</button>
    </div>
</form>
<form class="form-horizontal" method="post" id="recoverform" style="display: none"
      action="{{ route('password.email') }}">
    {{ csrf_field() }}

    <div class="form-group ">
        <div class="col-xs-12">
            <p class="text-muted">@lang('app.enterEmailInstruction')</p>
        </div>
    </div>
    <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
        <div class="col-xs-12">
            <input class="form-control" type="email" id="email" name="email" required=""
                   placeholder="{{ __('Email Address') }}" value="{{ old('email') }}">
            @if ($errors->has('email'))
            <span class="help-block">
                {{ $errors->first('email') }}
            </span>
            @endif
        </div>
    </div>
    <div class="d-grid my-4">
        <button class="btn btn-primary" type="submit">@lang('app.sendPasswordLink')</button>
    </div>
    <div class="form-group text-center m-t-20" style="margin:0">
        <div class="col-xs-12">
            <p><a href="{{ route('login') }}" class="text-primary m-l-5"><b>{{ __('Back to Login') }}</b></a></p>
        </div>
    </div>
</form>
<form class="form-horizontal" method="post" style="display:none" id="registerform" action="{{ route('register') }}">
    <br/>
    <!--include('admin.hospital-staff._form')-->
    <div class="d-grid my-4">
        <button class="btn btn-primary" type="submit">@lang('app.sendPasswordLink')</button>
    </div>
    <div class="form-group text-center m-t-20" style="margin:0">
        <div class="col-xs-12">
            <p><a href="{{ route('login') }}" class="text-primary m-l-5"><b>{{ __('Back to Login') }}</b></a></p>
        </div>
    </div>
</form>
@endif
@endsection
