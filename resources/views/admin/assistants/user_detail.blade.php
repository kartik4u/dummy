@extends('admin.index')
@section('content')

<div class="dashboard-content">
 <ol class="breadcrumb">

        <li class="breadcrumb-item"><a href="{{ route('admin.assistants.list') }}">Manage Assistants</a></li>
          <li class="breadcrumb-item active">{{ ucfirst($user->first_name) }}</li>
<!--    <li class="breadcrumb-item active">{{ $title }}</li>-->
</ol>
<div class="dashboard-user-dtl-section">

  <div class="row">
    <div class="col-md-3">
	
      <div class="usr-pic-dtl">

          <img src="{{ $user->profile_image }}" style="width: 100px; height:100px;" alt="img"/>
        <div class="margin-top-5x">
             @if($user->status == 1)
          <a href="javascript:void(0);" class="u_status_single" data-id="{{ $user->id }} " data-status="2"><button class="button button-rounded button-danger-light button-shadow button-font-bold button-width-medium button-width-large margin-bottom-2x" data-ng-click="userblock()">Deactivate</button></a><br/>
          @elseif($user->status == 2)
          <a href="javascript:void(0);" class="u_status_single" data-id="{{ $user->id }} " data-status="1"><button class="button button-rounded button-danger-light button-shadow button-font-bold button-width-medium button-width-large margin-bottom-2x" >Activate</button></a><br/>
          @else
                       
                    @endif
         </div>
      </div>
    </div>
    <div class="col-md-9">
      <div class="profile-block">
        <div class="user-profile-dtl">
          <uib-tabset justified="true">
            <uib-tab index="0" heading="Overview">
              <div class="profile-block__container">
                <div class="profile-block__content">
                  <div class="profile-block__content__row">
                    <div class="usr-name">
                      <h3>{{ ucfirst($user->first_name) }} {{ ucfirst($user->last_name) }}</h3>
                    </div>
                     
                  </div>
                  <div class="profile-block__content__row">
                    <div class="usr-dtl">
                      <ul>
                        <li><span class="fw300 label-name">Universit Name :</span><span class="fw400">@if($user->university_name){{ $user->university_name }}@else - @endif</li>
                        <!--<li><span class="fw300 label-name">Email Id :</span><span class="fw400">@if($user->email){{ $user->email }}@else - @endif</span></li>-->
                         <li><span class="fw300 label-name">Unique Id :</span><span class="fw400">@if($user->unique_id){{ $user->unique_id }}@else - @endif</span></li>
                          <li><span class="fw300 label-name">Email Id :</span><span class="fw400">@if($user->email){{ $user->email }}@else - @endif</span></li>
                           <li><span class="fw300 label-name">Gender :</span><span class="fw400">@if($user->gender){{ $user->gender }}@else - @endif</span></li>
                          <li><span class="fw300 label-name">Phone no :</span><span class="fw400">@if($user->phone_no){{ $user->phone_no }}@else - @endif</span></li>
                       <form action="{{ route('admin.posts.list')}}" method="get">
                           <a href="javascript:void(0);"><button class="button button-rounded button-success-light button-shadow button-font-bold button-width-medium button-width-large margin-bottom-2x" name="user_id" value="{{ $user->id }}">Total Posts (@if($user->post){{ $user->post->count() }}@else 0 @endif) </button></a><br/>
                       </form>
                      </ul>
                    </div>
                  </div>

                </div>

              </div>
            </uib-tab>
            <uib-tab index="1" heading="About">
              <div class="profile-block__container">
                <div class="profile-block__content">

                </div>
              </div>
            </uib-tab>

          </uib-tabset>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
     
      <script src="{{ URL::asset('admin_assets/js/assistants.js') }}"></script>
      @endsection
