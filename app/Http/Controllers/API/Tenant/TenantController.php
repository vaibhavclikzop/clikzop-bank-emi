<?php

namespace App\Http\Controllers\API\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function tenantCompanyProfile(Request $request)
    {
        $user = auth()->user();

        $tenant = Tenant::where('id', $user->tenant_id)->first();

        if (! $tenant) {
            return response()->json([
                'status' => false,
                'message' => 'Tenant not found',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Data Load Successfully',
            'data' => [
                'id' => $tenant->id,
                'name' => $tenant->name,
                'company_name' => $tenant->company_name,
                'email' => $tenant->email,
                'mobile' => $tenant->mobile,
                'domain' => $tenant->domain,
                'expiry_date' => $tenant->expiry_date,
                'state' => $tenant->state,
                'district' => $tenant->district,
                'city' => $tenant->city,
                'address' => $tenant->address,
                'pincode' => $tenant->pincode,

                // 🔐 Masked data
                'pan' => $tenant->pan_masked,
                'aadhar' => $tenant->aadhar_masked,
                'bank' => $tenant->bank_masked,
            ],
        ]);
    }
}
