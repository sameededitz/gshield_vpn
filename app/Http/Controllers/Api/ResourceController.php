<?php

namespace App\Http\Controllers\Api;

use App\Models\Plan;
use App\Models\Server;
use App\Models\VpsServer;
use App\Models\UserFeedback;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Http\Resources\ServerResource;
use App\Http\Resources\VpsServerResource;
use Illuminate\Support\Facades\Validator;

class ResourceController extends Controller
{
    public function servers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'platform' => 'required|string|in:android,ios,macos,windows',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->all()
            ], 400);
        }

        $servers = Server::where($request->platform, true)->with(['subServers.vpsServer'])->get();

        return response()->json([
            'status' => true,
            'servers' => ServerResource::collection($servers),
        ]);
    }
    public function vpsServers()
    {
        $servers = VpsServer::all();

        return response()->json([
            'status' => true,
            'servers' => VpsServerResource::collection($servers),
        ]);
    }

    public function plans()
    {
        $plans = Plan::all();

        return response()->json([
            'status' => true,
            'plans' => $plans,
        ]);
    }
    
    public function addFeedback(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'email' => 'required|email',
            'message' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->all()
            ], 400);
        }


        $feedback = UserFeedback::create([
            'subject' => $request->subject,
            'email' => $request->email,
            'message' => $request->message,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Feedback added successfully',
            'feedback' => $feedback,
        ], 201);
    }

    public function nearestServer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'platform' => 'required|string|in:android,ios,macos,windows',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->all()
            ], 422);
        }

        $platform = $request->platform;
        $ip = $request->ip();
        $locationData = Http::get("http://ip-api.com/json/{$ip}")->json();

        if (!isset($locationData['lat']) || !isset($locationData['lon'])) {
            return response()->json(['error' => 'Could not determine location'], 422);
        }

        $userLat = $locationData['lat'];
        $userLon = $locationData['lon'];

        // Fetch all servers and filter based on platform
        $servers = Server::where($platform, true)->get(); // Get only servers supporting the platform

        if ($servers->isEmpty()) {
            return response()->json(['error' => 'No servers available for this platform'], 404);
        }

        // Separate free and premium servers
        $freeServers = $servers->where('type', 'free');
        $premiumServers = $servers->where('type', 'premium');

        // Find the closest free server
        $closestFreeServer = $freeServers->map(function ($server) use ($userLat, $userLon) {
            $server->latitude = (float) $server->latitude;
            $server->longitude = (float) $server->longitude;
            $server->distance_km = $this->haversineDistance($userLat, $userLon, $server->latitude, $server->longitude);
            return $server;
        })->sortBy('distance_km')->first();

        // Find the closest premium server
        $closestPremiumServer = $premiumServers->map(function ($server) use ($userLat, $userLon) {
            $server->latitude = (float) $server->latitude;
            $server->longitude = (float) $server->longitude;
            $server->distance_km = $this->haversineDistance($userLat, $userLon, $server->latitude, $server->longitude);
            return $server;
        })->sortBy('distance_km')->first();

        return response()->json([
            'status' => true,
            'free' => $closestFreeServer,
            'server' => $closestPremiumServer,
        ]);
    }
    private function haversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Earth's radius in KM

        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        $dLat = $lat2 - $lat1;
        $dLon = $lon2 - $lon1;

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos($lat1) * cos($lat2) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c; // Distance in KM
    }

}
