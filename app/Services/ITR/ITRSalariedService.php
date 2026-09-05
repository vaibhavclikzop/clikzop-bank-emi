<?php

namespace App\Services\ITR;

use Illuminate\Support\Facades\Http;

class ITRSalariedService
{
    public function verify($username, $password)
    {


        $url = config('services.perfios.base_url') . '/ssp/itr/api/v1/itr-return-salaried';
        // echo $url;
        // die;
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'x-secure-id' => config('services.perfios.user_name'),
            'x-secure-cred' => config('services.perfios.client_password'),
            'x-organization-id' => config('services.perfios.client_id'),
        ])
            ->connectTimeout(10)
            ->timeout(60)
            ->post($url, [
                'username'       => $username,
                'password'       => $password,
                'apiVersion'     => '1.0.2',
                'consent'        => 'Y',
                'numberOfYears'  => 3,
                'partialReport'  => false,
            ]);

        $data = $response->json();

        if (! $response->successful()) {
            return [
                'status' => false,
                'message' => 'API request failed',
                'data' => $data,
            ];
        }


        $statusCode = $data['statusCode'] ?? null;

        if ($statusCode != 101) {

            return [
                'status' => false,
                'message' => 'Invalid Username or password or no data found',
                'data' => $data,
            ];
        }

        $profileData =  $data['result']["profile"];
        $filingData =  $data['result']["filingData"];
        $outstandingDemand =  $data['result']["outstandingDemand"];
        $filingHistoryData =  $data['result']["filingHistory"];

        $result["profileData"]['pan_no']              = $profileData['pan'];
        $result["profileData"]['name']                = $profileData['name'];
        $result["profileData"]['dob']                 = !empty($profileData['dob'])
            ? \Carbon\Carbon::createFromFormat('d-m-Y', $profileData['dob'])->format('Y-m-d')
            : null;

        $result["profileData"]['aadhaar']             = $profileData['aadhaar'];
        $result["profileData"]['passport_no']         = $profileData['passportNo'] ?? null;
        $result["profileData"]['emplyrCat']           = $profileData['emplyrCat'] ?? null;
        $result["profileData"]['emplyr_cat']          = $profileData['emplyrCat'] ?? null;
        $result["profileData"]['status_of_entity']    = $profileData['statusOfEntity'] ?? null;
        $result["profileData"]['residential_status']  = $profileData['residentialStatus'] ?? null;

        $result["profileData"]['pin_code']            = $profileData['address']['pinCode'] ?? null;
        $result["profileData"]['state_code']          = $profileData['address']['stateCode'] ?? null;
        $result["profileData"]['country_code']        = $profileData['address']['countryCode'] ?? null;
        $result["profileData"]['residence_no']        = $profileData['address']['residenceNo'] ?? null;
        $result["profileData"]['road_or_street']      = $profileData['address']['roadOrStreet'] ?? null;
        $result["profileData"]['residence_name']      = $profileData['address']['residenceName'] ?? null;
        $result["profileData"]['locality_of_area']    = $profileData['address']['localityOrArea'] ?? null;
        $result["profileData"]['city']                = $profileData['address']['cityOrTownOrDistrict'] ?? null;

        $result["profileData"]['std']                 = $profileData['contactDetails']['std'] ?? null;
        $result["profileData"]['email']               = $profileData['contactDetails']['email'] ?? null;
        $result["profileData"]['phone_no']            = $profileData['contactDetails']['phNo'];
        $result["profileData"]['email2']              = $profileData['contactDetails']['email2'] ?? null;
        $result["profileData"]['mobile']              = $profileData['contactDetails']['mobile'] ?? null;
        $result["profileData"]['mobile2']             = $profileData['contactDetails']['mobile2'] ?? null;
        $result["profileData"]['excelDownloadLink']             =  $data['result']['excelDownloadLink'] ?? null;
        $result["profileData"]['pdfDownloadLink']             =  $data['result']['pdfDownloadLink'] ?? null;
        $yearlyITR = [];
        foreach ($filingData as $itr) {
            $ItData = [];
            $ItData['form_name'] = $itr["formDetails"]['formName'] ?? "NA";
            $ItData['form_version'] = $itr["formDetails"]['formVersion'] ?? "NA";
            $ItData['description'] = $itr["formDetails"]['description'] ?? "NA";
            $ItData['financial_year'] = $itr["formDetails"]['financialYear'] ?? "NA";
            $ItData['assessment_year'] = $itr["formDetails"]['assessmentYear'] ?? "NA";
            $ItData['filing_date'] = $itr["formDetails"]['filingDate'] ?? "NA";
            $ItData["financialInfo"]['assessment_year'] = $itr["financialInfo"]['assessmentYear'] ?? "NA";
            $ItData["financialInfo"]['financial_rear'] = $itr["financialInfo"]['financialYear'] ?? "NA";
            $ItData["financialInfo"]['immovable_assets'] = $itr['financialInfo']['assetLiabilityDetails']['immovableAssets'] ?? 0;
            $ItData["financialInfo"]['movable_assets'] = $itr['financialInfo']['assetLiabilityDetails']['movableAssets'] ?? 0;
            $ItData["financialInfo"]['financial_assets'] = $itr['financialInfo']['assetLiabilityDetails']['financialAssets'] ?? 0;
            $ItData["financialInfo"]['total_liabilities'] = $itr['financialInfo']['assetLiabilityDetails']['totalLiabilities'] ?? 0;
            $ItData["financialInfo"]['refund'] = $itr['financialInfo']['taxDetails']['refund'] ?? 0;
            $ItData["financialInfo"]['taxes_paid'] = $itr['financialInfo']['taxDetails']['taxesPaid'] ?? 0;
            $ItData["financialInfo"]['aggregate_liability'] = $itr['financialInfo']['taxDetails']['aggregateLiability'] ?? 0;
            $ItData["financialInfo"]['net_tax_liability'] = $itr['financialInfo']['taxDetails']['netTaxLiability'] ?? 0;
            $ItData["financialInfo"]['total_interest_and_fee_payable'] = $itr['financialInfo']['taxDetails']['totalInterestAndFeePayable'] ?? 0;
            $ItData["financialInfo"]['total_advance_tax_paid'] = $itr['financialInfo']['taxDetails']['totalAdvanceTaxPaid'] ?? 0;
            $ItData["financialInfo"]['total_tds_claimed'] = $itr['financialInfo']['taxDetails']['totalTdsClaimed'] ?? 0;
            $ItData["financialInfo"]['total_tcs_claimed'] = $itr['financialInfo']['taxDetails']['totalTcsClaimed'] ?? 0;
            $ItData["financialInfo"]['total_self_assessment_tax_paid'] = $itr['financialInfo']['taxDetails']['totalSelfAssessmentTaxPaid'] ?? 0;
            $ItData["financialInfo"]['amount_payable'] = $itr['financialInfo']['taxDetails']['amountPayable'] ?? 0;
            $ItData["financialInfo"]['salary'] = $itr['financialInfo']['incomeDetails']['headwiseIncome']['salary'] ?? 0;
            $ItData["financialInfo"]['house_property'] = $itr['financialInfo']['incomeDetails']['headwiseIncome']['houseProperty'] ?? 0;
            $ItData["financialInfo"]['other_sources'] = $itr['financialInfo']['incomeDetails']['headwiseIncome']['otherSources'] ?? 0;
            $ItData["financialInfo"]['capital_gains'] = $itr['financialInfo']['incomeDetails']['headwiseIncome']['capitalGains'] ?? 0;
            $ItData['bank_details'] = [];

            foreach (($itr['bankDetails']['domesticBankDetails'] ?? []) as $bank) {

                $ItData['bank_details'][] = [
                    'name' => $bank['name'] ?? null,
                    'account_no' => $bank['accountNo'] ?? null,
                    'ifsc_code' => $bank['ifscCode'] ?? null,
                    'use_for_refund' => $bank['useForRefund'] ?? null,
                ];
            }
            $ItData['delay'] = $itr['complianceData']['delay'] ?? false;
            $ItData['default'] = $itr['complianceData']['default'] ?? false;
            $ItData['revised'] = $itr['complianceData']['revised'] ?? false;
            $ItData['late_fee'] = $itr['complianceData']['lateFee'] ?? false;
            $ItData['intrst_fee'] = $itr['complianceData']['intrstFee'] ?? false;
            $ItData['demand_notice'] = $itr['complianceData']['demandNotice'] ?? false;
            $ItData['high_val_transaction'] = $itr['complianceData']['highValTransaction'] ?? false;
            $yearlyITR["filingData"][] = $ItData;
        }

        $filingHistoryArray = [];
        foreach ($filingHistoryData as $value) {
            $filingDataHistory = [];
            $filingDataHistory["pan"] = $value["pan"];
            $filingDataHistory["assessment_year"] = $value["A.Y."];
            $filingDataHistory["form"] = $value["form"];
            $filingDataHistory["filing_type"] = $value["filingType"];
            $filingDataHistory["status"] = $value["status"];
            $filingDataHistory["date"] = $value["date"];
            $filingDataHistory["downloadLink"] = $value["downloadsStatus"]["downloadLink"];
            $filingHistoryArray["filingHistory"][] = $filingDataHistory;
        }




        $result["filingData"] = $yearlyITR;
        $result["outstandingDemand"] = $outstandingDemand;
        $result["filingHistory"] = $filingHistoryArray;



        return [

            'status' => true,
            'message' => 'Verified successfully',
            // 'yearlyITR' => $yearlyITR,
            'data' => $result,

        ];
    }
}
