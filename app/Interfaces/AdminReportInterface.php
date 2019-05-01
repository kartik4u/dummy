<?php

namespace App\Interfaces;
use App\Http\Requests\Admin\Report\GetReportDatailRequest;
use App\Http\Requests\Admin\Report\DeleteReportRequest;
use Illuminate\Http\Request;

interface AdminReportInterface {
    public function reportsListing(Request $request);
    public function getReportDatail(GetReportDatailRequest $request);
    public function deleteReport(DeleteReportRequest $request);   
}

