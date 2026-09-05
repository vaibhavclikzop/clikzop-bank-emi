<?php

namespace App\Services\LoanReports;

use App\Models\LoanVanillaReportSummary;
use App\Models\vanillaReportMst;
use Illuminate\Support\Facades\Http;

class VanillaReportService
{
    public function vanillaReport(int $id)
    {

        try {



            $mst = vanillaReportMst::with(
                "customerDetails:name,id",
                "years.values",

                'fields.fieldDetails'
            )
                ->where("loan_id", $id)->get();


            $response = [];
            foreach ($mst as $report) {


                $years = $report->years
                    ->sortByDesc('financial_year')
                    ->values();


                $headerYears = [];

                foreach ($years as $year) {
                    $headerYears[] = $year->financial_year;
                }

                $rows = [];




                foreach ($report->fields as $field) {
                    // echo json_encode($field);
                    $row = [
                        'field_id' => $field->id,
                        'display_name' => $field->fieldDetails->display_name,
                        'years' => [],
                        'average' => $field->average,
                        'eligible_percentage' => $field->eligible_percentage,
                        'eligible_income' => $field->eligible_income,

                    ];



                    foreach ($years as $year) {

                        $value =
                            $year->values
                            ->where('vanilla_field_id', $field->vanilla_field_id)
                            ->first();



                        $row['years'][] = [

                            'id' => $value->id,
                            'year' => $year->financial_year,
                            'amount' => $value->amount,
                            'field_name' => $value->field_name,
                            'display_name' => $value->display_name,

                        ];
                    }

                    $rows[] = $row;
                }

                $response[] = [
                    'customer_id' => $report->customerDetails->id,
                    'customer_name' =>  $report->customerDetails->name,
                    'years' => $headerYears,
                    'rows' => $rows,

                ];
            }

            $summary =  LoanVanillaReportSummary::where("loan_id", $id)->first();
            $response[] = [
                "summary" => $summary
            ];


            return  [
                'status' => true,
                'message' => "Fetch Successfully",
                'data' => $response
            ];
        } catch (\Throwable $th) {
            return  [
                'status' => false,
                'message' => $th->getMessage(),
                'data' => []
            ];
        }
    }
}
