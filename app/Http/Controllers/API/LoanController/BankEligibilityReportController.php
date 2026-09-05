<?php

namespace App\Http\Controllers\API\LoanController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BankEligibilityReportController extends Controller
{
    public function generateBankEligibility(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'loan_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => [],
            ], 422);
        }
    }
}
