<?php

namespace App\Http\Controllers\API\CustomerController;

use App\Http\Controllers\Controller;
use App\Models\CustomerITRFilingHistory;
use App\Models\CustomerITROutStandingDemand;
use App\Models\CustomerITRProfile;
use App\Models\CustomerITRYearly;
use App\Models\Customers;
use App\Models\Loan;
use App\Models\LoanITRFillingHistory;
use App\Models\LoanITROutStandingDemand;
use App\Models\LoanITRProfile;
use App\Models\LoanITRYearly;
use App\Models\LoanITRYearlyFinancials;
use App\Models\loanLipReportDet;
use App\Models\loanLipReportMst;
use App\Models\loanVanillaReportFields;
use App\Models\LoanVanillaReportSummary;
use App\Models\vanillaField;
use App\Models\vanillaReportMst;
use App\Models\vanillaReportValues;
use App\Models\vanillaReportYears;
use App\Services\GST\GSTReturnService;
use App\Services\ITR\ITRBusinessService;
use App\Services\ITR\ITRSalariedService;
use App\Services\Verification\GSTDetailsByGSTINService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ITRController extends Controller
{
    public function getSalariedITRDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required',
            'username' => 'required',
            'password' => 'required',
            'loan_id' => 'required',
            'loan_applicant_id' => 'required',

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

            $exists = Customers::where('id', $request->customer_id)->exists();

            if (! $exists) {
                return response()->json([
                    'status' => false,
                    'message' => 'Customer Not Found',
                    'data' => [],
                ], 404);
            }
            $loanExists = Loan::where('id', $request->loan_id)->exists();

            if (! $loanExists) {
                return response()->json([
                    'status' => false,
                    'message' => 'Loan Not Found',
                    'data' => [],
                ], 404);
            }


            // $itrExists = LoanITRProfile::where("customer_id", $request->customer_id)->where("loan_id", $request->loan_id)->exists();
            // if ($itrExists) {
            //     return response()->json([
            //         'status' => true,
            //         'message' => 'ITR already fetched',
            //         'data' => [],
            //     ], 200);
            // }

            $response = app(ITRSalariedService::class)->verify(
                $request->username,
                $request->password
            );




            if ($response["status"] == true) {


                $profileData = $response["data"]["profileData"];
                $filingData = $response["data"]["filingData"];
                $outstandingDemand = $response["data"]["outstandingDemand"];
                $filingHistory = $response["data"]["filingHistory"];
                $customerProfile = LoanITRProfile::updateOrCreate(
                    [
                        'customer_id' => $request->customer_id,
                        'loan_id' => $request->loan_id,
                        'loan_applicant_id' => $request->loan_applicant_id,

                    ],
                    [
                        'pan_no' => $profileData['pan_no'],

                        'name' => $profileData['name'] ?? null,
                        'dob' => $profileData['dob'] ?? null,
                        'aadhaar' => $profileData['aadhaar'] ?? null,
                        'passport_no' => $profileData['passport_no'] ?? null,
                        'emplyrCat' => $profileData['emplyrCat'] ?? null,
                        'emplyr_cat' => $profileData['emplyr_cat'] ?? null,
                        'status_of_entity' => $profileData['status_of_entity'] ?? null,
                        'residential_status' => $profileData['residential_status'] ?? null,
                        'pin_code' => $profileData['pin_code'] ?? null,
                        'state_code' => $profileData['state_code'] ?? null,
                        'country_code' => $profileData['country_code'] ?? null,
                        'residence_no' => $profileData['residence_no'] ?? null,
                        'road_or_street' => $profileData['road_or_street'] ?? null,
                        'residence_name' => $profileData['residence_name'] ?? null,
                        'locality_of_area' => $profileData['locality_of_area'] ?? null,
                        'city' => $profileData['city'] ?? null,
                        'std' => $profileData['std'] ?? null,
                        'email' => $profileData['email'] ?? null,
                        'phone_no' => $profileData['phone_no'] ?? null,
                        'email2' => $profileData['email2'] ?? null,
                        'mobile' => $profileData['mobile'] ?? null,
                        'mobile2' => $profileData['mobile2'] ?? null,
                        'excelDownloadLink' => $profileData['excelDownloadLink'] ?? null,
                        'pdfDownloadLink' => $profileData['pdfDownloadLink'] ?? null,
                        'user_id' => auth()->user()->id,
                        'api_response' => $response,
                    ]
                );


                foreach ($filingData['filingData'] as $itr) {

                    LoanITRYearly::updateOrCreate(
                        [
                            'loan_applicant_id' => $request->loan_applicant_id,
                            'loan_id' => $request->loan_id,
                            'loan_itr_profile_id' => $customerProfile->id,
                            'financial_year' => $itr['financial_year']
                        ],
                        [

                            'customer_id' => $request->customer_id,


                            'form_name' => $itr['form_name'] ?? null,
                            'form_version' => $itr['form_version'] ?? null,
                            'description' => $itr['description'] ?? null,

                            'assessment_year' => $itr['assessment_year'] ?? null,

                            'filing_date' => !empty($itr['filing_date'])
                                ? \Carbon\Carbon::createFromFormat('d-m-Y', $itr['filing_date'])->format('Y-m-d')
                                : null,

                            'financial_assessment_year' => $itr['financialInfo']['assessment_year'] ?? null,
                            'financial_info_year' => $itr['financialInfo']['financial_rear'] ?? null,

                            'immovable_assets' => $itr['financialInfo']['immovable_assets'] ?? 0,
                            'movable_assets' => $itr['financialInfo']['movable_assets'] ?? 0,
                            'financial_assets' => $itr['financialInfo']['financial_assets'] ?? 0,
                            'total_liabilities' => $itr['financialInfo']['total_liabilities'] ?? 0,

                            'refund' => $itr['financialInfo']['refund'] ?? 0,
                            'taxes_paid' => $itr['financialInfo']['taxes_paid'] ?? 0,
                            'aggregate_liability' => $itr['financialInfo']['aggregate_liability'] ?? 0,
                            'net_tax_liability' => $itr['financialInfo']['net_tax_liability'] ?? 0,
                            'total_interest_and_fee_payable' => $itr['financialInfo']['total_interest_and_fee_payable'] ?? 0,
                            'total_advance_tax_paid' => $itr['financialInfo']['total_advance_tax_paid'] ?? 0,
                            'total_tds_claimed' => $itr['financialInfo']['total_tds_claimed'] ?? 0,
                            'total_tcs_claimed' => $itr['financialInfo']['total_tcs_claimed'] ?? 0,
                            'total_self_assessment_tax_paid' => $itr['financialInfo']['total_self_assessment_tax_paid'] ?? 0,
                            'amount_payable' => $itr['financialInfo']['amount_payable'] ?? 0,

                            'salary' => $itr['financialInfo']['salary'] ?? 0,
                            'house_property' => $itr['financialInfo']['house_property'] ?? 0,
                            'other_sources' => $itr['financialInfo']['other_sources'] ?? 0,
                            'capital_gains' => $itr['financialInfo']['capital_gains'] ?? 0,
                            'bank_name' => $itr['bank_details'][0]['bank_name'] ?? "NA",
                            'account_no' => $itr['bank_details'][0]['account_no'] ?? "NA",
                            'ifsc_code' => $itr['bank_details'][0]['ifsc_code'] ?? "NA",
                            'use_for_refund' => (bool) $itr['bank_details'][0]['use_for_refund'] ?? false,

                            'delay' =>  (bool) $itr['delay'] ?? false,
                            'default' =>  (bool) $itr['default'] ?? false,
                            'revised' =>  (bool) $itr['revised'] ?? false,
                            'late_fee' => (bool) $itr['late_fee'] ?? false,
                            'intrst_fee' => (bool) $itr['intrst_fee'] ?? false,
                            'demand_notice' => (bool) $itr['demand_notice'] ?? false,
                            'high_val_transaction' => $itr['high_val_transaction'] ?? false,
                        ]
                    );

                    $saveVanillaReport =  $this->saveVanillaReport($request->loan_id, $request->customer_id, $itr['financial_year']);
                    updateVanillaValuesSalaried($request->loan_id, $itr['financialInfo']['salary'], $itr['financial_year']);

                    if ($saveVanillaReport == false) {
                        DB::rollBack();
                        return response()->json([
                            'status' => false,
                            'message' => "Something went wrong",
                            'data' => [],
                        ], 404);
                    }
                }

                foreach ($outstandingDemand as $value) {

                    LoanITROutStandingDemand::updateOrCreate([
                        'loan_applicant_id' => $request->loan_applicant_id,
                        'loan_id' => $request->loan_id,
                        'loan_itr_profile_id' => $customerProfile->id,
                        "assessment_year" => $value["assessmentYear"],
                    ], [

                        'customer_id' => $request->customer_id,

                        "rectification_rights" => $value["rectificationRights"],
                        'date_of_service' => !empty($value['dateOfService'])
                            ? \Carbon\Carbon::createFromFormat('d-m-Y', $value['dateOfService'])->format('Y-m-d')
                            : null,
                        "din" => $value["din"],
                        "date_of_demandRaised" => $value["dateOfDemandRaised"],
                        "section_code" => $value["sectionCode"],

                        "outstanding_demand_amount" => $value["outstandingDemandAmount"],
                        "mode_of_service" => $value["modeOfService"],
                        "uploaded_by" => $value["uploadedBy"],
                    ]);
                }
                foreach ($filingHistory["filingHistory"] as  $v) {
                    LoanITRFillingHistory::updateOrCreate([
                        'loan_applicant_id' => $request->loan_applicant_id,
                        'loan_id' => $request->loan_id,
                        'loan_itr_profile_id' => $customerProfile->id,
                        "assessment_year" => $v["assessment_year"],
                    ], [

                        'customer_id' => $request->customer_id,

                        "pan_no" => $v["pan"],
                        "pan_no_last4" => substr($v["pan"], -4),

                        "form" => $v["form"],
                        "filingType" => $v["filing_type"],
                        "status" => $v["status"],
                        "date" => $v["date"],
                        "downloadLink" => $v["downloadLink"],
                    ]);
                }


                DB::commit();
            } else {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => $response["message"],
                    'data' => $response['data'],
                ], 404);
            }


            return response()->json([
                'status' => true,
                'message' => 'ITR saved successfully',
                'data' => $response['data'],
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function getBusinessITRDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required',
            'username' => 'required',
            'password' => 'required',
            'loan_id' => 'required',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => [],
            ], 422);
        }
        // $itrExists = LoanITRProfile::where("customer_id", $request->customer_id)->where("loan_id", $request->loan_id)->exists();
        // if ($itrExists) {
        //     return response()->json([
        //         'status' => true,
        //         'message' => 'ITR already fetched',
        //         'data' => [],
        //     ], 200);
        // }
        DB::beginTransaction();
        try {
            $response = app(ITRBusinessService::class)->verify($request->username, $request->password);

            if ($response["status"] == true) {

                $profileData = $response["data"]["profileData"];
                $filingData = $response["data"]["filingData"];
                $filingHistory = $response["data"]["filingHistory"];
                $customerProfile = LoanITRProfile::updateOrCreate(
                    [
                        'loan_applicant_id' => $request->loan_applicant_id,
                        'customer_id' => $request->customer_id,
                        'loan_id' => $request->loan_id,

                    ],
                    [
                        'pan_no' => $profileData['pan_no'],
                        'type' => "business",
                        'name' => $profileData['name'] ?? null,
                        'dob' => $profileData['dob'] ?? null,
                        'aadhaar' => $profileData['aadhaar'] ?? null,
                        'passport_no' => $profileData['passport_no'] ?? null,
                        'emplyrCat' => $profileData['emplyrCat'] ?? null,
                        'emplyr_cat' => $profileData['emplyr_cat'] ?? null,
                        'status_of_entity' => $profileData['status_of_entity'] ?? null,
                        'residential_status' => $profileData['residential_status'] ?? null,
                        'pin_code' => $profileData['pin_code'] ?? null,
                        'state_code' => $profileData['state_code'] ?? null,
                        'country_code' => $profileData['country_code'] ?? null,
                        'residence_no' => $profileData['residence_no'] ?? null,
                        'road_or_street' => $profileData['road_or_street'] ?? null,
                        'residence_name' => $profileData['residence_name'] ?? null,
                        'locality_of_area' => $profileData['locality_of_area'] ?? null,
                        'city' => $profileData['city'] ?? null,
                        'std' => $profileData['std'] ?? null,
                        'email' => $profileData['email'] ?? null,
                        'phone_no' => $profileData['phone_no'] ?? null,
                        'email2' => $profileData['email2'] ?? null,
                        'mobile' => $profileData['mobile'] ?? null,
                        'mobile2' => $profileData['mobile2'] ?? null,
                        'excelDownloadLink' => $profileData['excelDownloadLink'] ?? null,
                        'pdfDownloadLink' => $profileData['pdfDownloadLink'] ?? null,
                        'user_id' => auth()->user()->id,
                        'api_response' => $response,
                    ]
                );


                foreach ($filingData['filingData'] as $itr) {

                    $LoanITRYearly =  LoanITRYearly::updateOrCreate(
                        [
                            'loan_applicant_id' => $request->loan_applicant_id,
                            'loan_id' => $request->loan_id,
                            'loan_itr_profile_id' => $customerProfile->id,
                        ],
                        [

                            'customer_id' => $request->customer_id,

                            'financial_year' => $itr["itrYearly"]['financial_rear'] ?? null,
                            'assessment_year' => $itr["itrYearly"]['assessment_year'] ?? null,
                            'salary' => $itr["itrYearly"]['salary'] ?? 0,
                            'house_property' => $itr["itrYearly"]['house_property'] ?? 0,
                            'other_sources' => $itr["itrYearly"]['other_sources'] ?? 0,
                            'capital_gains' => $itr["itrYearly"]['capital_gains'] ?? 0,
                        ]
                    );

                    LoanITRYearlyFinancials::updateOrCreate(
                        [
                            'loan_applicant_id' => $request->loan_applicant_id,
                            "loan_itr_yearly_id" => $LoanITRYearly->id,
                        ],
                        [
                            "loan_id" => $request->loan_id,
                            "customer_id" => $request->customer_id,

                            "total_current_liability" => $itr["itrYearlyFinancial"]["total_current_liability"],
                            "total_inventory" => $itr["itrYearlyFinancial"]["total_inventory"],
                            "total_assets" => $itr["itrYearlyFinancial"]["total_assets"],
                            "trade_receivables" => $itr["itrYearlyFinancial"]["trade_receivables"],
                            "total_liability" => $itr["itrYearlyFinancial"]["total_liability"],
                            "trade_payable" => $itr["itrYearlyFinancial"]["trade_payable"],
                            "total_investment" => $itr["itrYearlyFinancial"]["total_investment"],
                            "total_el" => $itr["itrYearlyFinancial"]["total_el"],
                            "total_fixed_asset" => $itr["itrYearlyFinancial"]["total_fixed_asset"],
                            "total_equity" => $itr["itrYearlyFinancial"]["total_equity"],
                            "cash_and_cash_eqv" => $itr["itrYearlyFinancial"]["cash_and_cash_eqv"],
                            "total_current_asset" => $itr["itrYearlyFinancial"]["total_current_asset"],
                            "non_operating_turnover" => $itr["itrYearlyFinancial"]["non_operating_turnover"],
                            "operating_turnover" => $itr["itrYearlyFinancial"]["operating_turnover"],
                            "profit_before_tax" => $itr["itrYearlyFinancial"]["profit_before_tax"],
                            "direct_cost" => $itr["itrYearlyFinancial"]["direct_cost"],
                            "purchases" => $itr["itrYearlyFinancial"]["purchases"],
                            "interest_expense" => $itr["itrYearlyFinancial"]["interest_expense"],
                            "total_expense" => $itr["itrYearlyFinancial"]["total_expense"],
                            "income_from_bp" => $itr["itrYearlyFinancial"]["income_from_bp"],
                            "income_from_os" => $itr["itrYearlyFinancial"]["income_from_os"],
                            "profit_after_tax" => $itr["itrYearlyFinancial"]["profit_after_tax"],
                            "gross_profit" => $itr["itrYearlyFinancial"]["gross_profit"],
                            "income_from_salary" => $itr["itrYearlyFinancial"]["income_from_salary"],
                            "income_from_hp" => $itr["itrYearlyFinancial"]["income_from_hp"],
                            "total_tax" => $itr["itrYearlyFinancial"]["total_tax"],
                            "income_from_cg" => $itr["itrYearlyFinancial"]["income_from_cg"],
                            "ebitda" => $itr["itrYearlyFinancial"]["ebitda"],
                            "total_revenue" => $itr["itrYearlyFinancial"]["total_revenue"],
                            "revenue_from_operations" => $itr["itrYearlyFinancial"]["revenue_from_operations"],
                        ]
                    );
                    $saveVanillaReport =  $this->saveVanillaReport($request->loan_id, $request->customer_id, $itr["itrYearly"]['financial_rear']);
                    updateVanillaValuesBusiness($request->loan_id, $itr["itrYearly"]['financial_rear'], $LoanITRYearly->id);
                }


                foreach ($filingHistory as  $v) {
                    LoanITRFillingHistory::updateOrCreate(
                        [
                            'loan_applicant_id' => $request->loan_applicant_id,
                            'loan_id' => $request->loan_id,
                            'loan_itr_profile_id' => $customerProfile->id,
                        ],
                        [

                            'customer_id' => $request->customer_id,

                            "pan_no" => $v["pan"],
                            "pan_no_last4" => substr($v["pan"], -4),
                            "assessment_year" => $v["assessment_year"],
                            "form" => $v["form"],
                            "filingType" => $v["filing_type"],
                            "status" => $v["status"],
                            "date" => $v["date"],
                            "downloadLink" => $v["downloadLink"],
                        ]
                    );
                }
                DB::commit();
                return response()->json([
                    'status' => true,
                    'message' => "Save Successfully",
                    'data' => [],
                ], 200);
            } else {

                return response()->json([
                    'status' => false,
                    'message' => $response["message"],
                    'data' => [],
                ], 500);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    protected function saveVanillaReport($loan_id, $customer_id, $financial_year, $turn_over = 0, $gross_profit = 0)
    {

        try {
            $Loan = Loan::where("id", $loan_id)->first();

            $vanillaReportMst = vanillaReportMst::updateOrCreate(
                [
                    "loan_id" => $loan_id,
                    "customer_id" => $customer_id,
                ],
                [
                    "user_id" => auth()->user()->id,
                ]
            );

            $vanillaReportYears =   vanillaReportYears::updateOrCreate(
                [
                    "vanilla_mst_id" => $vanillaReportMst->id,
                    "financial_year" => $financial_year,
                ],
                [
                    "vanilla_mst_id" => $vanillaReportMst->id,
                    "financial_year" => $financial_year,
                    "turnover" => $turn_over,
                    "gross_profit" => $gross_profit,
                ]
            );

            $vanillaField = vanillaField::where("status", "active")->get();
            foreach ($vanillaField as $key => $value) {
                $field =  loanVanillaReportFields::updateOrCreate([
                    "vanilla_report_mst_id" => $vanillaReportMst->id,
                    "vanilla_field_id" => $value->id,
                ], [
                    "eligibility_percentage" => 0,
                    "average" => 0,
                    "eligibility_income" => 0,
                ]);

                vanillaReportValues::updateOrCreate([
                    "vanilla_report_year_id" => $vanillaReportYears->id,
                    "field_name" => $value->field_name,

                ], [
                    "loan_vanilla_report_field_id" => $field->id,
                    "vanilla_field_id" => $value->id,
                    "display_name" => $value->display_name,
                    "display_order" => $value->display_order,
                    "amount" => 0,
                    "eligibility_percentage" => 0,
                    "eligible_amount" => 0
                ]);
            }

            LoanVanillaReportSummary::updateOrCreate([
                "loan_id" => $loan_id
            ], [
                "tenor" => $Loan->tenure_months
            ]);


            $loanLipReportMst = loanLipReportMst::firstOrCreate([
                "loan_id" => $loan_id,
            ]);

            loanLipReportDet::firstOrCreate([
                "loan_id" => $loan_id,
                "loan_lip_report_mst_id" => $loanLipReportMst->id,
                "customer_id" => $customer_id,
            ]);


            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
}
