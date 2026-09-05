<?php

namespace App\Http\Controllers\API\LoanController;

use App\Http\Controllers\Controller;
use App\Models\CommonDocument;
use App\Models\Customers;
use App\Models\Documents;
use App\Models\Loan;
use App\Models\Loan_Statuses;
use App\Models\LoanApplicants;
use App\Models\loanBankingMst;
use App\Models\LoanCibilDataMst;
use App\Models\LoanDocument;
use App\Models\LoanGST\LoanGSTDetails;
use App\Models\LoanGSTRunningYearDet;
use App\Models\LoanGSTRunningYearMst;
use App\Models\LoanITRProfile;
use App\Models\loanLipReportDet;
use App\Models\loanLipReportMst;
use App\Models\LoanVanillaReportSummary;
use App\Services\LoanReports\VanillaReportService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class LoanController extends Controller
{
    use AuthorizesRequests;

    public function saveLoan(Request $request)
    {
        $this->authorize('create', Customers::class);
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required',
            'loan_type_id' => 'required',
            'loan_amount' => 'required',
            'tenure' => 'required',
            'income_type_id' => 'required',
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
            $Customers = Customers::where('id', $request->customer_id)->exists();
            if (! $Customers) {
                return response()->json([
                    'status' => false,
                    'message' => 'Customer not found',
                    'data' => [],
                ], 500);
            }

            $loan = Loan::create([
                'customer_id' => $request->customer_id,
                'user_id' => auth()->user()->id,
                'tenant_id' => auth()->user()->tenant_id,
                'loan_type_id' => $request->loan_type_id,
                'income_type_id' => $request->income_type_id,
                'loan_amount' => $request->loan_amount,
                'tenure_months' => $request->tenure,
                'loan_purpose' => $request->loan_purpose,
                'status_id' => 1,
            ]);

            $commonDocument = CommonDocument::get();
            foreach ($commonDocument as $key => $value) {
                LoanDocument::create([
                    'loan_id' => $loan->id,
                    'name' => $value->name,
                    'document_id' => $value->id,
                    'document_type' => 'common_document',
                ]);
            }

            $documents = Documents::where('income_type_id', $request->income_type_id)->get();
            foreach ($documents as $key => $value) {
                LoanDocument::create([
                    'loan_id' => $loan->id,
                    'name' => $value->name,
                    'document_id' => $value->id,
                    'document_type' => 'document',
                ]);
            }

            $loanLipReportMst = loanLipReportMst::firstOrCreate([
                "loan_id" => $loan->id,
            ]);

            loanLipReportDet::firstOrCreate([
                "loan_id" => $loan->id,
                "loan_lip_report_mst_id" => $loanLipReportMst->id,
                "customer_id" => $request->customer_id,
            ]);

            LoanApplicants::create([
                "loan_id" => $loan->id,
                "customer_id" => $request->customer_id,
                "applicant_type" => "applicant",
                "financial_status" => "financial",
                "relationship_id" => 1,
                "is_primary" => 1,
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Save successfully',
                'data' => $loan,
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

    public function getLoan(Request $request, $offset, $limit, $statusID = null)
    {
        $this->authorize('viewAny', Loan::class);
        try {
            $totalRecords = Loan::count();
            $query = Loan::with([
                'customer:id,name',
                'status:id,name',
                'loanType:id,name',
                'incomeType:id,name',
            ]);

            if ($statusID) {
                $query->where('status_id', $statusID);
            }

            $data = $query->offset($offset)
                ->limit($limit)
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Load Successfully',
                'data' => $data,
                'pagination' => [
                    'total_records' => $totalRecords,
                    'offset' => (int) $offset,
                    'limit' => (int) $limit,
                    'current_page' => floor($offset / $limit) + 1,
                    'total_pages' => ceil($totalRecords / $limit),
                    'has_more' => ($offset + $limit) < $totalRecords,
                ],
            ], 200);
        } catch (\Throwable $th) {

            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function getStatus(Request $request)
    {
        try {

            $data = Loan_Statuses::get();

            return response()->json([
                'status' => true,
                'message' => 'Load Successfully',
                'data' => $data,

            ], 200);
        } catch (\Throwable $th) {

            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function getLoanCheckList(Request $request, $id)
    {
        $this->authorize('viewAny', LoanDocument::class);
        try {

            $data = LoanDocument::with('userDetails:id,name')
                ->select(
                    'id',
                    'name',

                    'mime_type',
                    'file_size',
                    'uploaded_by'
                )
                ->selectRaw('file_path IS NOT NULL AS is_uploaded')
                ->where('loan_id', $id)
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Load Successfully',
                'data' => $data,

            ], 200);
        } catch (\Throwable $th) {

            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function uploadChecklistDocument(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'loan_id' => 'required|exists:loans,id',
            'loan_document_id' => 'required|exists:loan_documents,id',
            'file' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120',
            ],
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

            $loan = Loan::where('id', $request->loan_id)->first();

            // Policy
            $this->authorize('update', $loan);

            $file = $request->file('file');

            $uuid = Str::uuid();

            $extension = $file->getClientOriginalExtension();

            $fileName = $uuid . '.' . $extension;

            $folder = "loans/{$loan->id}_{$loan->loan_number}";

            $path = $file->storeAs(
                $folder,
                $fileName,
                'local'
            );

            $hash = hash_file(
                'sha256',
                $file->getRealPath()
            );

            $loanDocument = LoanDocument::where('id', $request->loan_document_id)->first();

            $loanDocument->update([
                'loan_id' => $loan->id,
                'file_name' => $fileName,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'file_path' => $path,
                'uploaded_by' => auth()->id(),
                'file_hash' => $hash,
            ]);
            $loanDocument->load('userDetails:id,name');
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Document uploaded successfully',
                'data' => [
                    'id' => $loanDocument->id,
                    'user_details' => $loanDocument->userDetails,
                ],
            ]);
        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function getUploadedDocument(Request $request, $id)
    {

        try {

            $data = LoanDocument::with('userDetails:id,name')
                ->select(
                    'id',
                    'name',
                    'file_path',
                    'mime_type',
                    'file_size',
                    'uploaded_by'
                )
                ->selectRaw('file_path IS NOT NULL AS is_uploaded')
                ->where('id', $id)
                ->first();

            return Storage::disk('local')->download(
                $data->file_path,
                $data->original_name
            );

            return response()->json([
                'status' => true,
                'message' => 'Load Successfully',
                'data' => $data,

            ], 200);
        } catch (\Throwable $th) {

            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function getLoanDetails(Request $request, int $id)
    {
        $this->authorize('viewAny', Loan::class);
        try {

            $loadDetails = Loan::with([
                "loanApplicants.applicantDetails",
                "loanApplicants.itrDetails.itrYearlyDetails.itrYearlyFinancial",
                "loanApplicants.itrDetails.itrFillingHistory",
                "loanApplicants.itrDetails.itrOutStandingDemands",

                'status:id,name',
                'loanType:id,name',
                'incomeType:id,name',
            ])->where('id', $id)->first();

            $loadDetails->customer?->makeHidden('number');
            $loanCheckList = LoanDocument::with('userDetails:id,name')
                ->select(
                    'id',
                    'name',

                    'mime_type',
                    'file_size',
                    'uploaded_by'
                )
                ->selectRaw('file_path IS NOT NULL AS is_uploaded')
                ->where('loan_id', $id)
                ->get();

            $loanCibilData = LoanCibilDataMst::with('bankDetails', 'loanType', 'cibilStatus', 'accountType', 'userDetails:id,name')->where('loan_id', $id)->get()
                ->makeHidden([
                    'file',
                    'json_file',
                    'raw_data',

                ]);
            $rteDetails = LoanCibilDataMst::select(
                'loan_id',
                'loan_account_no',
                'bank_id',
                'loan_type_id',
                'tenure',
                'tenure_left',
                'emi_amount'
            )
                ->with([
                    'loanDetails:id,customer_id',
                    'loanDetails.customer:id,name',
                    'bankDetails:id,name',
                    'loanType:id,name',
                ])
                ->where('loan_id', $id)
                ->get()
                ->map(function ($item) {
                    return [
                        'loan_account_no' => $item->loan_account_no,
                        'customer_name' => $item->loanDetails?->customer?->name,
                        'bank_name' => $item->bankDetails?->name,
                        'loan_type' => $item->loanType?->name,
                        'tenure' => $item->tenure,
                        'tenure_left' => $item->tenure_left,
                        'tenure_paid' => $item->tenure - $item->tenure_left,
                        'emi_amount' => $item->emi_amount,
                    ];
                });

            $LoanGSTRunningDetails = LoanGSTRunningYearMst::with('gstRunningYearDetails')->where('loan_id', $id)->get();
            $bankingDetails = loanBankingMst::with("monthDetails")->where("loan_id", $id)->get();
            $rtrDetails = LoanCibilDataMst::select('id', 'loan_account_no', 'tenure', 'tenure_left', 'emi_amount', 'bank_id', 'loan_type_id')
                ->with([
                    'bankDetails:id,name',
                    'loanType:id,name'
                ])
                ->where('loan_id', $id)
                ->get();

            $vanillaReport = app(VanillaReportService::class)->vanillaReport(
                $id
            );

            if ($vanillaReport["status"] == true) {
                $vanillaReport = $vanillaReport["data"];
            } else {
                $vanillaReport = $vanillaReport;
            }

            $loanITRProfile = LoanITRProfile::where("loan_id", $id)->get();
            $loanGSTDetails = LoanGSTDetails::with("gstr3bDetails")->where("loan_id", $id)->get();

            $data['loadDetails'] = $loadDetails;
            // $data['loanCheckList'] = $loanCheckList;
            // $data['loanITRProfile'] = $loanITRProfile;
            // $data['loanCibilData'] = $loanCibilData;
            // $data['rtrDetails'] = $rteDetails;
            // $data['loanGSTDetails'] = $loanGSTDetails;
            // $data['bankingDetails'] = $bankingDetails;
            // $data['rtrDetails'] = $rtrDetails;
            // $data['vanillaReport'] = $vanillaReport;


            return response()->json([
                'status' => true,
                'message' => 'Load Successfully',
                'data' => $data,

            ], 200);
        } catch (\Throwable $th) {

            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }





    public function saveCibilData(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'loan_id' => 'required',
            'bank_id' => 'required',
            'loan_account_no' => 'required',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => [],
            ], 422);
        }
        $loan = Loan::where('id', $request->loan_id)->first();
        if (! $loan) {
            return response()->json([
                'status' => false,
                'message' => 'Loan not found',
                'data' => [],
            ], 422);
        }
        $this->authorize('update', $loan);

        DB::beginTransaction();

        try {

            $data = LoanCibilDataMst::create([

                'user_id' => auth()->user()->id,
                'loan_id' => $request->loan_id,
                'bank_id' => $request->bank_id,
                'loan_account_no' => $request->loan_account_no,
                'loan_amount' => $request->loan_amount,
                'outstanding_amount' => $request->outstanding_amount,
                'start_date' => $request->start_date,
                'loan_type_id' => $request->loan_type_id,
                'tenure' => $request->tenure,
                'tenure_left' => $request->tenure_left,
                'cibil_status_id' => $request->cibil_status_id,
                'overdue' => $request->overdue,
                'emi_bank_name' => $request->emi_bank_name,
                'account_type_id' => $request->account_type_id,
                'paid_account_no' => $request->paid_account_no,
                'emi_amount' => $request->emi_amount,
                'obligate_amount' => $request->obligate_amount,

            ]);
            $LoanVanillaReportSummary = LoanVanillaReportSummary::where("loan_id", $request->loan_id)->first();
            $LoanVanillaReportSummary->appraised_obligations = $request->obligate_amount ?? 0;
            $LoanVanillaReportSummary->update();

            updateVanillaReportSummary($request->loan_id);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Save successfully',
                'data' => $data,
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

    public function saveGSTRunningYear(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'loan_id' => 'required',
            'company' => 'required',
            'year' => 'required',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => [],
            ], 422);
        }
        $loan = Loan::where('id', $request->loan_id)->first();
        if (! $loan) {
            return response()->json([
                'status' => false,
                'message' => 'Loan not found',
                'data' => [],
            ], 422);
        }

        $check = LoanGSTRunningYearMst::where('loan_id', $request->loan_id)->where('year', $request->year)->first();
        if ($check) {
            return response()->json([
                'status' => false,
                'message' => 'This year GST already added.',
                'data' => [],
            ], 422);
        }
        $this->authorize('update', $loan);

        DB::beginTransaction();

        try {

            $data = LoanGSTRunningYearMst::create([

                'user_id' => auth()->user()->id,
                'loan_id' => $request->loan_id,
                'company' => $request->company,
                'location' => $request->location,
                'year' => $request->year,
                'financial_year' => $request->year . '-' . substr($request->year + 1, -2),
            ]);

            $months = [
                4 => 'April',
                5 => 'May',
                6 => 'June',
                7 => 'July',
                8 => 'August',
                9 => 'September',
                10 => 'October',
                11 => 'November',
                12 => 'December',
                1 => 'January',
                2 => 'February',
                3 => 'March',
            ];

            foreach ($months as $month => $monthName) {

                LoanGSTRunningYearDet::create([
                    'user_id' => auth()->id(),
                    'loan_id' => $request->loan_id,
                    'mst_id' => $data->id,
                    'month' => $month,
                    'month_name' => $monthName,
                    'amount' => 0,
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Save successfully',
                'data' => $data,
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

    public function saveGSTRunningYearAmount(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'amount' => 'required',
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

            $detail = LoanGSTRunningYearDet::find($request->id);

            if (! $detail) {
                return response()->json([
                    'status' => false,
                    'message' => 'Record not found',
                ], 404);
            }

            $detail->update([
                'amount' => $request->amount,
            ]);

            $amount = LoanGSTRunningYearDet::where('mst_id', $detail->mst_id)->sum('amount');

            LoanGSTRunningYearMst::where('loan_id', $detail->loan_id)->where('id', $detail->mst_id)->update([
                'total_turnover' => $amount,
                'average_turnover' => $amount / 7,
            ]);

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
