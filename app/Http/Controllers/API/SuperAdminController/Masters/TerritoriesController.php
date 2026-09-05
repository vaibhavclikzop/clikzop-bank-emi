<?php

namespace App\Http\Controllers\API\SuperAdminController\Masters;

use App\Http\Controllers\Controller;
use App\Models\Territories;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TerritoriesController extends Controller
{
    use AuthorizesRequests;

    public function saveTerritories(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'state' => 'required',
            'district' => 'required',
            'city' => 'required',
            'status' => 'required',
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

                $data = Territories::find($request->id);

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
                    'state' => $request->state,
                    'city' => $request->city,
                    'district' => $request->district,
                    'status' => $request->status,

                ]);
            } else {
                $this->authorize('create', Territories::class);
                $data = Territories::create([
                    'name' => $request->name,
                    'state' => $request->state,
                    'city' => $request->city,
                    'district' => $request->district,
                    'status' => $request->status,
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
        $this->authorize('viewAny', Territories::class);

        return response()->json([
            'status' => true,
            'message' => 'Load Successfully',
            'data' => Territories::latest()->get(),
        ]);
    }
}
