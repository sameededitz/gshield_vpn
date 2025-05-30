<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BillingAddressController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|min:3',
            'address' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'postal_code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->all()
            ], 400);
        }

        /** @var \App\Models\User $user **/
        $user = Auth::user();
        $user->billingAddress()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'full_name' => $request->name,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'postal_code' => $request->postal_code,
            ]
        );

        return response()->json([
            'status' => true,
            'message' => 'Billing address updated successfully!',
            'user' => new UserResource($user->load('billingAddress')),
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        $user = Auth::user();
        return response()->json([
            'status' => true,
            'user' => new UserResource($user->load('billingAddress')),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy()
    {
        /** @var \App\Models\User $user **/
        $user = Auth::user();
        $user->billingAddress()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Billing address deleted successfully!',
        ], 200);
    }
}
