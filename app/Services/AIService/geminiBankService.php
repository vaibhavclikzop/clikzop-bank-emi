<?php

namespace App\Services\AIService;

use App\Models\loanBankingMst;
use App\Models\LoanCibilDataMst;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class geminiBankService
{
    public function extractData($id)
    {
        $data = loanBankingMst::findOrFail($id);

        $promptTemplate = File::get(
            app_path('Services/AIService/bankprompt.txt')
        );

        $rawText = File::get(
            storage_path('app/public/' . $data->raw_text_file)
        );



        // Split page-wise
        preg_match_all(
            '/================ PAGE \d+ ================.*?(?=(================ PAGE \d+ ================)|\z)/s',
            $rawText,
            $matches
        );

        $pages = $matches[0];

        $pagePerChunk = 5;

        $chunks = array_chunk($pages, $pagePerChunk);

        $finalChunks = [];

        foreach ($chunks as $chunk) {
            $finalChunks[] = implode("\n\n", $chunk);
        }




        $apiKey = config('services.gemini.api_key');

        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent";

        $responses = [];

        try {

            foreach ($finalChunks as $index => $chunk) {
                $prompt = $promptTemplate;
                $prompt = str_replace(
                    ['{{CHUNK_TYPE}}', '{{BANK_STATEMENT_TEXT}}'],
                    [
                        $index === 0 ? 'FIRST_CHUNK' : 'TRANSACTION_CHUNK',
                        $chunk
                    ],
                    $promptTemplate
                );



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

                    Log::error("Gemini Chunk Failed", [
                        "chunk" => $index + 1,
                        "body" => $response->body()
                    ]);

                    continue;
                }

                $json = $response->json();

                $text = $json['candidates'][0]['content']['parts'][0]['text'] ?? '';

                $responses[] = [
                    "chunk_no" => $index + 1,
                    "response" => json_decode($text, true)
                ];

                sleep(1);
            }

            $folder = public_path('banking_json');

            if (!File::exists($folder)) {
                File::makeDirectory($folder, 0755, true);
            }

            $fileName = 'banking_' . $data->id . '_' . time() . '.json';

            $filePath = $folder . '/' . $fileName;

            File::put(
                $filePath,
                json_encode($responses, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            );

            $data->json_file = 'banking_json/' . $fileName;
            $data->status = 'processing';
            $data->processing_started_at = now();
            $data->save();

            return [
                "status" => true
            ];
        } catch (\Throwable $e) {

            Log::error($e);

            return [
                "status" => false,
                "message" => $e->getMessage()
            ];
        }
    }
}
