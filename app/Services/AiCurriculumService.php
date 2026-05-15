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
        $this->model = config('services.gemini.model', 'gemini-2.5-flash');
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

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '?key=' . $this->apiKey, $payload);

            if ($response->successful()) {
                $data = $response->json();
                
                // Extract the generated text from Gemini response
                $generatedText = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

                if ($generatedText) {
                    return json_decode($generatedText, true);
                }
            }

            Log::error('Gemini API Error: ' . $response->body());
            return null;

        } catch (\Exception $e) {
            Log::error('AiCurriculumService Exception: ' . $e->getMessage());
            return null;
        }
    }
}
