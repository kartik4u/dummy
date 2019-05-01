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
<!--        <div class="sort-input-field margin-bottom-2x">
          
            <a href="{{ route('admin.pages.getadd') }}" class="button"><i class="fa fa-plus" aria-hidden="true"></i>
            Add Page</a>
         
      </div>-->
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

    <div class="total-entries"><span>{{ $pages->total() }} Entrie(s)</span></div>
    <div class="data-table">
      <div class="table-responsive">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th class="text-center">Name</th>
              <th class="text-center">Actions</th>
            </tr>
          </thead>
          <tbody>

            @if(count($pages))
                           @foreach($pages as $page)

            <tr>
                  <td> {{ ucfirst($page->name) }} (v{{$page->version}})
                      </td>

                      <td>
                              <span class="opt-ico">  
                            <a target='_blank' href="{{ route('getpage.pages',['version'=>'v'.$page->version,'slug'=>$page->meta_key]) }}">
                              <i class="icon-eye"></i>
                            </a>
                        </span>
                              @if(($page->version == $aboutus_latest_id->version) && ($page->meta_key == 'about_us'))
                                  <span class="opt-ico">
                  <a href="{{ route('admin.pages.getedit', $page->id) }}"><i class="icon-edit"></i></a></span>
                  @elseif(($page->version == $policy_latest_id->version) && ($page->meta_key == 'privacy_policy'))
                                  <span class="opt-ico">
                  <a href="{{ route('admin.pages.getedit', $page->id) }}"><i class="icon-edit"></i></a></span>
                  @elseif(($page->version == $term_latest_id->version) && ($page->meta_key == 'term'))
                                  <span class="opt-ico">
                  <a href="{{ route('admin.pages.getedit', $page->id) }}"><i class="icon-edit"></i></a></span>
                  @endif
                 
                  
                        
                      <!--<span><a class="pagedel" href="javascript:void(0);" data-id="{{$page->id}}" ><i class="icon-delete"></i></a></span>-->
                    </td>
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

       {{ $pages->links() }}
    </div>
  </div>
</div>
</div>

<script src="{{ URL::asset('admin_assets/js/pages.js') }}"></script>

      @endsection
