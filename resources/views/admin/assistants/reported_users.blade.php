
      @extends('admin.index')
      @section('content')

<div class="dashboard-content padding-sm">
      <div class="dashboard-user-mange-section">

        <div class="user-mangement-list">

          <div class="total-entries"><span>{{ $users->total() }} Entrie(s)</span></div>
          <div class="data-table">
            <div class="table-responsive">
              <table class="table  table-bordered">
                <thead>
                  <tr>

                    <th class="text-center">Full Name</th>
                    <th class="text-center">No. of Times Reported</th>
                    <th class="text-center">Actions</th>
                  </tr>
                </thead>
                <tbody>

                  @if(count($users))
                  @foreach($users as $key => $user)
                  <tr>
                    <td class="text-center">{{ $user->reportedToUser->first_name }} {{ $user->reportedToUser->last_name }}</td>
                    <td class="text-center">{{ $user->total }}</td>
                    <td class="text-center">
                      @if($user->reportedToUser->is_verified == '1')
                      @if($user->reportedToUser->status == '1')
                      <a title="Deactivate" style="outline:none;" class="userstatus" data-id="{{ $user->reportedToUser->id }}" data-status="1"  href="javascript:void(0);" >
                      <span class="n-ico opt-ico"><i class="icon-not-allowed"></i></span></a>
                        @else
                        <a title="Activate" style="outline:none;" class="userstatus" data-id="{{ $user->reportedToUser->id }}" data-status="0"  href="javascript:void(0);" >
                          <span class="opt-ico activate-ico "><i class="icon-unlocked"></i></span></a>
                          @endif
                          @else
                          <span title="Unverified" class="opt-ico unv-ico"><i class="icon-danger"></i></span>
                          @endif
                          <a title="View" class="view-ico opt-ico" href="{{ route('admin.reportedusers.detail', $user->reportedToUser->id) }}"><i class="icon-eye"></i></a></td>
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
          </div>
          <div class="page-nation-list margin-top-4x">

             {{ $users->links() }}
          </div>
        </div>
      </div>
</div>
            <script src="{{ URL::asset('js/assistants.js') }}"></script>
            @endsection
