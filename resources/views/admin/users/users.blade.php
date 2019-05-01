@extends('admin.index')
@section('content')


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
                            <tr >
                                <th class="text-center" style="background-color:#9ccc65">Name</th>
                                <th class="text-center" style="background-color:#9ccc65">Email</th>
                                <th class="text-center" style="background-color:#9ccc65">Joined Date <br/><span>(DD/MM/YYYY)</span></th>
                               
                                <th class="text-center" style="background-color:#9ccc65">Status</th>

                                <th class="text-center" style="background-color:#9ccc65">Actions</th>
                            </tr>
                        </thead>
                        <tbody>

                            @if(count($users))
                            @foreach($users as $user)

                            <tr>
                                <td class="text-center">{{ ucfirst($user->fullname) }} </td>
                                <td class="text-center">{{ $user->email }}</td>
                                <td class="text-center">{{ 	date('d/m/Y', $user->created_at)  }}</td>
                                
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
                                            <i class="icon-delete"></i>
                                            
                                        </span>
                                     </a>
                                                                                  

                                    @if($user->status == 1)
                                    <a title="Deactivate"   style="outline:none;"  href="javascript:void(0);" class="userstatus" data-id="{{ $user->id }}" data-status="2">
                                        <span class="n-ico opt-ico"><i class="icon-not-allowed"></i></span></a>

                                    @elseif($user->status == 2)
                                    <a title="Activate"  style="outline:none;" class="userstatus" data-id="{{ $user->id }}" data-status="1" href="javascript:void(0);" >
                                        <span class="opt-ico activate-ico"><i class="icon-unlocked"></i></span></a>

                                    @else

                                    @endif

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

                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>

<script src="{{ URL::asset('admin_assets/js/users.js') }}"></script>
@endsection
