<?php

namespace App\Interfaces;

use App\Http\Requests\Chats\GetPersonalChatRequest;
use App\Http\Requests\Chats\SendMessageRequest;
use Illuminate\Http\Request;

interface ChatInterface
{
    public function getPersonalChat(GetPersonalChatRequest $request);
    public function getInboxChat(Request $request);
    public function sendMessage(SendMessageRequest $request);
}
