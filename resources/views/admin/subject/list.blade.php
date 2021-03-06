@extends('admin.index')
@section('content')

<div class="dashboard-content padding-sm">
<ol class="breadcrumb">
   <li class="breadcrumb-item active">{{ $title }}</li>
</ol>
<div class="dashboard-user-mange-section">
  <div class="header-block">
    <div class="row">
      <div class="col-md-5">
        <div class="sort-input-field margin-bottom-2x">
        
          <form action="{{ route('admin.subject.postadd') }}" method="post" enctype="multipart/form-data">
              {{ csrf_field() }}
              <div class="form-group">
                <label class="form-control-label" for="input-small">Subject Name</label>
                
                  <input id="input-small" name="subject" value="{{ old('subject') }}" class="form-control form-control-sm" placeholder="subject name" type="text">
                    @if ($errors->any())
            <div class="alert alert-danger">
              <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
            @endif
                
              </div>
                     
              <button type="submit" class="button btn btn-sm btn-primary">Submit</button>
            </form>
         
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

    <div class="total-entries"><span>{{ $subject->total() }} Entrie(s)</span></div>
    <div class="data-table">
      <div class="table-responsive">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th class="text-center">Name</th>
              <th class="text-center">Status</th>
              <th class="text-center">Actions</th>
            </tr>
          </thead>
          <tbody>

            @if(count($subject))
                           @foreach($subject as $cat)

            <tr>
                  <td>{{ ucfirst($cat->name) }}</td>
 <td align="center">
                    @if($cat->status == 1)
                  Active
                     
                     @else
                        Deactivate
                        @endif
                      </td>
                     
                      <td>
                           @if($cat->status == 1)
                  <a title="Deactivate"   style="outline:none;"  href="javascript:void(0);" class="catstatus" data-id="{{ $cat->id }}" data-status="1">
									<span class="n-ico opt-ico"><i class="icon-not-allowed"></i></span></a>
									
                    @else
                   <a title="Activate"  style="outline:none;" class="catstatus" data-id="{{ $cat->id }}" data-status="0" href="javascript:void(0);" >
                                        <span class="opt-ico activate-ico"><i class="icon-unlocked"></i></span></a>
										
                    @endif
                    <span><a href="{{ route('admin.subject.getedit', $cat->id) }}"><i class="icon-edit"></i></a></span>
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

       {{ $subject->links() }}
    </div>
  </div>
</div>
</div>

<script src="{{ URL::asset('admin_assets/js/subject.js') }}"></script>
 @endsection
