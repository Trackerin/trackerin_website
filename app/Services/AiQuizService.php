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
        $this->model = config('services.gemini.model', 'gemini-2.5-flash');
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
                . "Setiap pertanyaan harus memiliki 4 pilihan jawaban yang salah satunya benar.";

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

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '?key=' . $this->apiKey, $payload);

            if ($response->successful()) {
                $data = $response->json();
                $generatedText = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

                if ($generatedText) {
                    return json_decode($generatedText, true);
                }
            }

            Log::error('Gemini API Error (Quiz): ' . $response->body());
            return null;

        } catch (\Exception $e) {
            Log::error('AiQuizService Exception: ' . $e->getMessage());
            return null;
        }
    }
}
