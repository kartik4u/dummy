@extends('admin.index')
@section('content')


<div class="dashboard-content padding-sm">
  <div class="dashboard-user-mange-section">
        @if (Session::has('flash_message'))
            <div class="alert alert-{!! Session::get('flash_level') !!}">
                {!! Session::get('flash_message') !!}
            </div>
        @endif
        @if ($errors->any())
        <div class="alert alert-danger">
          <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
        @endif
        <form class="form-horizontal" method="POST" action="{{ route('admin.postchange.password') }}">
               {{ csrf_field() }}
        <div class="c-field">
          <label>Current Password</label>
          <input class="c-field-control" type="password" name="current-password"  placeholder="Enter Current Password"/>
        </div>
        <div class="c-field">
          <label>New Password</label>
          <input class="c-field-control" type="password" name="password" placeholder="Enter New Password"/>
        </div>
        <div class="c-field">
          <label>Confirm Password</label>
          <input class="c-field-control" type="password" name="password_confirmation"  placeholder="Enter New Password"/>
        </div>
        <div class="btn-field padding-top-4x">
          <button class="button button-rounded button-uppercase button-width-large button-font-sbold margin-right-2x" type="submit"> Save</button>
            </form>
        <!-- <button class="button button-danger button-rounded button-uppercase button-width-large button-font-sbold"> <a href="{{ route('admin.dashboard') }}"> CANCEL </a></button> -->
        </div>

      </div>
    </div>

  </div>
</div>
      @endsection
