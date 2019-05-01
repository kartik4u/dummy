<?php

namespace App\Interfaces;

use App\Http\Requests\Admin\Subscription\CreatePlanRequest;
use App\Http\Requests\Admin\Subscription\EditPlanRequest;
use App\Http\Requests\Admin\Subscription\ViewPlanRequest;
use App\Http\Requests\Admin\Subscription\DeletePlanRequest;

use Illuminate\Http\Request;

interface AdminSubscriptionsInterface {


    public function getPlans(Request $request);

    public function viewPlan(ViewPlanRequest $request);

    public function createPlan(CreatePlanRequest $request);

    public function editPlan(EditPlanRequest $request);

    public function deletePlan(DeletePlanRequest $request);

}   

