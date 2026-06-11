<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Milestone;

class AiQuizService
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
     * Generate a quiz for a milestone using Gemini AI.
     *
     * @param Milestone $milestone
     * @return array|null
     */
    public function generateQuiz(Milestone $milestone): ?array
    {
        if (empty($this->apiKey)) {
            Log::error('Gemini API Key is missing.');
            throw new \Exception('Gemini API Key is missing in server configuration.');
        }

        $curriculumTopic = $milestone->curriculum->topic ?? 'Materi Umum';
        $milestoneTitle = $milestone->title;

        $prompt = "Buatkan tepat 3 pertanyaan kuis pilihan ganda yang relevan untuk sub-materi '{$milestoneTitle}' "
                . "dalam konteks kurikulum '{$curriculumTopic}'. "
                . "Setiap pertanyaan harus memiliki 4 pilihan jawaban yang salah satunya benar. "
                . "Pilihan jawaban salah (distractor) harus dirancang secara akademis: realistis, memiliki tingkat kesulitan sedang hingga tinggi, dan tampak masuk akal (tidak terlalu gampang atau obvious), sehingga kuis ini benar-benar menguji pemahaman mendalam mengenai konsep '{$milestoneTitle}'. "
                . "Hindari pilihan jawaban yang konyol, bertentangan secara ekstrem, atau mudah dieliminasi.";

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
                        'title' => [
                            'type' => 'STRING',
                            'description' => 'Judul kuis yang spesifik'
                        ],
                        'questions' => [
                            'type' => 'ARRAY',
                            'description' => 'Daftar pertanyaan (harus persis 3)',
                            'items' => [
                                'type' => 'OBJECT',
                                'properties' => [
                                    'question' => [
                                        'type' => 'STRING'
                                    ],
                                    'options' => [
                                        'type' => 'ARRAY',
                                        'description' => 'Daftar 4 opsi jawaban',
                                        'items' => [
                                            'type' => 'STRING'
                                        ]
                                    ],
                                    'correct_answer' => [
                                        'type' => 'STRING',
                                        'description' => 'Jawaban benar, harus sama persis stringnya dengan yang ada di options'
                                    ]
                                ],
                                'required' => ['question', 'options', 'correct_answer']
                            ]
                        ]
                    ],
                    'required' => ['title', 'questions']
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

                    Log::warning("Gemini API (Quiz) - Model {$model} attempt {$attempt} failed: Status {$response->status()} - {$response->body()}");
                    
                    if ($attempt < 2) {
                        usleep(300000);
                    }
                } catch (\Exception $e) {
                    Log::warning("Gemini API (Quiz) - Model {$model} attempt {$attempt} exception: " . $e->getMessage());
                    if ($attempt < 2) {
                        usleep(300000);
                    }
                }
            }
        }

        Log::error('Gemini API (Quiz) - All models and attempts failed.');
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
