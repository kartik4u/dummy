


@extends('admin.index')
@section('content')

<div class="dashboard-content padding-sm">
<ol class="breadcrumb">

        <li class="breadcrumb-item"><a href="{{ route('admin.weeklychallenge.get') }}">Manage weeklychallenge</a></li>
          <li class="breadcrumb-item active">{{ $weeklychallenge->name }}</li>
    <li class="breadcrumb-item active">{{ $title }}</li>
</ol>
<div class="dashboard-user-mange-section">
  <div class="header-block">
    <div class="row">
      <div class="col-md-4">
        
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

      @if ($errors->any())
            <div class="alert alert-danger">
              <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
            @endif
    <div class="data-table">
      <div class="">
       <form action="{{ route('admin.weeklychallenge.postedit' , $weeklychallenge->id) }}" method="post" class="form-horizontal"  enctype="multipart/form-data">
                                  {{ csrf_field() }}
                                                                       
                                  
                      <div class="form-group row">
                         <label class="col-sm-3 form-control-label" for="input-small">weekly challenge Name</label>
                        <div class="col-sm-9">
                               <input id="input-small" name="sub_category" class="form-control form-control-sm" placeholder="weeklychallenge name" type="text" value="{{ old('sub_category',$weeklychallenge->name) }}">
                                                                            </div>
                                                                        </div>
                      
              <div class="form-group row">
                <label class="col-sm-3 form-control-label" for="input-small">Image</label>
                <div class="col-sm-9">
                  <input  name="image" id="image"  value="{{ old('image') }}" class="form-control-sm" placeholder="sub category name" type="file">
                </div>
              </div>

              <div class="form-group row">
                <label class="col-sm-3 form-control-label" for="input-small">Icon (Optional) </label>
                <div class="col-sm-9">
                  <input  name="tag" id="tag"  value="{{ old('tag') }}" class="form-control-sm"  type="file">
                </div>
              </div>

              <div class="form-group row">
                <label class="col-sm-3 form-control-label" for="input-small">Sponsered </label>
                <div class="col-sm-9">
                  <input  name="sponsered" {{ $weeklychallenge->sponsered == '1' ? 'checked' : '' }}   id="sponsered"  value="1" class="form-control-sm" type="radio">Yes 
                   <input  name="sponsered" id="sponsered"  value="0" class="form-control-sm" type="radio" {{ $weeklychallenge->sponsered == '0' ? 'checked' : '' }}>No 
                </div>
              </div>



                                                                      <button type="submit" class="button btn btn-sm btn-primary">Update</button>
                                                                    </form>
      </div>
    </div>
    
  </div>
</div>
</div>

@endsection

