<?php

namespace App\Http\Controllers\API\SuperAdminController\Masters;

use App\Http\Controllers\Controller;
use App\Models\Documents;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DocumentController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Documents::class);

        return response()->json([
            'status' => true,
            'message' => 'Load Successfully',
            'data' => Documents::with('incomeType')->latest()->get(),
        ]);
    }

    public function saveDocument(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'income_type_id' => 'required',
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

                $data = Documents::find($request->id);

                if (! $data) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Not found',
                        'data' => [],
                    ], 404);
                }

                $this->authorize('update', $data);
                $exists = Documents::where('name', $request->name)
                    ->where('income_type_id', $request->income_type_id)
                    ->exists();

                if ($exists) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Documents already exists.',
                        'data' => [],
                    ], 422);
                }

                $data->update([
                    'name' => $request->name,
                    'income_type_id' => $request->income_type_id,
                ]);
            } else {

                $this->authorize('create', Documents::class);
                $exists = Documents::where('name', $request->name)
                    ->where('income_type_id', $request->income_type_id)
                    ->exists();

                if ($exists) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Documents already exists.',
                        'data' => [],
                    ], 422);
                }
                $data = Documents::create([
                    'name' => $request->name,
                    'income_type_id' => $request->income_type_id,

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
