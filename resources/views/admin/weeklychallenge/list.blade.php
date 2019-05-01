@extends('admin.index')
@section('content')

<div class="dashboard-content padding-sm">
<ol class="breadcrumb">
   <li class="breadcrumb-item active">{{ $title }}</li>
</ol>
<div class="dashboard-user-mange-section">
  <div class="header-block">
    <div class="row">
      <div class="col-md-4">
        <div class="sort-input-field margin-bottom-2x">
            <a href="{{ route('admin.weeklychallenge.getadd') }}" class="button"><i class="fa fa-plus" aria-hidden="true"></i>
            Add weekly challenge</a>
         
      </div>
          @if (Session::has('flash_message'))
    <div class="alert alert-{!! Session::get('flash_level') !!}">
        {!! Session::get('flash_message') !!}
    </div>
@endif

		
      <div class="col-md-8">
        <div class="text-right">
           
        </div>
      </div>
    </div>
  </div>



  <div class="user-mangement-list">

    <div class="total-entries"><span>{{ $weeklychallenge->total() }} Entrie(s)</span></div>
    <div class="data-table">
      <div class="table-responsive">
        <table class="table table-bordered">
          <thead>
            <tr>
            
               <th class="text-center">Weekly challenge</th>
                <th class="text-center">Image</th>
               <th class="text-center">Timing</th>
               <th class="text-center">Status</th>
              <th class="text-center">Actions</th>
            </tr>
          </thead>
          <tbody>

            @if(count($weeklychallenge))
                           @foreach($weeklychallenge as $cat)

            <tr>
                 
                  <td>{{ ucfirst($cat->name) }} @if($cat->sponsered == 1) (Sponsered) @endif </td>
                   <td>
                    <div style="width: 200px; height: 150px; position: relative;">
   <img id="image1" style="position: relative; width: 100px; height: 100px;" src="{{ $cat->icon }}" alt=""  />
   <img id="image2" style="width: 20px; height: 20px;  style="position: absolute; top: 0px; left: 10px; bottom: 80px; right: 10px;" src="{{ $cat->tag }}" alt="" />
</div>
                  </td>
                  <td>
<?php 

                   $date = $cat->created_at; // Change to whatever date you need
$dotw = $dotw = date('w', $date);
$start = ($dotw == 1 /* Saturday */) ? $date : strtotime('last Monday', $date);
$end = ($dotw == 7 /* Friday */) ? $date : strtotime('next Sunday', $date);
            echo date('d/m/Y', $start)." To ".date('d/m/Y', $end);
                  ?>
                  </td>

                  
                  <td align="center">
                    @if($cat->status == 1)
                  Active
                     
                     @else
                        Deactivate
                        @endif
                      </td>
                     
                      <td>  @if($cat->status == 1)
                  <a title="Deactivate"   style="outline:none;"  href="javascript:void(0);" class="subcatstatus" data-id="{{ $cat->id }}" data-status="1">
									<span class="n-ico opt-ico"><i class="icon-not-allowed"></i></span></a>
									
                    @else
                   <a title="Activate"  style="outline:none;" class="subcatstatus" data-id="{{ $cat->id }}" data-status="0" href="javascript:void(0);" >
                                        <span class="opt-ico activate-ico"><i class="icon-unlocked"></i></span></a>
										
                    @endif	
                    
                    <span><a href="{{ route('admin.weeklychallenge.getedit', $cat->id) }}"><i class="icon-edit"></i></a></span>
                      <!--<span><a class="catdel" href="javascript:void(0);" data-id="{{$cat->id}}" ><i class="fa fa-trash-o text-danger" aria-hidden="true">delete</i></a></span>-->
                    </td>
                  </tr>
                    @endforeach
                    @else
                    <tr>
                      <td colspan="2">No Result(s) Found</td>
                    </tr>
                    @endif

          </tbody>
        </table>
      </div>
    </div>
    <div class="cat-nation-list margin-top-4x">

       {{ $weeklychallenge->links() }}
    </div>
  </div>
</div>
</div>

<script src="{{ URL::asset('admin_assets/js/weeklychallenge.js') }}"></script>
 @endsection
