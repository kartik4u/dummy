@extends('layouts.app')

@section('content')

<div ui-view="header"></div>
   <div class="main-cotainer form-bg">
     <div ui-view="menu"></div>
     <div class="dashboard-content">

<div class="dashboard-form-container">
  <div class="dashboard-form__box">
    <div class="logo"><img class="img-responsive" src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}"/></div>
    <div class="forgot-form">
      <div class="forgot-form__heading-block">
        <h3>Forgot Your Password ?</h3>
        <p>In order to receive your access code - please enter the email address you provided during  the registration process.</p>
      </div>
      @if (session('status'))
          <div class="alert alert-success margin-top-4x">
              {{ session('status') }}
          </div>
      @endif

      <form class="form-horizontal" method="POST" action="{{ route('password.email') }}">
          {{ csrf_field() }}
      <div class="field-group">
        <div class="c-field c-b-sm c-bg-color">
          <input class="c-field-control" type="email" placeholder="Enter Email/Username"  name="email" value="{{ old('email') }}" />
          @if ($errors->has('email'))
              <span class="help-block">

                  <strong class="text-danger">{{ $errors->first('email') }}</strong>
              </span>
          @endif
        </div>
        <div class="btn-field">
          <button class="button button-uppercase button-block button-font-medium button-height-large button-font-medium-txt button-rounded margin-bottom-3x" type="submit">SUBMIT</button>


     <a href="{{ route('login') }}" class="button button-uppercase button-danger button-block button-font-medium button-height-large button-font-medium-txt button-rounded">Cancel</a>
        </div>
      </div>
      </form>

    </div>
  </div>
</div>
</div>
</div>
@endsection
