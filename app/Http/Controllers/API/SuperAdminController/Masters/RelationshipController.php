<?php

namespace App\Http\Controllers\API\SuperAdminController\Masters;

use App\Http\Controllers\Controller;
use App\Models\Relationships;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RelationshipController extends Controller
{
    public function saveRelationship(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'active' => 'required',


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

                $data = Relationships::find($request->id);

                if (!$data) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Not found',
                        'data' => [],
                    ], 404);
                }
                $data->update([
                    'name' => $request->name,
                    'active' => $request->active,

                ]);
            } else {

                $data = Relationships::create([
                    'name' => $request->name,
                    'active' => $request->active,

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
    
        return response()->json([
            'status' => true,
            'message' => 'Load Successfully',
            'data' => Relationships::latest()->get(),
        ]);
    }
}
