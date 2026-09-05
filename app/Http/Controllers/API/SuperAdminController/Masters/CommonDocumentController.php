<?php

namespace App\Http\Controllers\API\SuperAdminController\Masters;

use App\Http\Controllers\Controller;
use App\Models\CommonDocument;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CommonDocumentController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', CommonDocument::class);

        return response()->json([
            'status' => true,
            'message' => 'Load Successfully',
            'data' => CommonDocument::latest()->get(),
        ]);
    }

    public function saveCommonDocument(Request $request)
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

                if ($request->id < 5) {
                    return response()->json([
                        'status' => false,
                        'message' => 'This document can not update',
                        'data' => [],
                    ], 422);
                }

                $data = CommonDocument::find($request->id);

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
                    'variable_name' => Str::slug($request->name, '_'),

                ]);
            } else {
                $this->authorize('create', CommonDocument::class);
                $data = CommonDocument::create([
                    'name' => $request->name,
                    'variable_name' => Str::slug($request->name, '_'),

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
