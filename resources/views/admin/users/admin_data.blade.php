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
        <form class="form-horizontal" method="POST" action="{{ route('admin.postdata') }}">
               {{ csrf_field() }}
        
               @foreach($admin_data as $data)
               <div class="c-field">
                <label>{{ ucfirst(str_replace('_', ' ', $data->name)) }}</label>
                <input type="text" name="options[{{ $data->name}}]" 
                value="{{ old($data->name, $data->value) }}"
                class="form-control col-md-6"  placeholder="Please enter data.">
               </div>
               @endforeach
       
               
       
        <div class="btn-field padding-top-4x">
          <button class="button button-rounded button-uppercase button-width-large button-font-sbold margin-right-2x" type="submit"> Save</button>
            </form>
      
        </div>

      </div>
    </div>

  </div>
</div>
      @endsection
