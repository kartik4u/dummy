


@extends('admin.index')
@section('content')

<div class="dashboard-content padding-sm">
<ol class="breadcrumb">

        <li class="breadcrumb-item"><a href="{{ route('admin.subject.get') }}">Manage subject</a></li>
          <li class="breadcrumb-item active">{{ $subject->title }}</li>
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
       <form action="{{ route('admin.subject.postedit' , $subject->id) }}" method="post" class="form-horizontal" enctype="multipart/form-data">
                                  {{ csrf_field() }}
                                                                        <div class="form-group row">
                                                                            <label class="col-sm-3 form-control-label" for="input-small">Subject Name</label>
                                                                            <div class="col-sm-9">
                                                                                <input id="input-small" name="subject" class="form-control form-control-sm" placeholder="subject name" type="text" value="{{ old('subject',$subject->name) }}">
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

