<?php

namespace App\Http\Controllers\API\SuperAdminController\Masters;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BankController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Bank::class);

        return response()->json([
            'status' => true,
            'data' => Bank::latest()->get(),
        ]);
    }

    public function saveBank(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'ifsc_code' => 'required',
            'branch_name' => 'required',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => [],
            ], 422);
        }

        try {
            if ($request->id) {

                $data = Bank::where('id', $request->id)->first();

                if (! $data) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Bank not found',
                        'data' => [],
                    ], 404);
                }

                $this->authorize('update', $data);

                $data->update([
                    'name' => $request->name,
                    'ifsc_code' => $request->ifsc_code,
                    'branch_name' => $request->branch_name,
                ]);
            } else {
                $this->authorize('create', Bank::class);
                $data = Bank::create([
                    'name' => $request->name,
                    'ifsc_code' => $request->ifsc_code,
                    'branch_name' => $request->branch_name,

                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'data' => [],
            ], 422);
        }

        return response()->json([
            'status' => true,
            'message' => 'Save Successfully',
            'data' => $data,
        ], 200);
    }
}
