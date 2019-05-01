<?php

namespace App\Interfaces;
use App\Http\Requests\Stories\ViewStoryRequest;
use App\Http\Requests\CommonStoryRequest;
use App\Http\Requests\Stories\SaveAdditionalInfoRequest;
use App\Http\Requests\Stories\AddStoryRequest;
use App\Http\Requests\Stories\AddEpisodeRequest;
use App\Http\Requests\Stories\StoryReportRequest;

use Illuminate\Http\Request;

interface StoryInterface
{
    public function saveAdditionalInfo(SaveAdditionalInfoRequest $request);
    public function getAllGenres(Request $request);
    public function saveGenres(Request $request);
    public function getMyGenres(Request $request);
    public function getStories(Request $request);
    public function saveStory(CommonStoryRequest $request);
    public function getWritters(Request $request);
    public function viewStory(ViewStoryRequest $request);
    public function getHomePage(Request $request);
    public function getStoryDetail(CommonStoryRequest $request);
    public function addOrdeleteDownload(CommonStoryRequest $request);
    public function addStory(AddStoryRequest $request);
    public function addEpisode(AddEpisodeRequest $request);
    public function storyReport(StoryReportRequest $request);
    public function deleteStory(CommonStoryRequest $request);
    public function approve(CommonStoryRequest $request);    
    public function weeklyPayment(Request $request);    
}
