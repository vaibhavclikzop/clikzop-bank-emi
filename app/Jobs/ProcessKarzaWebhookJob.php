<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\customerGstDetailFilling;
use App\Models\customerGstDetails;
use App\Models\customerGSTR1Details;
use App\Models\customerGSTR3bDetails;
use App\Models\customerGSTR3bITCDetails;
use App\Models\customerGSTR3bSupplyDetails;
use App\Models\LoanGST\LoanGSTDetailFillings;
use App\Models\LoanGST\LoanGSTDetails;
use App\Models\LoanGST\LoanGSTR1Details;
use App\Models\LoanGST\LoanGSTR3bDetails;
use App\Models\LoanGST\LoanGSTR3bITCDetails;
use App\Models\LoanGST\LoanGSTR3BSupplyDetails;
use App\Models\vanillaReportMst;
use App\Models\webhook;
use Carbon\Carbon;

class ProcessKarzaWebhookJob implements ShouldQueue
{
    use Queueable;
    protected $webhookId;
    /**
     * Create a new job instance.
     */
    public function __construct($webhookId = null)
    {
        $this->webhookId = $webhookId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $webhook = webhook::find($this->webhookId);

        if (!$webhook) {
            Log::error("Webhook not found: {$this->webhookId}");
            return;
        }



        $path = base_path('webhook_files/' . $webhook->payload);

        if (!file_exists($path)) {
            $webhook->update(array(
                "processed" => 1,
                "processed_at" => now(),
                "status" => "failed",
                "error" => "Webhook file not found: {$path}",
            ));
            Log::error("Webhook file not found: {$path}");

            return;
        }



        $json = json_decode(file_get_contents($path), true);
        if (json_last_error() !== JSON_ERROR_NONE || empty($json['result'])) {
            $webhook->update(array(
                "processed" => 1,
                "processed_at" => now(),
                "status" => "failed",
                "error" => "Invalid Response",
            ));


            Log::error("Invalid Response");

            return;
        }

        DB::beginTransaction();
        try {




            $profile = $json['result']["profile"];
            // $filingData = $json['result']["previous"]["filingStatus"];
            // $gstr1 = $json['result']["previous"]["gstr1"]["details"];
            // $gstr3b = $json['result']["previous"]["gstr3b"]["details"];
            // echo  json_encode($gstr3b);

            $customerGstDetails = LoanGSTDetails::where("id", $webhook->loan_gst_details_id)->first();
            if (!$customerGstDetails) {
                $webhook->update(array(
                    "processed" => 1,
                    "processed_at" => now(),
                    "status" => "failed",
                    "error" => "GST no. not found",
                ));
                DB::commit();
                return;
            }

            $profilePayload = [
                'company_name' => $profile['lgnm'] ?? null,
                'email_id' => $profile['contacted']['email'] ?? null,
                'gst_in' => $json['result']['gstin'] ?? null,
                'gst_in_ref' => null,
                'mobile' => $profile['contacted']['mobNum'] ?? null,
                'pan' => !empty($json['result']['gstin'])
                    ? substr($json['result']['gstin'], 2, 10)
                    : null,
                'registration_name' => $profile['tradeNam'] ?? null,
                'tin_number' => null,
                'state' => $profile['addressDetails']['state'] ?? null,
                'stjCd' => $profile['stjCd'] ?? null,
                'dty' => $profile['dty'] ?? null,
                'stj' => $profile['stj'] ?? null,
                'nba' => !empty($profile['nba'])
                    ? json_encode($profile['nba'])
                    : null,
                'ctb' => $profile['ctb'] ?? null,
                'registration_date' => !empty($profile['vintage']['dateOfRegistration'])
                    ? Carbon::createFromFormat('d/m/Y', $profile['vintage']['dateOfRegistration'])->format('Y-m-d')
                    : (!empty($profile['rgdt'])
                        ? Carbon::createFromFormat('d/m/Y', $profile['rgdt'])->format('Y-m-d')
                        : null),
                'address' => $profile['address'] ?? null,
                'trade_name' => $profile['tradeNam'] ?? null,
                'ctjCd' => $profile['ctjCd'] ?? null,
                'status' => isset($profile['status']['isActive'])
                    ? ($profile['status']['isActive'] ? 'Active' : 'Inactive')
                    : ($profile['sts'] ?? null),
                'ctj' => $profile['ctj'] ?? null,
                'e_invoice_status' => null,
                'api_response' => json_encode($json['result']["profile"]),

            ];


            $customerGstDetails->update($profilePayload);
            //  Previous Data
            if (!empty($json['result']['previous'])) {
                $this->saveGSTData(
                    $customerGstDetails,
                    $json['result']['previous']
                );
            }

            // Current Data
            if (!empty($json['result']['current'])) {
                $this->saveGSTData(
                    $customerGstDetails,
                    $json['result']['current']
                );
            }

            $this->getTurnOver($customerGstDetails->id, $customerGstDetails->loan_id);

            $webhook->update(array(
                "processed" => 1,
                "processed_at" => now(),
                "status" => "success",
                "error" => "Process successfully",
            ));
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th);
            $webhook->update(array(
                "processed" => 1,
                "processed_at" => now(),
                "status" => "failed",
                "error" => $th->getMessage(),
            ));
        }
    }



    private function saveGSTData($customerGstDetails, array $data)
    {
        $filingData = $data['filingStatus'] ?? [];
        $gstr1 = $data['gstr1']['details'] ?? [];
        $gstr3b = $data['gstr3b']['details'] ?? [];


        foreach ($filingData as $status) {
            $retPeriod = $status['retPeriod'];
            foreach ($status["status"] as $key) {
                LoanGSTDetailFillings::updateOrCreate(
                    [
                        'gst_details_id' => $customerGstDetails->id,
                        'return_type'    => $key['rtntype'],
                        'arn'        => $key['arn'],
                    ],
                    [
                        'arn'        => $key['arn'],
                        'valid'      => $key['valid'],
                        'mof'        => $key['mof'],
                        'dof'        => !empty($key['dof'])
                            ? Carbon::createFromFormat('d-m-Y', $key['dof'])->format('Y-m-d')
                            : null,
                        'status'     => $key['status'],
                        'filed_date' => $key['dof'] ?? null,
                        'due_date'   => !empty($key['dueDt'])
                            ? Carbon::createFromFormat('Y-m-d', $key['dueDt'])->format('Y-m-d')
                            : null,
                        'is_delay'   => $key['isDelay'],
                        'delay_days' => $key['delayDays'],
                        'ret_prd'        => $key['retPrd'] ?? null,
                    ]
                );
            }
        }
        foreach ($gstr1 as $month) {

            foreach ($month['secSum'] as $section) {
                LoanGSTR1Details::updateOrCreate(
                    [
                        'gst_details_id' => $customerGstDetails->id,
                        'ret_period' => Carbon::createFromFormat('mY', $month['retPrd'])
                            ->startOfMonth()
                            ->format('Y-m-d'),
                        'section_name'   => $section['secNm'],
                    ],
                    [
                        'total_records' => $section['ttlRec'] ?? 0,
                        'taxable_value' => $section['ttlTax'] ?? 0,
                        'igst'          => $section['ttlIgst'] ?? 0,
                        'cgst'          => $section['ttlCgst'] ?? 0,
                        'sgst'          => $section['ttlSgst'] ?? 0,
                        'cess'          => $section['ttlCess'] ?? 0,
                        'total_value'   => $section['ttlVal'] ?? 0,
                        'checksum'      => $section['chksum'] ?? null,
                    ]
                );
            }
        }

        foreach ($gstr3b as $row) {
            $month = substr($row['retPeriod'], 0, 2);
            $year  = substr($row['retPeriod'], 2, 4);

            $date = Carbon::createFromDate($year, $month, 1);

            $summary = LoanGSTR3bDetails::updateOrCreate(
                [
                    'gst_details_id'      => $customerGstDetails->id,
                    'ret_period'          => $date->format('Y-m-d'),

                ],
                [
                    'ttl_tax_payable'     => $row['ttlTaxPayable'] ?? 0,
                    'ttl_tax_paid'        => $row['ttlTaxPaid'] ?? 0,
                    'ttl_itc_paid'        => $row['ttVal']['ttItcPd'] ?? 0,
                    'ttl_cash_paid'       => $row['ttVal']['ttCshPd'] ?? 0,

                    'ttl_late_fee'        => $row['ttlLateFee'] ?? 0,
                    'ttl_interest'        => $row['ttlIntr'] ?? 0,

                    'opening_balance'     => $row['openingBal'] ?? 0,
                    'closing_balance'     => $row['closingBal'] ?? 0,

                    'itc_avl_by_gstr2a'   => $row['itcAvlByGstr2a'] ?? 0,
                ]
            );


            foreach ($row['supDetails'] as $type => $sup) {

                LoanGSTR3BSupplyDetails::updateOrCreate(
                    [
                        'gstr3b_details_id' => $summary->id,
                        'type'              => $type,
                    ],
                    [

                        'txval'             => $sup['txval'] ?? 0,
                        'iamt'              => $sup['iamt'] ?? 0,
                        'camt'              => $sup['camt'] ?? 0,
                        'samt'              => $sup['samt'] ?? 0,
                        'csamt'             => $sup['csamt'] ?? 0,
                    ]
                );
            }

            foreach ($row['itcElg'] as $category => $data) {

                // Skip total fields
                if (in_array($category, [
                    'itcAvlTotal',
                    'itcRevTotal',
                    'itcNetTotal',
                    'itcInelgTotal'
                ])) {
                    continue;
                }


                if ($category == 'itcNet') {

                    LoanGSTR3bITCDetails::updateOrCreate(
                        [
                            'gstr3b_details_id' => $summary->id,
                            'section' => $category,
                            'type' => 'NET',
                        ],
                        [
                            'iamt'  => $data['iamt'] ?? 0,
                            'camt'  => $data['camt'] ?? 0,
                            'samt'  => $data['samt'] ?? 0,
                            'csamt' => $data['csamt'] ?? 0,
                            'total' => $data['total'] ?? 0,
                        ]
                    );
                } else {


                    foreach ($data as $itc) {

                        LoanGSTR3bITCDetails::updateOrCreate(
                            [
                                'gstr3b_details_id' => $summary->id,
                                'section' => $category,
                                'type' => $itc['ty'],
                            ],
                            [
                                'iamt'  => $itc['iamt'] ?? 0,
                                'camt'  => $itc['camt'] ?? 0,
                                'samt'  => $itc['samt'] ?? 0,
                                'csamt' => $itc['csamt'] ?? 0,
                                'total' => $itc['total'] ?? 0,
                            ]
                        );
                    }
                }
            }
        }
    }


    private function getTurnOver($gst_details_id, $loan_id)
    {

        $vanillaReportMst = VanillaReportMst::select('id', 'loan_id')
            ->with('years:id,financial_year,vanilla_mst_id')
            ->where('loan_id', $loan_id)
            ->first();
        foreach ($vanillaReportMst->years as $yr) {
            $year = $yr->financial_year;
        

            [$startYear, $endYear] = explode('-', $year);

            $startYear = (int) $startYear;
            $endYear = $startYear + 1;

            $startDate = $startYear . '-04-01';
            $endDate   = $endYear . '-03-01';

            $totalTaxPayable = DB::table('loan_gstr3b_details')
                ->where('gst_details_id', $gst_details_id)
                ->whereBetween('ret_period', [$startDate, $endDate])
                ->sum('ttl_tax_payable');
            updateVanillaReportYear($yr->id,$totalTaxPayable,$loan_id);
        }
      
    }
}
