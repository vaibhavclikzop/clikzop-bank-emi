<?php

namespace App\Services\ITR;

use Illuminate\Support\Facades\Http;

class ITRBusinessService
{
    public function verify($username, $password)
    {




        $url = config('services.perfios.base_url') . '/ssp/itr/api/v1/itr-return-forms';
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
                'apiVersion'     => '1.0.1',
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

        $profileData =  $data['result']["generalInformation"];
        $financialInformation =  $data['result']["financialInformation"];
        //  $outstandingDemand =  $data['result']["itrFilled"];
        $filingHistoryData =  $data['result']["itrFilled"];

        $result["profileData"]['pan_no']              = $profileData['entityPan'];
        $result["profileData"]['name']                = $profileData["natOfBusiness"][0]["tradeName1"];
        $result["profileData"]['dob'] = !empty($profileData['dateOfBirthOrIncorporation'])
            ? \Carbon\Carbon::createFromFormat('Y-m-d', $profileData['dateOfBirthOrIncorporation'])->format('Y-m-d')
            : null;


        $result["profileData"]['aadhaar']             = $profileData['aadharCardNo'];
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

        $result["profileData"]['std']                 = $profileData['contact']['countryCodeMobileNumber'] ?? null;
        $result["profileData"]['email']               = $profileData['contact']['email'] ?? null;
        $result["profileData"]['phone_no']            = $profileData['contact']['mobileNumber'];
        $result["profileData"]['email2']              = $profileData['contact']['emailAddressSecondary'] ?? null;
        $result["profileData"]['mobile']              = $profileData['contact']['mobile'] ?? null;
        $result["profileData"]['mobile2']             = $profileData['contact']['mobileNumberSecondary'] ?? null;
        $result["profileData"]['excelDownloadLink']             =  $data['result']['excelReportLink'] ?? null;
        $result["profileData"]['pdfDownloadLink']             =  $data['result']['pdfDownloadLink'] ?? null;



        $filingHistoryArray = [];
        foreach ($filingHistoryData as $value) {
            $filingDataHistory = [];
            $filingDataHistory["pan"] = $value["pan"];
            $filingDataHistory["assessment_year"] = $value["annualYear"];
            $filingDataHistory["form"] = $value["itrForm"];
            $filingDataHistory["filing_type"] = $value["filingType"];
            $filingDataHistory["status"] = $value["status"];
            if (!empty($value["activity"]) && isset($value["activity"][0])) {
                $filingDataHistory["date"] = $value["activity"][0]["date"];
                $filingDataHistory["downloadLink"] = $value["activity"][0]["downloadsStatus"]["downloadLink"] ?? null;
            } else {
                $filingDataHistory["date"] = null;
                $filingDataHistory["downloadLink"] = null;
            }
            $filingHistoryArray[] = $filingDataHistory;
        }

        $yearlyITR = [];
        foreach ($financialInformation as $itr) {
            $ItData = [];
            $ItData["itrYearly"]['assessment_year'] = $itr['assessmentYear'] ?? "NA";
            $ItData["itrYearly"]['financial_rear'] = $itr['financialYear'] ?? "NA";
            $ItData["itrYearly"]['salary'] = $itr['incomeConcentration']['salary'] ?? 0;
            $ItData["itrYearly"]['house_property'] = $itr['incomeConcentration']['houseProperty'] ?? 0;
            $ItData["itrYearly"]['other_sources'] = $itr['incomeConcentration']['otherSources'] ?? 0;
            $ItData["itrYearly"]['capital_gains'] = $itr['incomeConcentration']['capitalGains'] ?? 0;
            $ItData["itrYearlyFinancial"]['total_current_liability'] = $itr['balanceSheet']['totalCurrentLiability'] ?? 0;
            $ItData["itrYearlyFinancial"]['total_inventory'] = $itr['balanceSheet']['totalInventory'] ?? 0;
            $ItData["itrYearlyFinancial"]['total_assets'] = $itr['balanceSheet']['totalAssets'] ?? 0;
            $ItData["itrYearlyFinancial"]['trade_receivables'] = $itr['balanceSheet']['tradeReceivables'] ?? 0;
            $ItData["itrYearlyFinancial"]['total_liability'] = $itr['balanceSheet']['totalLiability'] ?? 0;
            $ItData["itrYearlyFinancial"]['trade_payable'] = $itr['balanceSheet']['tradePayables'] ?? 0;
            $ItData["itrYearlyFinancial"]['total_investment'] = $itr['balanceSheet']['totalInvestment'] ?? 0;
            $ItData["itrYearlyFinancial"]['total_el'] = $itr['balanceSheet']['totalEL'] ?? 0;
            $ItData["itrYearlyFinancial"]['total_fixed_asset'] = $itr['balanceSheet']['totalFixedAsset'] ?? 0;
            $ItData["itrYearlyFinancial"]['total_equity'] = $itr['balanceSheet']['totalEquity'] ?? 0;
            $ItData["itrYearlyFinancial"]['cash_and_cash_eqv'] = $itr['balanceSheet']['cashAndCashEqv'] ?? 0;
            $ItData["itrYearlyFinancial"]['total_current_asset'] = $itr['balanceSheet']['totalCurrentAsset'] ?? 0;
            $ItData["itrYearlyFinancial"]['non_operating_turnover'] = $itr['turnoverDetails']['nonOperating'] ?? 0;
            $ItData["itrYearlyFinancial"]['operating_turnover'] = $itr['turnoverDetails']['operating'] ?? 0;
            $ItData["itrYearlyFinancial"]['profit_before_tax'] = $itr['profitAndLoss']['profitBeforeTax'] ?? 0;
            $ItData["itrYearlyFinancial"]['direct_cost'] = $itr['profitAndLoss']['directCost'] ?? 0;
            $ItData["itrYearlyFinancial"]['purchases'] = $itr['profitAndLoss']['purchases'] ?? 0;
            $ItData["itrYearlyFinancial"]['interest_expense'] = $itr['profitAndLoss']['interestExpense'] ?? 0;
            $ItData["itrYearlyFinancial"]['total_expense'] = $itr['profitAndLoss']['totalExpense'] ?? 0;
            $ItData["itrYearlyFinancial"]['income_from_bp'] = $itr['profitAndLoss']['incomeFromBP'] ?? 0;
            $ItData["itrYearlyFinancial"]['income_from_os'] = $itr['profitAndLoss']['incomeFromOS'] ?? 0;
            $ItData["itrYearlyFinancial"]['profit_after_tax'] = $itr['profitAndLoss']['profitAfterTax'] ?? 0;
            $ItData["itrYearlyFinancial"]['gross_profit'] = $itr['profitAndLoss']['grossProfit'] ?? 0;
            $ItData["itrYearlyFinancial"]['income_from_salary'] = $itr['profitAndLoss']['incomeFromSalary'] ?? 0;
            $ItData["itrYearlyFinancial"]['income_from_hp'] = $itr['profitAndLoss']['incomeFromHP'] ?? 0;
            $ItData["itrYearlyFinancial"]['total_tax'] = $itr['profitAndLoss']['totalTax'] ?? 0;
            $ItData["itrYearlyFinancial"]['income_from_cg'] = $itr['profitAndLoss']['incomeFromCG'] ?? 0;
            $ItData["itrYearlyFinancial"]['ebitda'] = $itr['profitAndLoss']['ebitda'] ?? 0;
            $ItData["itrYearlyFinancial"]['total_revenue'] = $itr['profitAndLoss']['totalRevenue'] ?? 0;
            $ItData["itrYearlyFinancial"]['revenue_from_operations'] = $itr['profitAndLoss']['revenueFromOperations'] ?? 0;



            $yearlyITR["filingData"][] = $ItData;
        }

        // return $financialInformation;


        // $result["outstandingDemand"] = $outstandingDemand;
        $result["filingHistory"] = $filingHistoryArray;
        $result["filingData"] = $yearlyITR;




        return [
            'status' => true,
            'message' => 'Verified successfully',
            // 'yearlyITR' => $yearlyITR,
            'data' => $result,
        ];
    }
}
