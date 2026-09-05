<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\StateDistrict;
use Illuminate\Http\Request;

class CommonMasterController extends Controller
{
    public function getStateDistrict(Request $request)
    {

        if (request('district')) {
            $data = StateDistrict::select('district')->where('state', request('district'))->pluck('district');

            return response()->json([
                'status' => true,
                'message' => 'Load Successfully',
                'data' => $data,
            ], 200);
        } else {
            $data = StateDistrict::select('state')
                ->distinct()->orderBy('state')->pluck('state');

            return response()->json([
                'status' => true,
                'message' => 'Load Successfully',
                'data' => $data,
            ], 200);
        }
    }
}
