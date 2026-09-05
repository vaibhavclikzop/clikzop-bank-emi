<?php

namespace App\Http\Controllers\API\CustomerController;

use App\Http\Controllers\Controller;
use App\Models\customerGstDetails;
use App\Models\LoanGST\LoanGSTDetails;
use App\Models\webhook;
use App\Services\GST\GSTReturnService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class GSTController extends Controller
{
    public function sendOTPForGstFilling(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'username' => 'required',
            'gst' => 'required',
            'customer_id' => 'required',
            'loan_id' => 'required',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => [],
            ], 422);
        }

        $response = app(GSTReturnService::class)->sendOtp(
            $request->username,
            $request->gst
        );

        return $response;
    }

    public function verifyOTPForGstFilling(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required',
            'gst' => 'required',
            'customer_id' => 'required',
            'request_id' => 'required',
            'loan_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => [],
            ], 422);
        }
        DB::beginTransaction();

        try {
            $exists = LoanGSTDetails::where('gst_in', $request->gst)->exists();

            // if ($exists) {
            //     return response()->json([
            //         'status' => false,
            //         'message' => 'GST already added',
            //         'data' => [],
            //     ], 200);
            // }

            // if ($request->request_id != "TESTINGKEY") {
            //     return response()->json([
            //         'status' => false,
            //         'message' => "Testing key not verified",
            //         'data' => [],
            //     ], 200);
            // } else {
            //     $response["message"] = "Save successfully";
            // }
            $response = app(GSTReturnService::class)->verifyOTP(
                $request->otp,
                $request->request_id
            );
            $LoanGSTDetails = LoanGSTDetails::create([
                "customer_id" => $request->customer_id,
                "loan_id" => $request->loan_id,
                "gst_in" => $request->gst,
                "user_id" => auth()->user()->id,
            ]);

            // webhook::create([
            //     "loan_gst_details_id" => $LoanGSTDetails->id,
            //     'provider'   => 'KARZA',
            //     'request_id' => $request->request_id ?? null,
            //     'event'      => $payload['event'] ?? null,
            //     'method'     => $request->method(),
            //     'ip_address' => $request->ip(),
            //     'headers' => json_encode($request->headers->all()),
            //     'payload'    => "testing.json",
            //     'http_status' => 200,

            // ]);
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => $response["message"],
                'data' => $response,
            ], 200);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([
                'status' => true,
                'message' => $th->getMessage(),
                'data' => [],
            ], 422);
        }
    }
}
