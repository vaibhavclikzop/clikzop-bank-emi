<?php

namespace App\Http\Controllers\API\SuperAdminController\Masters;

use App\Http\Controllers\Controller;
use App\Models\CommonDocument;
use App\Models\CustomerBanks;
use App\Models\CustomerCompanyDetails;
use App\Models\customerCourtSummary;
use App\Models\CustomerDocuments;
use App\Models\customerGstDetails;
use App\Models\CustomerITRProfile;
use App\Models\Customers;
use App\Models\Documents;
use App\Models\Loan;
use App\Models\LoanDocument;
use App\Services\Verification\VerificationManager;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\String\TruncateMode;

class CustomerController extends Controller
{
    use AuthorizesRequests;

    public function checkMobileNumber(Request $request)
    {
        $this->authorize('viewAny', Customers::class);

        $validator = Validator::make($request->all(), [
            'number' => 'required|digits:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => [],
            ], 422);
        }
        $query = Customers::where('number', $request->number);

        if (auth()->user()->role !== 'super_admin') {
            $query->where('tenant_id', auth()->user()->tenant_id);
        }

        $data = $query->first();
        if ($data) {
            return response()->json([
                'status' => true,
                'message' => 'Load Successfully',
                'data' => $data,
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No Customer Found',
                'data' => [],
            ], 404);
        }
    }

    public function saveCustomer(Request $request)
    {

        $this->authorize('create', Customers::class);

        $validator = Validator::make($request->all(), [
            'number' => 'required|digits:10|unique:customers,number',
            'name' => 'required',
            'email' => 'required|Email',
            'dob' => 'required',
            'signup_document_type' => 'required|in:aadhaar,pan,driving_license,voter_id,passport',
            'signup_document' => 'required',
            'loan_type_id' => 'required',
            'loan_amount' => 'required',
            'tenure' => 'required',
            'pan_no' => 'required',

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
            $customer = Customers::create([
                'name' => $request->name,
                'number' => $request->number,
                'email' => $request->email,
                'dob' => $request->dob,

                'state' => $request->state,
                'district' => $request->district,
                'city' => $request->city,
                'address' => $request->address,
                'country' => $request->country,
                'pincode' => $request->pincode,

                'signup_document_type' => $request->signup_document_type,
                'signup_document' => $request->signup_document,
                'pan_no' => $request->pan_no,
                'pan_verified' => 1,

                'created_by' => auth()->user()->id,
                'user_id' => auth()->user()->id,
                'tenant_id' => auth()->user()->tenant_id,
            ]);

            $loan = Loan::create([
                'customer_id' => $customer->id,
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

                CustomerDocuments::create([
                    'customer_id' => $customer->id,
                    'document_id' => $value->id,
                    'document_name' => $value->name,
                ]);
            }

            $document = CustomerDocuments::where("customer_id", $customer->id)->where("document_id", 2)->first();
            $document->document_no = $request->pan_no;
            $document->document_no_last4 = substr($request->pan_no, -4);
            $document->country = $request->country ?? 'NA';
            $document->state = $request->state ?? 'NA';
            $document->city = $request->city ?? 'NA';
            $document->address = $request->address ?? 'NA';
            $document->pincode = $request->pincode ?? 'NA';
            $document->is_verified = 1;
            $document->verified_at = now();
            $document->user_id = auth()->id();
            $document->save();


            $documents = Documents::where('income_type_id', $request->income_type_id)->get();
            foreach ($documents as $key => $value) {
                LoanDocument::create([
                    'loan_id' => $loan->id,
                    'name' => $value->name,
                    'document_id' => $value->id,
                    'document_type' => 'document',
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Customer created successfully',
                'data' => $customer,
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

    public function checkDocumentVerification(Request $request)
    {
        $this->authorize('viewAny', Customers::class);

        $validator = Validator::make($request->all(), [

            'signup_document_type' => 'required|in:aadhaar,pan,driving_license,voter_id,passport',
            'signup_document' => 'required',


        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => [],
            ], 422);
        }
        try {

            $result = app(VerificationManager::class)
                ->verify(
                    $request->signup_document_type,
                    $request->signup_document,
                    $request->dob
                );

            return $result;
        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getCustomers(Request $request, $offset, $limit)
    {
        try {
            $totalRecords = Customers::count();
            $data = Customers::offset($offset)
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

    public function getCustomerDetails(int $id)
    {
        $this->authorize('viewAny', Customers::class);



        $customerDetails = Customers::with('userDetails:id,name')->where('id', $id)->first();
        if (!$customerDetails) {
            return response()->json([
                'status' => false,
                'message' => 'Customer not found',
                'data' => [],
            ], 404);
        }

        $CustomerBanks = CustomerBanks::with('bankDetails', 'bankBranchDetails')->Select('id', 'account_holder_name', 'account_no_last4', 'ifsc', 'status_code', 'bank_id', 'ifsc_master_id')->where('customer_id', $id)->get();
        $customerDocuments = CustomerDocuments::where('customer_id', $id)->get();
        $customerCompanyDetails = CustomerCompanyDetails::with('directorDetails')->where('customer_id', $id)->get()->makeVisible(['pan_no']);



        $customerCourtSummary = customerCourtSummary::where("customer_id", $id)->get();



        $data['customerDetails'] = $customerDetails;
        $data['CustomerBanks'] = $CustomerBanks;
        $data['customerDocuments'] = $customerDocuments;
        $data['customerCompanyDetails'] = $customerCompanyDetails;

        $data['customerCourtSummary'] = $customerCourtSummary;

        return response()->json([
            'status' => true,
            'message' => 'Load Successfully',
            'data' => $data,

        ], 200);
    }

    public function getCustomerLoan(Request $request, int $id)
    {
        try {
            $data =   Loan::where("customer_id", $id)->get();
            return response()->json([
                'status' => true,
                'message' => "Load Successfully",
                "data" => $data
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function saveCustomerDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'number' => 'required|digits:10|unique:customers,number',
            'name' => 'required',
         
            'dob' => 'required',
            'signup_document_type' => 'required|in:aadhaar,pan,driving_license,voter_id,passport',
            'signup_document' => 'required',
            'pan_no' => 'required',

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
            $customer = Customers::create([
                'name' => $request->name,
                'number' => $request->number,
                'email' => $request->email,
                'dob' => $request->dob,

                'state' => $request->state,
                'district' => $request->district,
                'city' => $request->city,
                'address' => $request->address,
                'country' => $request->country,
                'pincode' => $request->pincode,

                'signup_document_type' => $request->signup_document_type,
                'signup_document' => $request->signup_document,
                'pan_no' => $request->pan_no,
                'pan_verified' => 1,

                'created_by' => auth()->user()->id,
                'user_id' => auth()->user()->id,
                'tenant_id' => auth()->user()->tenant_id,
            ]);

       

            $commonDocument = CommonDocument::get();

            foreach ($commonDocument as $key => $value) {


                CustomerDocuments::create([
                    'customer_id' => $customer->id,
                    'document_id' => $value->id,
                    'document_name' => $value->name,
                ]);
            }

            $document = CustomerDocuments::where("customer_id", $customer->id)->where("document_id", 2)->first();
            $document->document_no = $request->pan_no;
            $document->document_no_last4 = substr($request->pan_no, -4);
            $document->country = $request->country ?? 'NA';
            $document->state = $request->state ?? 'NA';
            $document->city = $request->city ?? 'NA';
            $document->address = $request->address ?? 'NA';
            $document->pincode = $request->pincode ?? 'NA';
            $document->is_verified = 1;
            $document->verified_at = now();
            $document->user_id = auth()->id();
            $document->save();


            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Customer created successfully',
                'data' => $customer,
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
