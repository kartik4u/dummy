@extends('admin.index')
@section('content')

<div class="dashboard-content">

<div class="dashboard-user-dtl-section">

  <div class="row">
    <div class="col-md-3">
	<div class="margin-bottom-3x">
	<a class="back-btn" data-ui-sref="dashboard-user-management" href="{{ URL::previous() }}"><span class="ico"><i class="icon-arrow-left"></i></span>Back </a>
	</div>
      <div class="usr-pic-dtl">

          <img src="{{ $user->profile_image }}" style="width: 100px; height:100px;" alt="img"/>
        <!--<div class="margin-top-5x">
            @if($user->status == 1)
          <a href="javascript:void(0);" class="u_status_single" data-id="{{ $user->id }} " data-status="1"><button class="button button-rounded button-danger-light button-shadow button-font-bold button-width-medium button-width-large margin-bottom-2x" data-ng-controller="ModalCtrl" data-ng-click="userblock()">Deactivate</button></a><br/>
          @else
          <a href="javascript:void(0);" class="u_status_single" data-id="{{ $user->id }} " data-status="0"><button class="button button-rounded button-danger-light button-shadow button-font-bold button-width-medium button-width-large margin-bottom-2x" data-ng-controller="ModalCtrl" data-ng-click="userblock()">Activate</button></a><br/>
          @endif
         </div>-->
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
                        <li><span class="fw300 label-name">Email Id :</span><span class="fw400">@if($user->email){{ $user->email }}@else - @endif</span></li>
                         <li><span class="fw300 label-name">Unique Id :</span><span class="fw400">@if($user->unique_id){{ $user->unique_id }}@else - @endif</span></li>
                          <li><span class="fw300 label-name">Email Id :</span><span class="fw400">@if($user->email){{ $user->email }}@else - @endif</span></li>
                           <li><span class="fw300 label-name">Gender :</span><span class="fw400">@if($user->gender){{ $user->gender }}@else - @endif</span></li>
                          <li><span class="fw300 label-name">Phone no :</span><span class="fw400">@if($user->phone_no){{ $user->phone_no }}@else - @endif</span></li>
                     
                      </ul>
                    </div>
                  </div>
<!--                  <div class="profile-block__content__row">
                    <div class="usr-oh-info usr-work-info">
                      <h4>Work Related Experience and Skiils</h4>
                      <ul>
                        <li class="w-label">IT Management</li>
                        <li class="w-time">6 Years 8 Months</li>
                        <li class="v-link">
                          <button class="button button-theme button-rounded button-font-sbold button-width-medium button-font-base-small" ng-controller="ModalCtrl" ng-click="workhistory()">View All</button>
                        </li>
                      </ul>
                    </div>
                  </div>-->
                </div>
<!--                <div class="usr-tp-container">
                  <div class="usr-tp-block">
                    <h2 class="title">Posts</h2>
                    <div class="usr-tp__list">
                      <div class="row">
                        <div class="col-md-4 col-sm-4 col-xs-4">
                          <div class="usr-tp__box">
                            <h3>Life Routes</h3>
                            <div class="t-no c-1">55</div>
                          </div>
                        </div>
                        <div class="col-md-4 col-sm-4 col-xs-4">
                          <div class="usr-tp__box text-center">
                            <h3>News Posts<146px/h3>
                            <div class="t-no c-2">5</div>
                          </div>
                        </div>
                        <div class="col-md-4 col-sm-4 col-xs-4">
                          <div class="usr-tp__box text-center">
                            <h3>Event Posts</h3>
                            <div class="t-no c-3">7</div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="usr-tp-block">
                    <h2 class="title">People</h2>
                    <div class="usr-tp__list">
                      <div class="row">
                        <div class="col-md-4 col-sm-4 col-xs-4">
                          <div class="usr-tp__box">
                            <h3>Connections</h3>
                            <div class="t-no c-4">55</div>
                          </div>
                        </div>
                        <div class="col-md-4 col-sm-4 col-xs-4">
                          <div class="usr-tp__box text-center">
                            <h3>People Following Me</h3>
                            <div class="t-no c-5">5</div>
                          </div>
                        </div>
                        <div class="col-md-4 col-sm-4 col-xs-4">
                          <div class="usr-tp__box text-center">
                            <h3>People I am Following</h3>
                            <div class="t-no c-6">7</div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>-->
              </div>
            </uib-tab>
            <uib-tab index="1" heading="About">
              <div class="profile-block__container">
                <div class="profile-block__content">
<!--                  <div class="profile-block__content__row">
                    <div class="usr-name">
                      <h3>Albert Smith</h3>
                    </div>
                    <div class="usr-profile"><span>Entrepreneur</span></div>
                  </div>-->
<!--                  <div class="profile-block__content__row">
                    <div class="usr-dtl">
                      <ul>
                        <li><span class="fw300 label-name">Age : </span><span class="fw400">66 Years Old</span></li>
                        <li><span class="fw300 label-name">Location :</span><span class="fw400">London, United Kingdom</span></li>
                      </ul>
                    </div>
                  </div>-->
<!--                  <div class="profile-block__content__row">
                    <div class="usr-dtl">
                      <ul>
                        <li><span class="fw300 label-name">Gender :</span><span class="fw400">Male</span></li>
                        <li><span class="fw300 label-name">Date of Birth : </span><span class="fw400">6 January 1982</span></li>
                        <li><span class="fw300 label-name">Email : </span><span class="fw400">Myliferoute@gmail.com</span></li>
                        <li><span class="fw300 label-name">Education Level :  </span><span class="fw400">PHD</span></li>
                      </ul>
                    </div>
                  </div>-->
<!--                  <div class="profile-block__content__row">
                    <div class="usr-oh-info">
                      <h4>Interests</h4>
                      <p class="fw300">Adventure, Reading, Gym, Hiking, Partying, Internet, Entrepreneurship</p>
                    </div>
                  </div>-->
<!--                  <div class="profile-block__content__row">
                    <div class="usr-oh-info usr-work-info">
                      <h4>Work Related Experience and Skiils</h4>
                      <ul>
                        <li class="w-label">IT Management</li>
                        <li class="w-time">6 Years 8 Months</li>
                        <li class="v-link">
                          <button class="button button-theme button-rounded button-font-sbold button-width-medium button-font-base-small" ng-controller="ModalCtrl" ng-click="workhistory()">View All</button>
                        </li>
                      </ul>
                    </div>
                  </div>-->
<!--                  <div class="profile-block__content__row">
                    <div class="about-block">
                      <h2 class="title">About Me</h2>
                      <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's st been dard dummy text ever since the 1500s,when an unknown printer took a galley tially unchanged typesetting prinitng tooks Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's st been dard dummy text ever since the 1500s,when an unknown printer took a galley tially unchanged typesetting prinitng been Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's st took dard dummy text ever since the 1500s,when an unknown printer took a galley tially unchanged typesetting prinitng been Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's st took dard dummy text ever since the 1500s,when an unknown printer took a galley tially unchanged typesetting prinitng been</p>
                    </div>
                  </div>-->
                </div>
              </div>
            </uib-tab>
<!--            <uib-tab index="2" heading="Posts">
              <div class="profile-post__container">
                <div class="usr-post-list">
                  <div class="connection-box c-1"><a href="#">
                      <h3>60 Life Routes</h3></a></div>
                  <div class="connection-box c-2"><a href="#">
                      <h3>88 News Posts</h3></a></div>
                  <div class="connection-box c-3"><a href="#">
                      <h3>66 Event Posts</h3></a></div>
                </div>
              </div>
            </uib-tab>-->
<!--            <uib-tab index="3" heading="People">
              <div class="profile-people__container">
                <div class="usr-conection-list">
                  <div class="connection-box c-4"><a href="#">
                      <h3>66 Connections</h3></a></div>
                </div>
              </div>
            </uib-tab>-->
          </uib-tabset>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
      <script src="{{ URL::asset('js/users.js') }}"></script>
      @endsection
