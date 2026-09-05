<?php

namespace App\Http\Controllers\API\SuperAdminController\Masters;

use App\Http\Controllers\Controller;
use App\Models\accountType;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AccountTypeController extends Controller
{
    use AuthorizesRequests;

    public function saveAccountType(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
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

                $data = accountType::find($request->id);

                if (! $data) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Not found',
                        'data' => [],
                    ], 404);
                }

                $this->authorize('update', $data);

                $data->update([
                    'name' => $request->name,

                ]);
            } else {
                $this->authorize('create', accountType::class);
                $data = accountType::create([
                    'name' => $request->name,

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

    public function index(Request $request)
    {
        $this->authorize('viewAny', accountType::class);

        return response()->json([
            'status' => true,
            'message' => 'Load Successfully',
            'data' => accountType::latest()->get(),
        ]);
    }
}
