<?php

namespace App\Providers;

use App\Models\accountType;
use App\Models\Bank;
use App\Models\cibilStatus;
use App\Models\CommonDocument;
use App\Models\CustomerBanks;
use App\Models\CustomerDocuments;
use App\Models\Customers;
use App\Models\Documents;
use App\Models\IfscMaster;
use App\Models\IncomeType;
use App\Models\Loan;
use App\Models\loanBankingDet;
use App\Models\loanBankingMst;
use App\Models\LoanCibilDataMst;
use App\Models\LoanDocument;
use App\Models\LoanGSTRunningYearDet;
use App\Models\LoanGSTRunningYearMst;
use App\Models\LoanType;
use App\Models\Plans;
use App\Models\TenantTerritories;
use App\Models\Territories;
use App\Models\User;
use App\Models\vanillaField;
use App\Observers\ActivityLogObserver;
use App\Policies\CustomerPolicy;
use App\Policies\GlobalMasterPolicy;
use App\Policies\LoanPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use App\Contracts\OtpProviderInterface;
use App\Services\Sms\SmsProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            OtpProviderInterface::class,
            SmsProvider::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // policy
        // global policy
        Gate::policy(Bank::class, GlobalMasterPolicy::class);
        Gate::policy(LoanType::class, GlobalMasterPolicy::class);
        Gate::policy(IncomeType::class, GlobalMasterPolicy::class);
        Gate::policy(Territories::class, GlobalMasterPolicy::class);
        Gate::policy(Plans::class, GlobalMasterPolicy::class);
        Gate::policy(CommonDocument::class, GlobalMasterPolicy::class);
        Gate::policy(Documents::class, GlobalMasterPolicy::class);
        Gate::policy(accountType::class, GlobalMasterPolicy::class);
        Gate::policy(cibilStatus::class, GlobalMasterPolicy::class);
        Gate::policy(vanillaField::class, GlobalMasterPolicy::class);

        // customer policy
        Gate::policy(Customers::class, CustomerPolicy::class);

        // user policy
        Gate::policy(User::class, UserPolicy::class);

        // loan policy
        Gate::policy(Loan::class, LoanPolicy::class);
        Gate::policy(LoanDocument::class, LoanPolicy::class);
        Gate::policy(LoanCibilDataMst::class, LoanPolicy::class);
        Gate::policy(LoanGSTRunningYearMst::class, LoanPolicy::class);
        Gate::policy(LoanGSTRunningYearDet::class, LoanPolicy::class);

        // observer
        User::observe(ActivityLogObserver::class);
        Bank::observe(ActivityLogObserver::class);
        LoanType::observe(ActivityLogObserver::class);
        IncomeType::observe(ActivityLogObserver::class);
        Customers::observe(ActivityLogObserver::class);
        Territories::observe(ActivityLogObserver::class);
        vanillaField::observe(ActivityLogObserver::class);
        CommonDocument::observe(ActivityLogObserver::class);
        TenantTerritories::observe(ActivityLogObserver::class);
        Plans::observe(ActivityLogObserver::class);
        Loan::observe(ActivityLogObserver::class);
        LoanDocument::observe(ActivityLogObserver::class);
        accountType::observe(ActivityLogObserver::class);
        cibilStatus::observe(ActivityLogObserver::class);
        LoanCibilDataMst::observe(ActivityLogObserver::class);
        LoanGSTRunningYearMst::observe(ActivityLogObserver::class);
        LoanGSTRunningYearDet::observe(ActivityLogObserver::class);
        loanBankingMst::observe(ActivityLogObserver::class);
        loanBankingDet::observe(ActivityLogObserver::class);
        CustomerBanks::observe(ActivityLogObserver::class);
        IfscMaster::observe(ActivityLogObserver::class);
        CustomerDocuments::observe(ActivityLogObserver::class);
    }
}
