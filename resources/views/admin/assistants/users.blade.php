@extends('admin.index')
@section('content')


<!--content-block-->

<div class="dashboard-content padding-sm">

<div class="dashboard-user-mange-section">
  <div class="header-block">
    <div class="row">
      <div class="col-md-4">
        <div class="sort-input-field margin-bottom-2x">
<!--          <div class="select-control">
            <select class="sort-field-control" name="sort_by" onchange="this.form.submit()">
							<option value="1" @if(app('request')->input('sort_by') == "1") selected @endif >Latest First</option>
							<option value="2" @if(app('request')->input('sort_by') == "2") selected @endif>Oldest First</option>
							<option value="3" @if(app('request')->input('sort_by') == "3") selected @endif>Latest Updated</option>
							<option value="4" @if(app('request')->input('sort_by') == "4") selected @endif>Oldest Updated</option>
            </select>
          </div>-->
        </div>
      </div>
			  </form>



      <div class="col-md-8">
        <div class="text-right">
           
        </div>
      </div>
    </div>
  </div>



  <div class="user-mangement-list">

    <div class="total-entries"><span>{{ $users->total() }} Entrie(s)</span></div>
    <div class="data-table">
      <div class="table-responsive">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th class="text-center">Name</th>
              <th class="text-center">Email</th>
              <th class="text-center">Joined Date <br/><span>(DD/MM/YYYY)</span></th>
              <th class="text-center">Number of Post Added</th>
              <th class="text-center">Score provided on number of posts</th>
             
              <th class="text-center">Status</th>
             
              <th class="text-center">Actions</th>
            </tr>
          </thead>
          <tbody>

            @if(count($users))
                           @foreach($users as $user)

            <tr>
              <td class="text-center">{{ ucfirst($user->first_name) }} {{ ucfirst($user->last_name) }}</td>
              <td class="text-center">{{ $user->email }}</td>
              <td class="text-center">{{ date('d/m/Y', $user->created_at)  }}</td>
              
              <td class="text-center">@if($user->post){{ $user->post->count() }}@else - @endif</td>
              <td class="text-centerx">
                  @php
$total = 0;
@endphp
              
              @if($user->post)  
              @foreach($user->post as $pos)  
               @php
              $total+= $pos->total_result->count();
            @endphp
              @endforeach 
             {{ $total }}
              @else - @endif
              </td>
              <td align="center">
                    @if($user->status == 1)
                  Active
                      @elseif($user->status == 2)
                    Deactive
                     @else
                        Un-verified
                        @endif
                      </td>
              
           <td align="center">
               <a title="Delete User"  style="outline:none;" class="userdel" data-id="{{ $user->id }}" data-status="1" href="javascript:void(0);" >
                                        <span class="opt-ico activate-ico">
                                            <!--<i class="icon-trash"></i>-->
                                            <i class="icon-delete"></i>
                                        </span>
                                     </a>
								<a title="View" class="view-ico opt-ico" href ="<?php echo url('/admin/manageAssistants/' . $user->id) ?>">
								<i class="icon-eye"></i></a>
								
                   @if($user->status == 1)
                  <a title="Deactivate"   style="outline:none;"  href="javascript:void(0);" class="userstatus" data-id="{{ $user->id }}" data-status="2">
									<span class="n-ico opt-ico"><i class="icon-not-allowed"></i></span></a>
									
                    @elseif($user->status == 2)
                   <a title="Activate"  style="outline:none;" class="userstatus" data-id="{{ $user->id }}" data-status="1" href="javascript:void(0);" >
                                        <span class="opt-ico activate-ico"><i class="icon-unlocked"></i></span></a>
										
                    @else
                       
                    @endif
                    
                    @if($user->status == 1)
                    @if($user->post_verify == 0)
                    <a title="Verify"   style="outline:none;"  href="javascript:void(0);" class="userverify" data-id="{{ $user->id }}" data-post_verify="0">
									<span class="n-ico opt-ico"><i class="icon-check"></i></span></a>
                    @else
                     <a title="Unverify"   style="outline:none;"  href="javascript:void(0);" class="userverify" data-id="{{ $user->id }}" data-post_verify="1">
									<span class="n-ico opt-ico"><i class="icon-close"></i></span></a>
                   
                    
                                                                        @endif
                    @endif
                    </tr>
                    @endforeach
                    @else
                    <tr>
                      <td colspan="15">No Result(s) Found</td>
                    </tr>
                    @endif


          </tbody>
        </table>
      </div>
    </div>
    <div class="page-nation-list margin-top-4x">

       {{ $users->links() }}
    </div>
  </div>
</div>
</div>

<script src="{{ URL::asset('admin_assets/js/assistants.js') }}"></script>

      @endsection
