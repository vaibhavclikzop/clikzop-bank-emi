<?php

namespace App\Http\Controllers\API\LoanController;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Services\Banking\BankingUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoanBankingUploadController extends Controller
{
    public function __construct(
        protected BankingUploadService $BankingUploadService
    ) {}
    public function initiateBankStatement(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'loan_id' => 'required',
            'bank_institute_id' => 'required',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => [],
            ], 422);
        }


        $loan =   Loan::where("id", $request->loan_id)->first();
        if (!$loan) {
            return response()->json([
                'status' => false,
                'message' => 'Loan not found',
                'data' => [],
            ], 404);
        }

        $result = $this->BankingUploadService->initiate(
            $request->loan_id,
            $request->bank_institute_id
        );

        if ($result["status"] == true) {
            return response()->json([
                'status' => true,
                'message' => 'Transaction ID created successfully',
                'transactionID' => $result["transactionID"],
                'data' => [],
            ], 200);
        }
        
    }
}
