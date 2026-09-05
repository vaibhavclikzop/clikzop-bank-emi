<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\SendOtpRequest;
use App\Models\LoginLogs;
use App\Services\Sms\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Jenssegers\Agent\Agent;

class AuthController extends Controller
{
    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => [],
            ], 401);
        }
        // $response = Http::get("https://nominatim.openstreetmap.org/reverse", [
        //     'lat' => $request->latitude,
        //     'lon' => $request->longitude,
        //     'format' => 'json'
        // ]);

        // $data = $response->json();
        $data = collect();

        $city = $data['address']['city'] ?? $data['address']['town'] ?? null;
        $state = $data['address']['state'] ?? null;
        $country = $data['address']['country'] ?? null;

        if (! Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        $user = Auth::user();

        try {
            // if ($user->role !== 'super_admin') {
            //     return response()->json([
            //         'status' => false,
            //         'message' => 'Only Super Admin allowed'
            //     ], 403);
            // }
            $tokenResult = $user->createToken('admin_token');

            $tokenModel = $tokenResult->accessToken;
            $tokenModel->expires_at = now()->addHours(24);
            $tokenModel->save();

            $token = $tokenResult->plainTextToken;
            $user->remember_token = $token;

            $user->save();

            $agent = new Agent;
            $agent->setUserAgent($request->header('User-Agent'));

            $device = $agent->isMobile() ? 'Mobile' : ($agent->isTablet() ? 'Tablet' : 'Desktop');

            $browser = $agent->browser();
            $platform = $agent->platform();

            LoginLogs::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
                'device' => $device,
                'browser' => $browser,
                'platform' => $platform,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'city' => $city,
                'state' => $state,
                'country' => $country,
                'session_id' => $token,
                'status' => 1,
                'login_at' => now(),
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Super Admin Login',
                'token' => $token,
                'data' => $user,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'token' => '',
                'data' => [],
            ]);
        }
    }

    public function Logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully',
        ]);
    }

    public function getProfileDetails(Request $request)
    {

        $user = $request->user();
        if ($user) {
            return response()->json([
                'status' => true,
                'message' => 'Profile fetched successfully',
                'data' => $user,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Session expired or something went wrong',
                'data' => [],
            ]);
        }
    }

    public function updateProfile(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => [],
            ], 401);
        }
        $user = $request->user();
        if ($user) {
            $user->name = $request->name;
            $user->company_name = $request->company_name;
            $user->gst_in = $request->gst_in;
            $user->state = $request->state;
            $user->district = $request->district;
            $user->city = $request->city;
            $user->address = $request->address;
            $user->pincode = $request->pincode;
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Profile fetched successfully',
                'data' => $user,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Session expired or something went wrong',
                'data' => [],
            ]);
        }
    }

    public function __construct(
        protected OtpService $otpService
    ) {}

    public function sendOTP(SendOtpRequest $request): JsonResponse
    {

        $result = $this->otpService->send(
            $request->validated('mobile'),
            $request->validated('purpose')
        );

        return response()->json([
            'status' => true,
            'message' => 'OTP sent successfully.',
            'data' => [
                'verification_id' => $result['verification_id'],
                'expires_at' => $result['expires_at'],
            ],
        ]);
    }

    public function verifyOTP(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'verification_id' => 'required|uuid',
            'otp' => 'required|digits:6',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => [],
            ], 422);
        }


        try {

            $result = $this->otpService->verify(
                $request->verification_id,
                $request->otp
            );

            return response()->json([
                'status' => true,
                'message' => 'OTP verified successfully.',
                'data' => $result,
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                'status' => false,
                'message' => $e->validator->errors()->first(),
                'data' => [],
            ], 422);
        }
    }
}
