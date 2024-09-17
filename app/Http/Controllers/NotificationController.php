<?php

namespace App\Http\Controllers;

use App\Services\FcmService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    private $fcmService;

    public function __construct(FcmService $fcmService)
    {
        $this->fcmService = $fcmService;
    }

    public function sendNotification(Request $request)
    {
        $deviceToken = $request->input('device_token');
        $title = $request->input('title');
        $body = $request->input('body');

        $response = $this->fcmService->sendNotification($deviceToken, $title, $body);

        return response()->json($response);
    }
}
