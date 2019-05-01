


@extends('layouts.app')

@section('content')

  <div ui-view="header"></div>
     <div class="main-cotainer form-bg">
       <div ui-view="menu"></div>
       <div class="dashboard-content">
         <div class="dashboard-form-container">
           <div class="dashboard-form__box">
             <div class="logo"><img class="img-responsive" src="{{ asset('images/logo.png') }}" alt="{{ config('app.name')}}"/></div>
             <form class="form-horizontal" method="POST" action="{{ route('password.request') }}">
                 {{ csrf_field() }}

                 <input type="hidden" name="token" value="{{ $token }}">
             <div class="login-form">
               <div class="login-form__heading-block">
                 <h3>Reset Password</h3>
                 <h4>Enter your details below</h4>
               </div>

               <div class="field-group">
                 <div class="c-field c-b-sm c-bg-color">
                   <input class="c-field-control{{ $errors->has('email') ? ' has-error' : '' }}" type="text"  name="email" value="{{ old('email') }}" placeholder="Enter Email">
                   @if ($errors->has('email'))
                       <span class="help-block">
                           <strong class="text-danger">{{ $errors->first('email') }}</strong>
                       </span>
                   @endif

                 </div>

                 <div class="c-field c-b-sm c-bg-color">
                   <input class="c-field-control{{ $errors->has('password') ? ' has-error' : '' }}" type="password" placeholder="New Password"  name="password">
                   @if ($errors->has('password'))
                                   <span class="help-block">
                                       <strong class="text-danger">{{ $errors->first('password') }}</strong>
                                   </span>
                               @endif
                 </div>

                 <div class="c-field c-b-sm c-bg-color">
                   <input class="c-field-control{{ $errors->has('password_confirmation') ? ' has-error' : '' }}" type="password" placeholder="Confirm Password"  name="password_confirmation">
                   @if ($errors->has('password_confirmation'))
                                   <span class="help-block">
                                       <strong class="text-danger">{{ $errors->first('password_confirmation') }}</strong>
                                   </span>
                               @endif
                 </div>

                 <div class="btn-field">
                   <button class="button button-block button-font-medium button-height-large button-font-medium-txt button-rounded" type="submit">Reset Password</button>
                 </div>

               </div>
             </div>
           </form>
           </div>
         </div>
       </div>
     </div>
@endsection
