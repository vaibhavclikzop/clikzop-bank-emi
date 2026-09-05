<?php

namespace App\Http\Controllers\API\LoanController;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\LoanApplicants;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CoApplicantController extends Controller
{
    public function saveCoApplicant(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'loan_id' => 'required',
            'customer_id' => 'required',
            'financial_status' => 'required',
            'relationship_id' => 'required',

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
            $Customers = Loan::where('id', $request->loan_id)->exists();
            if (! $Customers) {
                return response()->json([
                    'status' => false,
                    'message' => 'Loan not found',
                    'data' => [],
                ], 500);
            }

            LoanApplicants::updateOrCreate(
                [
                    "customer_id" => $request->customer_id,
                    "loan_id" => $request->loan_id,
                ],
                [

                    "applicant_type" => "co_applicant",
                    "financial_status" => $request->financial_status,
                    "relationship_id" => $request->relationship_id,
                    "is_primary" => 0,
                ]
            );


            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Save successfully',
                'data' => [],
            ], 201);
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
