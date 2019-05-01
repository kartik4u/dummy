<?php

namespace App\Interfaces;

use App\Http\Requests\Admin\Role\CreateRolesRequest;
use App\Http\Requests\Admin\Role\UpdateRolesRequest;
use App\Http\Requests\Admin\Role\DeleteRolesRequest;
use App\Http\Requests\Admin\Role\ViewRolesRequest;

use Illuminate\Http\Request;

interface AdminRolesInterface {

    public function getAllRoles(Request $request);

    public function createRoles(CreateRolesRequest $request);

    public function updateRoles(UpdateRolesRequest $request);

    public function deleteRoles(DeleteRolesRequest $request);

    public function viewRole(ViewRolesRequest $request);
}   

