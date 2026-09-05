<?php

namespace App\Http\Controllers\API\CustomerController;

use App\Http\Controllers\Controller;
use App\Models\CustomerBanks;
use App\Models\Customers;
use App\Models\IfscMaster;
use App\Services\Verification\BankService;
use App\Services\Verification\IfscCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CustomerBankController extends Controller
{
    public function saveCustomerBank(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'customer_id' => 'required',
            'account_number' => 'required',
            'ifsc_code' => 'required',
            'bank_id' => 'required',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => [],
            ], 422);
        }

        $exists = CustomerBanks::where(
            'account_no_hash',
            hash('sha256', $request->account_no)
        )
            ->where('ifsc', $request->ifsc)
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => false,
                'message' => 'Bank account already exists',
                'data' => [],
            ], 422);
        }
        DB::beginTransaction();

        try {

            $exists = Customers::where('id', $request->customer_id)->exists();

            if (! $exists) {
                return response()->json([
                    'status' => false,
                    'message' => 'Customer Not Found',
                    'data' => [],
                ], 404);
            }

            $IfscMaster = IfscMaster::where('ifsc', $request->ifsc_code)->first();
            if (! $IfscMaster) {
                return response()->json([
                    'status' => false,
                    'message' => 'IFSC Code Not Found',
                    'data' => [],
                ], 404);
            }

            $response = app(BankService::class)->verify(
                $request->account_number,
                $request->ifsc_code
            );

            if ($response['status'] == false) {
                return response()->json($response, 422);
            }

            if ($response['data']['isValid'] == true) {

                CustomerBanks::create([
                    'customer_id' => $request->customer_id,
                    'bank_id' => $request->bank_id,
                    'ifsc_master_id' => $IfscMaster->id,
                    'account_holder_name' => $response['data']['accountName'],
                    'account_no' => $response['data']['accountNumber'],
                    'account_no_last4' => substr($response['data']['accountNumber'], -4),
                    'ifsc' => $response['data']['ifsc'],
                    'bank_name' => null,
                    'status_as_per_source' => $response['data']['statusAsPerSource'] ?? 'NA',
                    'bank_response' => $response['data']['bankResponse'],
                    'bank_rrn' => $response['data']['bankRRN'],
                    'status_code' => $response['data']['statusCode'],
                    'karza_request_id' => $response['data']['requestId'],
                    'account_no_hash' => hash('sha256', $response['data']['accountNumber']),
                    'user_id' => auth()->id(),
                    'api_response' => json_encode($response['apiResponse']),
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid IFSC CODE or Account No. or no data found',
                    'data' => '',
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Bank account saved successfully',
                'data' => $response['data'],
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function getCustomerBank(Request $request, int $id)
    {
        $data = CustomerBanks::where('customer_id', $id)->get();

        return response()->json([
            'status' => true,
            'data' => $data,
            'message' => 'Load Successfully',
        ]);
    }

    public function checkIfscCode(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'ifsc_code' => 'required',

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

            $ifscDetails = IfscMaster::where('ifsc', $request->ifsc_code)->first();

            if ($ifscDetails) {
                return response()->json([
                    'status' => true,
                    'message' => 'Load Successfully',
                    'data' => $ifscDetails,
                ], 200);
            }

            $response = app(IfscCodeService::class)->verify(
                $request->ifsc_code
            );

            DB::commit();

            return $response;
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'data' => [],
            ], 500);
        }
    }
}
