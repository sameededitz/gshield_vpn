<?php

namespace App\Http\Controllers\Api;

use App\Models\Plan;
use App\Models\Option;
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

    public function options()
    {
        $options = [
            'tos' => Option::where('key', 'tos')->first()->value ?? '',
            'privacy_policy' => Option::where('key', 'privacy_policy')->first()->value ?? '',
            'about_us' => Option::where('key', 'about_us')->first()->value ?? '',
        ];

        return response()->json([
            'tos' => $options['tos'],
            'privacy_policy' => $options['privacy_policy'],
            'about_us' => $options['about_us'],
        ]);
    }

    public function registerClient(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ip' => 'required|ip',
            'client_name' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->all()
            ], 422);
        }

        $ip = $request->ip;
        $clientName = $request->client_name;
        $password = $request->password;

        $checkURL = "http://{$ip}:5000/api/ikev2/clients/{$clientName}";
        $registerURL = "http://{$ip}:5000/api/ikev2/clients/generate";

        $headers = [
            'X-API-TOKEN' => env('VPS_API_TOKEN'),
            'Content-Type' => 'application/json',
        ];

        try {
            // Step 1: Try registering
            $registerResponse = Http::withHeaders($headers)
                ->timeout(10)
                ->post($registerURL, [
                    'name' => $clientName,
                    'password' => $password,
                ]);

            if ($registerResponse->successful()) {
                return response()->json([
                    'connected' => true,
                    'message' => 'Client successfully registered.',
                    'data' => $registerResponse->json(),
                ]);
            }

            // Step 2: If failed with 500, delete and retry
            if ($registerResponse->status() === 500) {
                $deleteResponse = Http::withHeaders($headers)
                    ->timeout(5)
                    ->delete($checkURL);

                if ($deleteResponse->successful()) {
                    // Retry registration
                    $retryRegister = Http::withHeaders($headers)
                        ->timeout(10)
                        ->post($registerURL, [
                            'name' => $clientName,
                            'password' => $password,
                        ]);

                    if ($retryRegister->successful()) {
                        return response()->json([
                            'connected' => true,
                            'message' => 'Client registered after retry.',
                            'data' => $retryRegister->json(),
                        ]);
                    }

                    return response()->json([
                        'connected' => false,
                        'message' => 'Registration failed after retry.',
                        'retry_status' => $retryRegister->status(),
                    ], 500);
                }
            }
            return response()->json([
                'connected' => false,
                'message' => 'Initial registration failed.',
                'status' => $registerResponse->status(),
            ], $registerResponse->status());
        } catch (\Exception $e) {
            return response()->json([
                'connected' => false,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage(),
            ], 500);
        }
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
