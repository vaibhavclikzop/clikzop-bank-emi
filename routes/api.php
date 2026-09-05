<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\AuthControllerTenant;
use App\Http\Controllers\API\CommonMasterController;
use App\Http\Controllers\API\CustomerController\CustomerBankController;
use App\Http\Controllers\API\CustomerController\GSTController;
use App\Http\Controllers\API\CustomerController\ITRController;
use App\Http\Controllers\API\LoanController\BankEligibilityReportController;
use App\Http\Controllers\API\LoanController\CibilController;
use App\Http\Controllers\API\LoanController\CoApplicantController;
use App\Http\Controllers\API\LoanController\LoanBankingController;
use App\Http\Controllers\API\LoanController\LoanBankingUploadController;
use App\Http\Controllers\API\LoanController\LoanController;
use App\Http\Controllers\API\LoanController\LoanVanillaReportController;
use App\Http\Controllers\API\SuperAdminController\Masters\AccountTypeController;
use App\Http\Controllers\API\SuperAdminController\Masters\BankController;
use App\Http\Controllers\API\SuperAdminController\Masters\CibilController as MastersCibilController;
use App\Http\Controllers\API\SuperAdminController\Masters\CibilStatusController;
use App\Http\Controllers\API\SuperAdminController\Masters\CommonDocumentController;
use App\Http\Controllers\API\SuperAdminController\Masters\CustomerController;
use App\Http\Controllers\API\SuperAdminController\Masters\DocumentController;
use App\Http\Controllers\API\SuperAdminController\Masters\IncomeTypeController;
use App\Http\Controllers\API\SuperAdminController\Masters\LoanTypeController;
use App\Http\Controllers\API\SuperAdminController\Masters\PlanController;
use App\Http\Controllers\API\SuperAdminController\Masters\RelationshipController;
use App\Http\Controllers\API\SuperAdminController\Masters\TerritoriesController;
use App\Http\Controllers\API\SuperAdminController\Masters\VanillaFieldController;
use App\Http\Controllers\API\SuperAdminController\PartnerManagementController;
use App\Http\Controllers\API\Tenant\TenantController;
use App\Http\Controllers\API\UserManagementController;
use App\Http\Controllers\API\VerificationController;
use App\Http\Controllers\API\WebHook\KarzaWebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/test', function () {});

// Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
// Route::post('/tenant-login', [AuthControllerTenant::class, 'login'])->middleware('throttle:5,1');






Route::post('/login', [AuthController::class, 'login']);
Route::post('/tenant-login', [AuthControllerTenant::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {

    // common master controller
    Route::get('/get-state-district/{district?}', [CommonMasterController::class, 'getStateDistrict'])->name('get-state-district');

    // common user management controller
    Route::get('/get-users', [UserManagementController::class, 'getUsers'])->name('get-users');
    Route::post('/save-user', [UserManagementController::class, 'saveUser'])->name('save-user');
    Route::get('/get-roles', [UserManagementController::class, 'getRoles'])->name('get-roles');
    Route::post('/get-role-base-manager', [UserManagementController::class, 'getRoleBaseManager'])->name('get-role-base-manager');
    Route::get('/get-all-permissions', [UserManagementController::class, 'getAllPermissions'])->name('get-all-permissions');
    Route::get('/get-company-hierarchy', [UserManagementController::class, 'getCompanyHierarchy'])->name('get-company-hierarchy');
    Route::post('/save-role-permission', [UserManagementController::class, 'saveRolePermission'])->name('save-role-permission');

    Route::get('/get-profile-details', [AuthController::class, 'getProfileDetails'])->name('get-profile-details');
    Route::post('/update-profile', [AuthController::class, 'updateProfile'])->name('update-profile');

    // customer management routes
    Route::post('/check-mobile-number', [CustomerController::class, 'checkMobileNumber'])->name('check-mobile-number');
    Route::post('/send-otp', [AuthController::class, 'sendOTP'])->name('send-otp');
    Route::post('/verify-otp', [AuthController::class, 'verifyOTP'])->name('verify-otp');
    Route::post('/save-customer', [CustomerController::class, 'saveCustomer'])->name('save-customer');
    Route::post('/save-customer-details', [CustomerController::class, 'saveCustomerDetails'])->name('save-customer-details');


    Route::post('/check-document-verification', [CustomerController::class, 'checkDocumentVerification'])->name('check-document-verification');

    Route::get('/get-customers/{offset}/{limit}', [CustomerController::class, 'getCustomers'])->name('get-customers');
    Route::get('/get-customer-details/{id}', [CustomerController::class, 'getCustomerDetails'])->name('get-customer-details');
    Route::get('/get-customer-loan/{customer_id}', [CustomerController::class, 'getCustomerLoan'])->name('get-customer-loan');
    // loan Controller
    Route::middleware(['permission:view_loan'])
        ->get('/get-loan/{offset}/{limit}/{statusID?}', [LoanController::class, 'getLoan'])->name('get-loan');

    Route::middleware(['permission:view_loan'])
        ->get('/get-loan-details/{id}', [LoanController::class, 'getLoanDetails'])->name('get-loan-details');
    // Route::middleware(['permission:view_company_profile'])
    //     ->get('/tenant/get-company-profile', [LoanController::class, 'tenantCompanyProfile']);

    Route::middleware(['permission:create_loan'])
        ->post('/save-loan', [LoanController::class, 'saveLoan'])->name('save-loan');

  Route::post('/save-co-applicant', [CoApplicantController::class, 'saveCoApplicant'])->name('save-co-applicant');
        

    Route::get('/get-status', [LoanController::class, 'getStatus'])->name('get-status');
    Route::get('/get-loan-check-list/{id}', [LoanController::class, 'getLoanCheckList'])->name('get-loan-check-list');
    Route::post('/upload-check-list-document', [LoanController::class, 'uploadChecklistDocument'])->name('upload-check-list-document');

    Route::get('/get-uploaded-document/{id}', [LoanController::class, 'getUploadedDocument'])->name('get-uploaded-document');

    Route::post('/save-cibil-data', [LoanController::class, 'saveCibilData'])->name('save-cibil-data');

    //cibil controller 
    Route::post('/upload-cibil-pdf', [CibilController::class, 'uploadCibilPDF'])->name('upload-cibil-pdf');
    Route::post('/get-cibil-data', [CibilController::class, 'getCibilData'])->name('get-cibil-data');



    Route::post('/save-gst-running-year', [LoanController::class, 'saveGSTRunningYear'])->name('save-gst-running-year');
    Route::post('/save-gst-running-year-amount', [LoanController::class, 'saveGSTRunningYearAmount'])->name('save-gst-running-year-amount');


    //banking controller
    Route::post('/save-banking', [LoanBankingController::class, 'saveBanking'])->name('save-banking');
    Route::post('/save-banking-amount', [LoanBankingController::class, 'saveBankingAmount'])->name('save-banking-amount');
    Route::post('/upload-bank-statement', [LoanBankingController::class, 'uploadBankStatement'])->name('upload-bank-statement');
    Route::post('/create-banking-consent', [LoanBankingController::class, 'createBankingConsent'])->name('create-banking-consent');
    Route::post('/check-banking-consent', [LoanBankingController::class, 'checkBankingConsent'])->name('check-banking-consent');
    Route::post('/get-bank-statement', [LoanBankingController::class, 'getBankStatement'])->name('get-bank-statement');


    // banking upload controller 

    Route::post('/initiate-bank-statement', [LoanBankingUploadController::class, 'initiateBankStatement'])->name('initiate-bank-statement');













    Route::post('/save-customer-bank', [CustomerBankController::class, 'saveCustomerBank'])->name('save-customer-bank');
    Route::get('/get-customer-bank/{id}', [CustomerBankController::class, 'getCustomerBank'])->name('get-customer-bank');
    Route::post('/check-ifsc-code', [CustomerBankController::class, 'checkIfscCode'])->name('check-ifsc-code');

    // verify customer documents
    Route::post('/check-aadhar-card', [VerificationController::class, 'checkAadharCard'])->name('check-aadhar-card');
    Route::post('/check-pan-card', [VerificationController::class, 'checkPanCard'])->name('check-pan-card');
    Route::post('/check-driving-license', [VerificationController::class, 'checkDrivingLicense'])->name('check-driving-license');
    Route::post('/check-passport', [VerificationController::class, 'checkPassport'])->name('check-passport');
    Route::post('/get-company-details', [VerificationController::class, 'getCompanyDetails'])->name('get-company-details');
    Route::post('/get-gst-details-via-pan', [VerificationController::class, 'getGSTDetailsPan'])->name('get-gst-details-via-pan');
    Route::post('/get-gst-details-by-gstin', [VerificationController::class, 'getGSTDetailsByGSTIN'])->name('get-gst-details-by-gstin');


    //get ITR details
    Route::post('/get-salaried-itr-details', [ITRController::class, 'getSalariedITRDetails'])->name('get-salaried-itr-details');


    Route::post('/get-business-itr-details', [ITRController::class, 'getBusinessITRDetails'])->name('get-business-itr-details');





    //gst details by TRN
    Route::post('/send-otp-for-gst-filling', [GSTController::class, 'sendOTPForGstFilling'])->name('send-otp-for-gst-filling');
    Route::post('/verify-otp-for-gst-filling', [GSTController::class, 'verifyOTPForGstFilling'])->name('verify-otp-for-gst-filling');


    //background verification 
    Route::post('/check-background-verification', [VerificationController::class, 'checkBackgroundVerification'])->name('check-background-verification');
    Route::post('/update-background-verification', [VerificationController::class, 'updateBackgroundVerification'])->name('update-background-verification');


    // vanilla report controller 
    Route::post('/save-vanilla-year', [LoanVanillaReportController::class, 'saveVanillaYear'])->name('save-vanilla-year');
    Route::post('/update-vanilla-values', [LoanVanillaReportController::class, 'updateVanillaValues'])->name('update-vanilla-values');

    Route::post('/update-vanilla-summary', [LoanVanillaReportController::class, 'updateVanillaSummary'])->name('update-vanilla-summary');


    //bank eligibility controller


    Route::post('/generate-bank-eligibility', [BankEligibilityReportController::class, 'generateBankEligibility'])->name('generate-bank-eligibility');


    // super admin
    Route::middleware(['role:super_admin'])->group(function () {
        Route::get('/dashboard', function () {});

        Route::get('/logout', [AuthController::class, 'Logout'])->name('logout');

        Route::post('/create-partner', [PartnerManagementController::class, 'createPartner']);
        Route::get('/get-partners', [PartnerManagementController::class, 'getPartners'])->name('get-partners');

        // master controller
        // bank curd
        Route::get('/get-banks', [BankController::class, 'index'])->name('get-banks');
        Route::post('/save-bank', [BankController::class, 'saveBank'])->name('save-bank');

        Route::post('/save-loan-type', [LoanTypeController::class, 'saveLoanType'])->name('save-loan-type');
        Route::get('/get-loan-type', [LoanTypeController::class, 'index'])->name('get-loan-type');

        Route::post('/save-income-type', [IncomeTypeController::class, 'saveIncomeType'])->name('save-income-type');
        Route::get('/get-income-type', [IncomeTypeController::class, 'index'])->name('get-income-type');

        Route::post('/save-territories', [TerritoriesController::class, 'saveTerritories'])->name('save-territories');
        Route::get('/get-territories', [TerritoriesController::class, 'index'])->name('get-territories');

        Route::post('/save-plan', [PlanController::class, 'savePlan'])->name('save-plan');
        Route::get('/get-plans', [PlanController::class, 'index'])->name('get-plans');

        Route::get('/get-common-documents', [CommonDocumentController::class, 'index'])->name('get-common-documents');
        Route::post('/save-common-document', [CommonDocumentController::class, 'saveCommonDocument'])->name('save-common-document');

        Route::post('/save-document', [DocumentController::class, 'saveDocument'])->name('save-document');
        Route::get('/get-document', [DocumentController::class, 'index'])->name('get-document');

        Route::post('/save-account-type', [AccountTypeController::class, 'saveAccountType'])->name('save-account-type');
        Route::get('/get-account-type', [AccountTypeController::class, 'index'])->name('get-account-type');

        Route::post('/save-cibil-status', [CibilStatusController::class, 'saveCibilStatus'])->name('save-cibil-status');
        Route::get('/get-cibil-status', [CibilStatusController::class, 'index'])->name('get-cibil-status');


        Route::get('/get-vanilla-field', [VanillaFieldController::class, 'index'])->name('get-vanilla-field');
        Route::post('/save-vanilla-field', [VanillaFieldController::class, 'saveVanillaField'])->name('save-vanilla-field');



        Route::get('/get-relationship', [RelationshipController::class, 'index'])->name('get-relationship');
        Route::post('/save-relationship', [RelationshipController::class, 'saveRelationship'])->name('save-relationship');



        Route::post('/fetch-cibil-score', [MastersCibilController::class, 'fetchCIBIL'])->name('fetch-cibil-score');
        Route::get('/get-cibil-score-list/{offset}/{limit}', [MastersCibilController::class, 'getCibilScoreList'])->name('get-cibil-score-list');
    });

    Route::middleware(['role:dsa_admin', 'tenant'])->group(function () {
        Route::post('/tenant-dashboard', [PartnerManagementController::class, 'tenantDashboard']);

        Route::middleware(['permission:view_company_profile'])
            ->get('/tenant/get-company-profile', [TenantController::class, 'tenantCompanyProfile']);
    });

    Route::middleware(['tenant'])->group(function () {

        // Route::get('/dashboard', function () {
        //     return "Dashboard";
        // });
    });
});
//webhooks
Route::any('/webhook/karza-webhook', [KarzaWebhookController::class, 'receiveWebhook'])->name("/webhook/karza-webhook");
