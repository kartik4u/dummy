<?php

namespace App\Interfaces;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\Admin\User\UserChangePasswordRequest;
use App\Http\Requests\UserResetPasswordRequest;
use App\Http\Requests\Admin\User\ChangeStatusRequest;
use App\Http\Requests\Admin\User\DeleteUserRequest;
use App\Http\Requests\Admin\User\UserDetailRequest;
use Illuminate\Http\Request;

interface AdminUserInterface {

    public function login(UserLoginRequest $request);
    public function logout(Request $request);
    public function forgotPassword(ForgotPasswordRequest $request);
    public function resetPassword(UserResetPasswordRequest $request);
    public function changePassword(UserChangePasswordRequest $request); 
    public function getUsers(Request $request); 
    public function changeStatus(ChangeStatusRequest $request); 
    public function deleteUser(DeleteUserRequest $request); 
    public function userDetail(UserDetailRequest $request); 
    public function dashboard(Request $request); 

}

