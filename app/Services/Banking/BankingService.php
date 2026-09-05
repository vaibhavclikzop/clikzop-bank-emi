<?php

namespace App\Services\Banking;

use App\Models\loan_banking_transactions;
use App\Models\loanBankingDet;
use Illuminate\Support\Facades\Http;

class BankingService
{
    public function calculate($mstID, $loanID)
    {

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

            loanBankingDet::updateOrCreate(
                [
                    'loan_id' => $loanID,
                    'mst_id' => $mstID,
                    'month' => $month,
                ],
                [
                    'month_name' => $monthName,

                ]
            );
        }

        $loanBankingDet =  loanBankingDet::where("mst_id", $mstID)->get();

        foreach ($loanBankingDet as $bank) {

            $month = $bank->month;

            foreach ([5, 15, 25] as $day) {

                $transaction = loan_banking_transactions::where('loan_banking_mst_id', $mstID)
                    ->whereMonth('txn_date', $month)
                    ->whereDay('txn_date', '<=', $day)
                    ->orderBy('txn_date', 'desc')
                    ->orderBy('id', 'desc')
                    ->first();

                $balance = $transaction?->balance;


                loanBankingDet::where("id", $bank->id)->update(array(
                    "closing_balance_{$day}" => $balance ?? 0,
                ));

                $detail = loanBankingDet::where('id', $bank->id)
                    ->where('loan_id', $bank->loan_id)
                    ->first();

                $data =
                    $detail->closing_balance_5 +
                    $detail->closing_balance_15 +
                    $detail->closing_balance_20 +
                    $detail->closing_balance_25 +
                    $detail->closing_balance_30;

                loanBankingDet::where('id', $bank->id)->where('loan_id', $bank->loan_id)->update([
                    'monthly_total' => $data,
                    'monthly_average' => $data / 3,
                ]);

                // dump([
                //     'month'   => $month,
                //     'day'     => $day,
                //     'balance' => $balance,
                //     'date'    => $transaction?->txn_date,
                // ]);
            }
        }
        return [

            'status' => true,
        ];
    }
}
