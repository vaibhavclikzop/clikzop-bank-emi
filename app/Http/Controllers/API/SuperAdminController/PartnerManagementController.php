<?php

namespace App\Http\Controllers\API\SuperAdminController;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\TenantTerritories;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PartnerManagementController extends Controller
{
    public function createPartner(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'company_name' => 'required',
            'name' => 'required',
            'mobile' => 'required',
            'role' => 'required',
            'password' => 'required',
            'territory_id' => 'required',

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

            $tenant = Tenant::create([
                'name' => $request->name,
                'company_name' => $request->company_name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'domain' => $request->domain,
                'expiry_date' => $request->expiry_date,
                'pan_no' => $request->pan_no,
                'aadhar_no' => $request->aadhar_no,
                'gst_in' => $request->gst_in,
                'state' => $request->state,
                'district' => $request->district,
                'city' => $request->city,
                'address' => $request->address,
                'pincode' => $request->pincode,
                'bank_name' => $request->bank_name,
                'bank_account_no' => $request->bank_account_no,
                'ifsc_code' => $request->ifsc_code,
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'parent_id' => auth()->id(),
                'tenant_id' => $tenant->id,
            ]);

            foreach (explode(', ', $request->territory_id) as $key => $value) {
                TenantTerritories::create([
                    'territory_id' => $value,

                    'tenant_id' => $tenant->id,
                ]);
            }

            $role = Role::where('name', $request->role)->first();

            if (! $role) {

                return response()->json([
                    'status' => false,
                    'message' => 'Role Not found',
                    'data' => [],
                ], 401);
            }

            $user->roles()->sync([$role->id]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Save Successfully',
                'tenant' => $tenant,
                'user' => $user,
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function tenantDashboard() {}

    public function getPartners(Request $request)
    {
        $data = Tenant::get();

        return response()->json([
            'status' => true,
            'message' => 'Load Successfully',
            'data' => $data,

        ]);
    }
}
