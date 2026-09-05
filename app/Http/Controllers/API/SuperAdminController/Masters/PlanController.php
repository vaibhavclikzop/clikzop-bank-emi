<?php

namespace App\Http\Controllers\API\SuperAdminController\Masters;

use App\Http\Controllers\Controller;
use App\Models\Plans;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PlanController extends Controller
{
    use AuthorizesRequests;

    public function savePlan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'expire_days' => 'required',
            'commission' => 'required',
            'commission_type' => 'required',
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

                $data = Plans::find($request->id);

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
                    'description' => $request->description,
                    'expire_days' => $request->expire_days,
                    'commission' => $request->commission,
                    'commission_type' => $request->commission_type,
                    'status' => $request->status,

                ]);
            } else {
                $this->authorize('create', Plans::class);
                $data = Plans::create([
                    'name' => $request->name,
                    'description' => $request->description,
                    'expire_days' => $request->expire_days,
                    'commission' => $request->commission,
                    'commission_type' => $request->commission_type,
                    'status' => 1,
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

    public function index()
    {
        $this->authorize('viewAny', Plans::class);

        return response()->json([
            'status' => true,
            'message' => 'Load Successfully',
            'data' => Plans::latest()->get(),
        ]);
    }
}
