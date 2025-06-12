<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AppleSubscriptionController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('Apple notification received', $request->all());

        // Parse and store Apple's notification data (signedPayload for V2)

        return response()->json(['status' => 'ok'], 200);
    }
}
