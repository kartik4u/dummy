<?php

namespace App\Interfaces;

use App\Http\Requests\Admin\Faq\CreateFaqRequest;
use App\Http\Requests\Admin\Faq\EditFaqRequest;
use App\Http\Requests\Admin\Page\GetPageRequest;
use App\Http\Requests\Admin\Page\UpdatePageRequest;
use App\Http\Requests\Admin\Faq\ViewFaqRequest;
use App\Http\Requests\Admin\Faq\DeleteFaqRequest;





use Illuminate\Http\Request;

interface AdminPagesInterface {

    public function getFaqs(Request $request);

    public function viewFaq(ViewFaqRequest $request);

    public function createFaq(CreateFaqRequest $request);

    public function editFaq(EditFaqRequest $request);

    public function deleteFaq(DeleteFaqRequest $request);

    public function getPage(GetPageRequest $request);

    public function getPages(Request $request);

    public function updatePage(UpdatePageRequest $request);
}   

