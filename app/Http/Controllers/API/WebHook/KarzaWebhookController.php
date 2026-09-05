<?php

namespace App\Http\Controllers\API\WebHook;

use App\Http\Controllers\Controller;
use App\Models\customerGstDetailFilling;
use App\Models\customerGstDetails;
use App\Models\customerGSTR1Details;
use App\Models\customerGSTR3bDetails;
use App\Models\customerGSTR3bITCDetails;
use App\Models\customerGSTR3bSupplyDetails;
use App\Models\webhook;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KarzaWebhookController extends Controller
{

    public function receiveWebhook(Request $request, $token = null)
    {

        $payload = $request->all();

        Log::info('Karza Webhook Hit', [
            'time' => now()->toDateTimeString(),
            'ip' => $request->ip(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'headers' => $request->headers->all(),
            'body' => $request->getContent(),
        ]);



        $secret = "79fb0fed215b6e8031b263285aa44999b2ec27e411db88ef41d422082999f262";

        if (empty($secret)) {
            Log::error('Karza Webhook Error', [
                'message' => 'Webhook secret not configured.',

            ]);

            return response()->json([
                'status' => false,
                'message' => 'Webhook secret not configured.',
                'data' => []
            ], 500);
        }
        $incomingToken = $token ?? $request->query('token');

        if (!hash_equals($secret, (string) $incomingToken)) {
            Log::error('Karza Webhook Error', [
                'message' => 'Unauthorized',

            ]);
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
                'data' => []
            ], 401);
        }


        try {

            $json = $request->getContent();


            if (empty($json)) {
                Log::error('Karza Webhook Error', [
                    'message' => 'Empty webhook payload',

                ]);

                return response()->json([
                    'status'  => false,
                    'message' => 'Empty webhook payload',
                    'data'    => []
                ], 400);
            }

            $payload = json_decode($json, true);

            if (!is_array($payload)) {
                Log::error('Karza Webhook Error', [
                    'message' => 'Invalid JSON',

                ]);

                return response()->json([
                    'status'  => false,
                    'message' => 'Invalid JSON',
                    'data'    => []
                ], 400);
            }


            $folder = base_path('webhook_files');

            if (!file_exists($folder)) {
                mkdir($folder, 0755, true);
            }
            // $path = base_path('webhook_files/testing.json');

            // $testData = json_decode(file_get_contents($path), true);

            // $fileName = uniqid('karza_') . '.json';

            // file_put_contents(
            //     $folder . '/' . $fileName,
            //     json_encode($testData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            // );

            $payload = json_decode($request->getContent(), true);



            $fileName = uniqid('karza_') . '.json';

            file_put_contents(
                $folder . '/' . $fileName,
                json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            );



            webhook::create([
                'provider'   => 'KARZA',
                "loan_gst_details_id" => 1,
                'request_id' => $payload['requestId'] ?? null,
                'event'      => $payload['event'] ?? null,
                'method'     => $request->method(),
                'ip_address' => $request->ip(),
                'headers' => json_encode($request->headers->all()),
                'payload'    => $fileName,
                'http_status' => 200,

            ]);
            Log::info('Karza Webhook Error', [
                'message' => "Save successfully",

            ]);

            return response()->json([
                'status' => true,
                'message' => "Save successfully",
                'data' => [],
            ], 200);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'data' => [],
            ], 401);
        }
    }
}
