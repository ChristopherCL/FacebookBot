<?php
use App\Http\Controllers\BotManController;

$botman = resolve('botman');

$botman->hears('[a-zA-Z0-9_.-]*', BotManController::class.'@startTestConversation');
