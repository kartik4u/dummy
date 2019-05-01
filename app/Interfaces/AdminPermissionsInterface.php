<?php

namespace App\Interfaces;

use App\Http\Requests\Admin\Permission\CreatePermissionsRequest;
use App\Http\Requests\Admin\Permission\UpdatePermissionsRequest;
use App\Http\Requests\Admin\Permission\DeletePermissionsRequest;
use App\Http\Requests\Admin\Permission\ViewPermissionsRequest;



use Illuminate\Http\Request;

interface AdminPermissionsInterface {

    public function getAllPermissions(Request $request);

    public function createPermissions(CreatePermissionsRequest $request);

    public function updatePermissions(UpdatePermissionsRequest $request);

    public function deletePermissions(DeletePermissionsRequest $request);

    public function viewPermission(ViewPermissionsRequest $request);

}   
