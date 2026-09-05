<?php

namespace App\Services\AIService;

use App\Models\LoanCibilDataMst;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class geminiService
{
    public function extractData($id)
    {

        $data = LoanCibilDataMst::where("id", $id)->first();
        $data->raw_data;
        $prompt = file_get_contents(
            app_path('Services/AIService/prompt.txt')
        );
        $prompt = str_replace(
            '{{CIBIL_REPORT_TEXT}}',
            $data->raw_data,
            $prompt
        );
        $apiKey = config('services.gemini.api_key');

        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent";
        try {

            $response = Http::timeout(300)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'X-goog-api-key' => $apiKey,
                ])
                ->post($url, [
                    "contents" => [
                        [
                            "parts" => [
                                [
                                    "text" => $prompt
                                ]
                            ]
                        ]
                    ]
                ]);

            if (!$response->successful()) {
                Log::info($response->body());
                return [
                    "status" => false,
                    "message" => $response->body()
                ];
            }

            $json = $response->json();

            $text = $json['candidates'][0]['content']['parts'][0]['text'] ?? '';
            Log::info($json);

            $data = LoanCibilDataMst::findOrFail($id);


            $folder = public_path('cibil_json');

            if (!File::exists($folder)) {
                File::makeDirectory($folder, 0755, true);
            }
            $fileName = 'cibil_' . $data->id . '_' . time() . '.json';
            $filePath = $folder . '/' . $fileName;
            File::put($filePath, $text);
            $data->json_file = 'cibil_json/' . $fileName;
            $data->status = "processing";
            $data->processing_started_at = now();
            $data->save();

            return [
                "status" => true,
            ];
        } catch (\Throwable $e) {

            return [
                "status" => false,
                Log::info($e->getMessage()),
                "message" => $e->getMessage()
            ];
        }
    }
}
