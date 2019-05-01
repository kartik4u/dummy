@extends('admin.index')
@section('content')

<div class="dashboard-content containerClass">
<div class="dashboard-t-info">
  <div class="row row-10">
    <div class="col-lg-6 col-md-6">
      <div class="dashboard-t-box c-1">
        <h3>{{ $users }}</h3>
        <h4>Number of Users<span class="ico"><i class="icon-user"></i></span></h4>
      </div>
    </div>
    
  </div>
</div>
    

@endsection
