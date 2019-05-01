@extends('admin.index')
@section('content')
<div class="dashboard-content padding-sm">

<div class="row">

<div class="col-md-6">
<!-- Breadcrumb -->
<ol class="breadcrumb">
  <li class="breadcrumb-item active"><a href="{{ route('admin.reportedusers.list') }}">{{ $title }}</a></li>
    <li class="breadcrumb-item active">{{ $user->name }}</li>
  <li class="breadcrumb-item active">Details For Reports</li>
</ol>
</div>

@if (Session::has('flash_message'))
    <div class="alert alert-{!! Session::get('flash_level') !!}">
        {!! Session::get('flash_message') !!}
    </div>
@endif


 <div class="col-md-6 text-right">
                <div class="d-inline-block">
                <!-- <form class="form-inline my-1" action="" method="get">
                  <label>Reason:</label> <div class="custom-select-tp"><select  class="mx-sm-1 form-control" name="reason" onchange="this.form.submit()">
                    <option value="all"  @if(app('request')->input('reason') == "all") selected @endif>All</option>
                    <option value="Commercial or Spam or Fake"  @if(app('request')->input('reason') == "Commercial or Spam or Fake") selected @endif>Commercial or Spam or Fake</option>
                    <option value="Abusive"  @if(app('request')->input('reason') == "Abusive") selected @endif>Abusive</option>
                    <option value="Inappropriate" @if(app('request')->input('reason') == "Inappropriate") selected @endif>Inappropriate</option>
                    <option value="Misleading" @if(app('request')->input('reason') == "Misleading") selected @endif>Misleading</option>
                  </select></div>
                </form> -->
              </div>
                @if($user->status != '0')
                <div class="d-md-inline-block mt-1 align-top">
                @if($user->status == '1')
                <a class="u_status_single" data-id="{{ $user->id }}" data-status="2"  href="javascript:void(0);" >
                  <span class="btn btn-danger mt-0" >Deactivate</span></a>
                  @else
                  <a class="u_status_single" data-id="{{ $user->id }}" data-status="1"  href="javascript:void(0);" >
                    <span class="btn btn-success mt-0" >Activate</span></a>
                @endif
                 
                @endif
                  </div>
</div>


</div>



	<div class="dashboard-user-mange-section">

        <div class="user-mangement-list">
              <div class="total-entries"><span>{{ $users->total() }} Entrie(s)</span></div>
              <div class="data-table">
                  <table class="table  table-bordered">
                  <thead>
                    <tr>
                      <th class="text-center">Name</th>
                      <th class="text-center">Reason</th>
                      <th class="text-center">Comment</th>
                      <th class="text-center">Date & Time</th>
                    </tr>
                  </thead>
                  <tbody>
                    @if(count($users))
                    @foreach($users as $key => $user)
                    <tr>
                      <td class="text-center">{{ $user->reportedFrom->name }}</td>
                      <td class="text-center">{{ $user->reason }}</td>
                      <td class="text-center" style="width:30%;">{{ $user->comment }}</td>
                      <td class="text-center">{{ date("m/d/Y h:i:s A",$user->created_at) }}</td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                      <td colspan="4">No Result(s) Found</td>
                    </tr>
                    @endif
                  </tbody>
                </table>
              </div>
              <div class="page-nation-list margin-top-4x">

                 {{ $users->links() }}
              </div>
            </div>
          </div>
</div>
</div>
</div>
                <script src="{{ URL::asset('admin_assets/js/users.js') }}"></script>
                @endsection
