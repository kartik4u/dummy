<li  style="background-color:#9ccc65" class="{{ Request::path() == 'admin/managePages' ? 'actives' : '' }}"><a  class="nav-link" style="background-color:#9ccc65" href="{{ route('admin.pages.get') }}"><span class="icon-manage-pages"></span> Manage Pages</a></li>
<style>
li{
    background-color: #9ccc65;
}
</style>

<div class="dashboard-nav" style="background-color:#9ccc65">
  <div style="background-color:#9ccc65" class="dashboard-nav__list">
    <ul>
      <li class="{{ Request::path() == 'admin' ? 'actives' : '' }}"><a  class="nav-link" style="background-color:#9ccc65" href="{{ route('admin.dashboard') }}"><span class="icon-metor"></span>Dashboard</a></li>
       <li  class="{{ Request::path() == 'admin/manageUsers' ? 'actives' : '' }}"><a style="background-color:#9ccc65" class="nav-link" href="{{ route('admin.users.list') }}"><span class="icon-user-management"></span>User Management</a></li>
       <li  class="{{ Request::path() == 'admin/manageReportedUsers' ? 'actives' : '' }}"><a  class="nav-link" style="background-color:#9ccc65" href="{{ route('admin.reportedusers.list') }}"><span class="icon-question"></span></span>Reported Users</a></li>
        <li  class="{{ Request::path() == 'admin/manageCategory' ? 'actives' : '' }}"><a  class="nav-link" style="background-color:#9ccc65" href="{{ route('admin.category.get') }}"><span  class="icon-category"></span> Manage Category</a></li>
    <li  class="{{ Request::path() == 'admin/categoryChallenge' ? 'actives' : '' }}"><a  class="nav-link" style="background-color:#9ccc65" href="{{ route('admin.subcategory.get') }}"><span class="icon-sub-category"></span> Manage Subcategory</a></li> 
   
  
      <li  class="{{ Request::path() == 'admin/managePages' ? 'actives' : '' }}"><a  class="nav-link" style="background-color:#9ccc65" href="{{ route('admin.pages.get') }}"><span class="icon-manage-pages"></span> Manage Pages</a></li>

       
  </div>
</div>
