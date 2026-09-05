<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CustomerCompanyDetails;
use App\Models\CustomerCompanyDirectors;
use App\Models\customerCourtSummary;
use App\Models\CustomerDocuments;
use App\Models\customerGstDetailFilling;
use App\Models\customerGstDetails;
use App\Models\Customers;
use App\Services\Verification\AadhaarService;
use App\Services\Verification\BackgroundVerificationService;
use App\Services\Verification\CinService;
use App\Services\Verification\DlService;
use App\Services\Verification\GSTDetailsByGSTINService;
use App\Services\Verification\GSTDetailsService;
use App\Services\Verification\PanService;
use App\Services\Verification\PassportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VerificationController extends Controller
{
    public function checkAadharCard(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'aadhar_card_no' => 'required|digits:12',
            'customer_id' => 'required',
            'lat' => 'required',
            'long' => 'required',
            'id' => 'required',
            'document_id' => 'required',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => [],
            ], 422);
        }

        try {
            $response = app(AadhaarService::class)->verify(
                $request->aadhar_card_no,
                $request->customer_id,
                $request->lat,
                $request->long,
            );

            // $response["status"] = true;
            if ($response['status'] == true) {
                $document = CustomerDocuments::findOrFail($request->id);

                $document->document_no = $request->aadhar_card_no;
                $document->document_no_last4 = substr($request->aadhar_card_no, -4);
                $document->country = $response['data']['country'] ?? 'NA';
                $document->state = $response['data']['state'] ?? 'NA';
                $document->city = $response['data']['city'] ?? 'NA';
                $document->address = $response['data']['address'] ?? 'NA';
                $document->pincode = $response['data']['pinCode'] ?? 'NA';
                $document->is_verified = 1;
                $document->verified_at = now();
                $document->user_id = auth()->id();
                $document->save();

                Customers::where('id', $document->customer_id)
                    ->update([
                        'adhar_verified' => 1,
                    ]);
                return response()->json([
                    'status' => true,
                    'message' => 'Save Successfully',
                    'data' => $document,
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => $response['message'],
                    'data' => $response,
                ], 422);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'data' => [],
            ], 422);
        }
    }

    public function checkPanCard(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'pan_card_no' => 'required',
            'customer_id' => 'required',
            'document_id' => 'required',
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => [],
            ], 422);
        }
        try {


            $response = app(PanService::class)->verify(
                $request->pan_card_no
            );

            if ($response['status'] == true) {
                $document = CustomerDocuments::findOrFail($request->id);

                $document->document_no = $request->pan_card_no;
                $document->document_no_last4 = substr($request->pan_card_no, -4);
                $document->country = $response['data']['country'] ?? 'NA';
                $document->state = $response['data']['state'] ?? 'NA';
                $document->city = $response['data']['city'] ?? 'NA';
                $document->address = $response['data']['address'] ?? 'NA';
                $document->pincode = $response['data']['pinCode'] ?? 'NA';
                $document->is_verified = 1;
                $document->verified_at = now();
                $document->user_id = auth()->id();
                $document->save();

                return response()->json([
                    'status' => true,
                    'message' => 'Save Successfully',
                    'data' => $document,
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => $response['message'],
                    'data' => $response,
                ], 422);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'data' => [],
            ], 422);
        }
    }

    public function checkDrivingLicense(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'driving_license_no' => 'required',
            'customer_id' => 'required',
            'document_id' => 'required',
            'id' => 'required',
            'dob' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => [],
            ], 422);
        }
        try {

            $response = app(DlService::class)->verify(
                $request->driving_license_no,
                $request->dob
            );

            if ($response['status'] == true) {
                $document = CustomerDocuments::findOrFail($request->id);

                $document->document_no = $request->driving_license_no;
                $document->document_no_last4 = substr($request->driving_license_no, -4);
                $document->country = $response['data']['country'] ?? 'NA';
                $document->state = $response['data']['state'] ?? 'NA';
                $document->city = $response['data']['city'] ?? 'NA';
                $document->address = $response['data']['address'] ?? 'NA';
                $document->pincode = $response['data']['pinCode'] ?? 'NA';
                $document->is_verified = 1;
                $document->verified_at = now();
                $document->user_id = auth()->id();
                $document->save();

                return response()->json([
                    'status' => true,
                    'message' => 'Save Successfully',
                    'data' => $document,
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => $response['message'],
                    'data' => $response,
                ], 422);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'data' => [],
            ], 422);
        }
    }

    public function checkPassport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file_no' => 'required',
            'customer_id' => 'required',
            'document_id' => 'required',
            'id' => 'required',
            'dob' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => [],
            ], 422);
        }
        try {

            $response = app(PassportService::class)->verify(
                $request->file_no,
                $request->dob
            );

            if ($response['status'] == true) {
                $document = CustomerDocuments::findOrFail($request->id);

                $document->document_no = $request->file_no;
                $document->document_no_last4 = substr($request->file_no, -4);
                $document->country = $response['data']['country'] ?? 'NA';
                $document->state = $response['data']['state'] ?? 'NA';
                $document->city = $response['data']['city'] ?? 'NA';
                $document->address = $response['data']['address'] ?? 'NA';
                $document->pincode = $response['data']['pinCode'] ?? 'NA';
                $document->is_verified = 1;
                $document->verified_at = now();
                $document->user_id = auth()->id();
                $document->save();

                return response()->json([
                    'status' => true,
                    'message' => 'Save Successfully',
                    'data' => $document,
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => $response['message'],
                    'data' => $response,
                ], 422);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'data' => [],
            ], 422);
        }
    }

    public function getCompanyDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'customer_id' => 'required',
            'cin_no' => 'required|unique:customer_company_details,cin',

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

            $response = app(CinService::class)->verify(
                $request->cin_no
            );

            if ($response['status'] == true) {

                $companyData = $response['data']['result']['company'];

                $company = CustomerCompanyDetails::create(
                    [
                        'customer_id' => $request->customer_id,

                        'cin' => $companyData['cin'],
                        'company_name' => $companyData['entityName'] ?? null,
                        'status' => $companyData['status'] ?? null,
                        'entity_class' => $companyData['entityClass'] ?? null,
                        'category' => $companyData['category'] ?? null,
                        'subcategory' => $companyData['subcategory'] ?? null,
                        'registration_number' => $companyData['registrationNumber'] ?? null,
                        'roc_code' => $companyData['rocCode'] ?? null,
                        'whether_listed_or_not' => $companyData['whetherListedOrNot'] ?? null,

                        'paid_up_capital' => $companyData['paidUpCapital'] ?? null,
                        'authorised_capital' => $companyData['authorisedCapital'] ?? null,
                        'number_of_members' => $companyData['numberOfMembers'] ?? null,

                        'date_of_incorporation' => ! empty($companyData['dateOfIncorporation'])
                            ? Carbon::createFromFormat('d-m-Y', $companyData['dateOfIncorporation'])->format('Y-m-d')
                            : null,

                        'date_of_last_agm' => ! empty($companyData['dateOfLastAGM'])
                            ? Carbon::createFromFormat('d-m-Y', $companyData['dateOfLastAGM'])->format('Y-m-d')
                            : null,

                        'date_of_balance_sheet' => ! empty($companyData['dateOfBalanceSheet'])
                            ? Carbon::createFromFormat('d-m-Y', $companyData['dateOfBalanceSheet'])->format('Y-m-d')
                            : null,

                        'industry' => $companyData['industry'] ?? null,
                        'sub_industry' => $companyData['subIndustry'] ?? null,

                        'activity_group' => $companyData['activityGroup'] ?? null,
                        'activity_class' => $companyData['activityClass'] ?? null,
                        'activity_sub_class' => $companyData['activitySubClass'] ?? null,

                        'registered_address' => $companyData['registeredAddress'] ?? null,
                        'alternative_address' => $companyData['alternativeAddress'] ?? null,

                        'email' => $companyData['emailId'] ?? null,
                        'pan_no' => $response['data']['pan_no'] ?? null,
                        'pan_last4' => substr($response['data']['pan_no'], -4) ?? null,

                        'alternate_source_data' => $response['result']['alternateSourceData'] ?? false,

                        'api_response' => $response,
                        'user_id' => auth()->id(),
                    ]
                );

                foreach ($response['data']['result']['directors'] as $director) {

                    CustomerCompanyDirectors::create([
                        'company_id' => $company->id,
                        'customer_id' => $request->customer_id,

                        'din' => $director['din'] ?? null,
                        'pan' => $director['pan'] ?? null,
                        'name' => $director['name'] ?? null,
                        'designation' => $director['designation'] ?? null,

                        'dob' => ! empty($director['dob'])
                            ? Carbon::createFromFormat('d-m-Y', $director['dob'])->format('Y-m-d')
                            : null,

                        'father_name' => $director['fatherName'] ?? null,

                        'tenure_begin_date' => ! empty($director['tenureBeginDate'])
                            ? Carbon::createFromFormat('d-m-Y', $director['tenureBeginDate'])->format('Y-m-d')
                            : null,

                        'tenure_end_date' => ! empty($director['tenureEndDate'])
                            ? Carbon::createFromFormat('d-m-Y', $director['tenureEndDate'])->format('Y-m-d')
                            : null,

                        'address' => $director['address'] ?? null,

                        'user_id' => auth()->id(),
                    ]);
                }

                DB::commit();

                return response()->json([
                    'status' => true,
                    'message' => 'Save Successfully',
                    'data' => $response,
                ], 200);
            } else {
                DB::rollback();

                return response()->json([
                    'status' => false,
                    'message' => $response['message'],
                    'data' => $response,
                ], 422);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'data' => [],
            ], 422);
        }
    }

    public function getGSTDetailsPan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pan_no' => 'required',
            'customer_id' => 'required',
            'company_id' => 'required',

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

            $response = app(GSTDetailsService::class)->verify(
                $request->pan_no
            );
            $masterData = $response['data']['masterData'];
            $fillingData = $response['data']['fillingData'];

            if ($response['status'] == true) {

                $gstDetails = customerGstDetails::updateOrCreate(
                    [
                        'customer_id' => $request->customer_id,
                        'gst_in' => $masterData['gst_in'],
                        'company_id' => $request->company_id,
                    ],
                    [
                        'company_name' => $masterData['company_name'],
                        'email_id' => $masterData['email_id'],
                        'gst_in_ref' => $masterData['gst_in_ref'],
                        'mobile' => $masterData['mobile'],
                        'pan' => $masterData['pan'],
                        'registration_name' => $masterData['registration_name'],
                        'tin_number' => $masterData['tin_number'],
                        'state' => $masterData['state'],
                        'stjCd' => $masterData['stjCd'],
                        'dty' => $masterData['dty'],
                        'stj' => $masterData['stj'],
                        'nba' => $masterData['nba'],
                        'ctb' => $masterData['ctb'],
                        'registration_date' => Carbon::createFromFormat(
                            'd/m/Y',
                            $masterData['registration_date']
                        )->format('Y-m-d'),
                        'address' => $masterData['address'],
                        'trade_name' => $masterData['trade_name'],
                        'ctjCd' => $masterData['ctjCd'],
                        'status' => $masterData['status'],
                        'ctj' => $masterData['ctj'],
                        'e_invoice_status' => $masterData['e_invoice_status'],
                        'user_id' => auth()->id(),
                        'api_response' => $response['apiResponse'],
                    ]
                );

                foreach ($fillingData as $value) {

                    customerGstDetailFilling::firstOrCreate(
                        [
                            'gst_details_id' => $gstDetails->id,
                            'arn' => $value['arn'],
                        ],
                        [
                            'valid' => $value['valid'],
                            'mof' => $value['mof'],
                            'dof' => Carbon::createFromFormat('d-m-Y', $value['dof'])->format('Y-m-d'),
                            'return_type' => $value['return_type'],
                            'ret_prd' => $value['return_period'],
                            'status' => $value['status'],
                            'filed_date' => $value['filed_date'] ?? null,
                            'due_date' => $value['due_date'],
                            'is_delay' => $value['is_delay'],
                            'delay_days' => $value['delay_days'],
                        ]
                    );
                }

                DB::commit();

                return response()->json([
                    'status' => true,
                    'message' => 'Save Successfully',
                    'data' => $response,
                ], 200);
            } else {
                DB::rollBack();

                return response()->json([
                    'status' => false,
                    'message' => $response['message'],
                    'data' => $response,
                ], 422);
            }
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'data' => [],
            ], 422);
        }
    }

    public function getGSTDetailsByGSTIN(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'gst_no' => 'required',
            'customer_id' => 'required',
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

            $response = app(GSTDetailsByGSTINService::class)->verify(
                $request->gst_no
            );

            $masterData = $response['data']['masterData'];
            $fillingData = $response['data']['fillingData'];

            if ($response['status'] == true) {

                $gstDetails = customerGstDetails::updateOrCreate(
                    [
                        'customer_id' => $request->customer_id,
                        'gst_in' => $masterData['gst_in'],
                        'company_id' => $request->company_id,
                    ],
                    [
                        'company_name' => $masterData['company_name'],
                        'email_id' => $masterData['email_id'],
                        'gst_in_ref' => $masterData['gst_in_ref'],
                        'mobile' => $masterData['mobile'],
                        'pan' => $masterData['pan'],
                        'registration_name' => $masterData['registration_name'],
                        'tin_number' => $masterData['tin_number'],
                        'state' => $masterData['state'],
                        'stjCd' => $masterData['stjCd'],
                        'dty' => $masterData['dty'],
                        'stj' => $masterData['stj'],
                        'nba' => $masterData['nba'],
                        'ctb' => $masterData['ctb'],
                        'registration_date' => Carbon::createFromFormat(
                            'd/m/Y',
                            $masterData['registration_date']
                        )->format('Y-m-d'),
                        'address' => $masterData['address'],
                        'trade_name' => $masterData['trade_name'],
                        'ctjCd' => $masterData['ctjCd'],
                        'status' => $masterData['status'],
                        'ctj' => $masterData['ctj'],
                        'e_invoice_status' => $masterData['e_invoice_status'],
                        'user_id' => auth()->id(),
                        'api_response' => $response['apiResponse'],
                    ]
                );

                foreach ($fillingData as $value) {

                    customerGstDetailFilling::firstOrCreate(
                        [
                            'gst_details_id' => $gstDetails->id,
                            'arn' => $value['arn'],
                        ],
                        [
                            'valid' => $value['valid'],
                            'mof' => $value['mof'],
                            'dof' => Carbon::createFromFormat('d-m-Y', $value['dof'])->format('Y-m-d'),
                            'return_type' => $value['return_type'],
                            'ret_prd' => $value['return_period'],
                            'status' => $value['status'],
                            'filed_date' => $value['filed_date'] ?? null,
                            'due_date' => $value['due_date'],
                            'is_delay' => $value['is_delay'],
                            'delay_days' => $value['delay_days'],
                        ]
                    );
                }

                DB::commit();

                return response()->json([
                    'status' => true,
                    'message' => 'Save Successfully',
                    'data' => $gstDetails,
                ], 200);
            } else {
                DB::rollBack();

                return response()->json([
                    'status' => false,
                    'message' => $response['message'],
                    'data' => $response,
                ], 422);
            }
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'data' => [],
            ], 422);
        }
    }

    public function checkBackgroundVerification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'dob' => 'required',
            'customer_id' => 'required',

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

            $response = app(BackgroundVerificationService::class)->verify(
                $request->name,
                $request->dob,
            );


            if ($response['status'] == true && $response["statusCode"] == 101) {


                $data = $response["data"]["result"];
                $severity_total = $response["data"]["result"]["severityCount"]["total"]["total"] ?? 0;
                $severity_high_relevance = $response["data"]["result"]["severityCount"]["total"]["highRelevance"] ?? 0;
                $severity_high = $response["data"]["result"]["severityCount"]["total"]["high"] ?? 0;
                $severity_low = $response["data"]["result"]["severityCount"]["total"]["low"] ?? 0;
                $severity_medium = $response["data"]["result"]["severityCount"]["total"]["medium"] ?? 0;


                $civil_cases = $response["data"]["result"]["caseTypeCategoryCount"]["total"]["civil"] ?? 0;
                $criminal_cases = $response["data"]["result"]["caseTypeCategoryCount"]["total"]["criminal"] ?? 0;

                $pending_cases = $response["data"]["result"]["totalCases"]["pending"] ?? 0;
                $disposed_cases = $response["data"]["result"]["totalCases"]["disposed"] ?? 0;
                $not_available_cases = $response["data"]["result"]["totalCases"]["not available"] ?? 0;
                $total_cases = $response["data"]["result"]["totalCases"]["total"] ?? 0;


                $district_courts = $response["data"]["result"]["courtWiseCount"]["districtCourts"]["total"] ?? 0;
                $high_courts = $response["data"]["result"]["courtWiseCount"]["highCourts"]["total"] ?? 0;
                $consumer_courts = $response["data"]["result"]["courtWiseCount"]["consumerCourt"]["total"] ?? 0;
                $supreme_courts = $response["data"]["result"]["courtWiseCount"]["supremeCourt"]["total"] ?? 0;
                $tribunal_courts = $response["data"]["result"]["courtWiseCount"]["tribunalCourts"]["total"] ?? 0;

                $confidence_level = $response["data"]["result"]["confidenceLevel"] ?? null;

                $data = customerCourtSummary::updateOrCreate(
                    [
                        "customer_id" => $request->customer_id,
                    ],
                    [
                        "severity_total" => $severity_total,
                        "severity_high_relevance" => $severity_high_relevance,
                        "severity_high" => $severity_high,
                        "severity_low" => $severity_low,
                        "severity_medium" => $severity_medium,

                        "civil_cases" => $civil_cases,
                        "criminal_cases" => $criminal_cases,

                        "pending_cases" => $pending_cases,
                        "disposed_cases" => $disposed_cases,
                        "not_available_cases" => $not_available_cases,
                        "total_cases" => $total_cases,

                        "district_courts" => $district_courts,
                        "high_courts" => $high_courts,
                        "consumer_courts" => $consumer_courts,
                        "supreme_courts" => $supreme_courts,
                        "tribunal_courts" => $tribunal_courts,

                        "confidence_level" => $confidence_level,

                        'api_response' => $response["data"],
                        "user_id" => auth()->id(),
                    ]
                );


                DB::commit();

                return response()->json([
                    'status' => true,
                    'message' => 'Save Successfully',
                    'data' => $data,
                ], 200);
            } else if ($response['status'] == true && $response["statusCode"] == 102) {
                return response()->json([
                    'status' => true,
                    'message' => "No Record Found",
                    'data' => $response,
                ], 200);
            } else {
                DB::rollBack();

                return response()->json([
                    'status' => false,
                    'message' => $response['message'],
                    'data' => $response,
                ], 422);
            }
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'data' => [],
            ], 422);
        }
    }
    public function updateBackgroundVerification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'is_verified' => 'required',


        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => [],
            ], 422);
        }

        try {
            $data =   customerCourtSummary::where("id", $request->id)->first();
            $data->is_verified = $request->is_verified;
            $data->save();
            return response()->json([
                'status' => true,
                'message' => "Update Successfully",
                'data' => [],
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'data' => [],
            ], 422);
        }
    }
}
