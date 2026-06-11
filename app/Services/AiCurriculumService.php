<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiCurriculumService
{
    protected string $apiKey;
    protected string $model;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        $this->model = config('services.gemini.model', 'gemini-1.5-flash');
        $this->baseUrl = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent";
    }

    /**
     * Generate a curriculum based on a given topic using Gemini AI.
     *
     * @param string $topic
     * @return array|null
     */
    public function generateCurriculum(string $topic): ?array
    {
        if (empty($this->apiKey)) {
            Log::error('Gemini API Key is missing.');
            throw new \Exception('Gemini API Key is missing in server configuration.');
        }

        $prompt = "Buatkan saya kurikulum belajar atau roadmap terstruktur tentang: '{$topic}'. "
                . "Bagi ke dalam beberapa tahapan materi (milestones) yang berurutan dari dasar hingga mahir. "
                . "Berikan juga deskripsi singkat untuk kurikulum tersebut.";

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'responseMimeType' => 'application/json',
                'responseSchema' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'topic' => [
                            'type' => 'STRING',
                            'description' => 'Nama spesifik kurikulum/topik yang direkomendasikan'
                        ],
                        'description' => [
                            'type' => 'STRING',
                            'description' => 'Deskripsi singkat mengenai apa yang akan dipelajari (sekitar 2-3 kalimat)'
                        ],
                        'milestones' => [
                            'type' => 'ARRAY',
                            'description' => 'Daftar materi/tahapan berurutan',
                            'items' => [
                                'type' => 'OBJECT',
                                'properties' => [
                                    'title' => [
                                        'type' => 'STRING',
                                        'description' => 'Judul materi atau tahapan'
                                    ]
                                ],
                                'required' => ['title']
                            ]
                        ]
                    ],
                    'required' => ['topic', 'description', 'milestones']
                ]
            ]
        ];

        $generatedText = $this->sendRequestWithFallback($payload);
        if ($generatedText) {
            $cleanJson = $this->cleanJsonString($generatedText);
            return json_decode($cleanJson, true);
        }

        return null;
    }

    /**
     * Send request to Gemini API with model fallback and automatic retries.
     *
     * @param array $payload
     * @return string|null
     */
    protected function sendRequestWithFallback(array $payload): ?string
    {
        $models = [
            $this->model,
            'gemini-1.5-flash',
            'gemini-1.5-flash-latest'
        ];
        $models = array_values(array_unique($models));

        foreach ($models as $model) {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . $this->apiKey;
            
            for ($attempt = 1; $attempt <= 2; $attempt++) {
                try {
                    $request = Http::withHeaders([
                        'Content-Type' => 'application/json'
                    ])->timeout(30);

                    if (config('app.env') === 'local') {
                        $request->withoutVerifying();
                    }

                    $response = $request->post($url, $payload);

                    if ($response->successful()) {
                        $data = $response->json();
                        $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                        if ($text) {
                            return $text;
                        }
                    }

                    Log::warning("Gemini API (Curriculum) - Model {$model} attempt {$attempt} failed: Status {$response->status()} - {$response->body()}");
                    
                    if ($attempt < 2) {
                        usleep(300000);
                    }
                } catch (\Exception $e) {
                    Log::warning("Gemini API (Curriculum) - Model {$model} attempt {$attempt} exception: " . $e->getMessage());
                    if ($attempt < 2) {
                        usleep(300000);
                    }
                }
            }
        }

        Log::error('Gemini API (Curriculum) - All models and attempts failed.');
        return null;
    }

    /**
     * Clean potential markdown JSON formatting backticks.
     *
     * @param string $jsonStr
     * @return string
     */
    protected function cleanJsonString(string $jsonStr): string
    {
        $jsonStr = trim($jsonStr);
        if (preg_match('/^```(?:json)?\s*([\s\S]*?)\s*```$/i', $jsonStr, $matches)) {
            return trim($matches[1]);
        }
        return $jsonStr;
    }
}
