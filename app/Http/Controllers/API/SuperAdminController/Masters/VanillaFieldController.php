<?php

namespace App\Http\Controllers\API\SuperAdminController\Masters;

use App\Http\Controllers\Controller;
use App\Models\vanillaField;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class VanillaFieldController extends Controller
{
    use AuthorizesRequests;

    public function saveVanillaField(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
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

                if ($request->id <= 11) {
                    return response()->json([
                        'status' => false,
                        'message' => 'This field can not be update',
                        'data' => [],
                    ], 422);
                }
                $data = vanillaField::find($request->id);

                if (! $data) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Not found',
                        'data' => [],
                    ], 404);
                }

                $this->authorize('update', $data);

                $data->update([
                    'display_name' => $request->name,
                    'field_name' => Str::snake($request->name),


                ]);
            } else {
                $this->authorize('create', vanillaField::class);
                $data = vanillaField::create([
                    'display_name' => $request->name,
                    'field_name' => Str::snake($request->name),
                    'display_order' => 0,
                    'status' => "active",
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
        $this->authorize('viewAny', vanillaField::class);

        return response()->json([
            'status' => true,
            'message' => 'Load Successfully',
            'data' => vanillaField::get(),
        ]);
    }
}
