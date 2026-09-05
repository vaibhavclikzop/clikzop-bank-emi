<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class customerITRYearlyFinancials extends Model
{
    protected $table = "customer_itr_yearly_financial";
    protected $primaryKey = 'id';

    protected $fillable = [
        'customer_id',
        'customer_itr_yearly_id',


        'total_current_liability',
        'total_inventory',
        'total_assets',
        'trade_receivables',
        'total_liability',
        'trade_payable',
        'total_investment',
        'total_el',
        'total_fixed_asset',
        'total_equity',
        'cash_and_cash_eqv',
        'total_current_asset',


        'non_operating_turnover',
        'operating_turnover',


        'profit_before_tax',
        'direct_cost',
        'purchases',
        'interest_expense',
        'total_expense',
        'income_from_bp',
        'income_from_os',
        'profit_after_tax',
        'gross_profit',
        'income_from_salary',
        'income_from_hp',
        'total_tax',
        'income_from_cg',
        'ebitda',
        'total_revenue',
        'revenue_from_operations',
    ];
}
