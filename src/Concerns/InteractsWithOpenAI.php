<?php

namespace Lyre\Content\Concerns;

use Illuminate\Support\Facades\Log;

trait InteractsWithOpenAI
{
    /**
     * Check if model supports JSON mode
     */
    protected function supportsJsonMode(string $model): bool
    {
        $jsonModeModels = [
            'gpt-4-turbo-preview',
            'gpt-4-turbo',
            'gpt-4o',
            'gpt-4o-mini',
            'gpt-3.5-turbo-1106',
            'gpt-3.5-turbo-0125',
        ];

        foreach ($jsonModeModels as $jsonModel) {
            if (str_contains($model, $jsonModel)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Extract JSON from AI response (handles markdown code blocks)
     */
    protected function extractJsonFromResponse(string $content): ?array
    {
        // Try direct JSON decode first
        $decoded = json_decode($content, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }

        // Try to extract JSON from markdown code block
        if (preg_match('/```json\s*(\{.*?\})\s*```/s', $content, $matches)) {
            $decoded = json_decode($matches[1], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }

        // Try to extract any JSON object
        if (preg_match('/(\{.*?\})/s', $content, $matches)) {
            $decoded = json_decode($matches[1], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }

        return null;
    }

    /**
     * Call OpenAI Chat API with automatic JSON mode support
     * 
     * TODO: Kigathi - January 28 2026 - Add credit tracking here to ensure we follow billing
     */
    protected function callOpenAI(array $messages, array $options = []): array
    {
        $model = $options['model'] ?? config('services.openai.default_model', 'gpt-4');
        $temperature = $options['temperature'] ?? 0.7;

        $requestData = [
            'model' => $model,
            'messages' => $messages,
            'temperature' => $temperature,
        ];

        // Only add response_format for models that support it
        if ($this->supportsJsonMode($model)) {
            $requestData['response_format'] = ['type' => 'json_object'];
        }

        Log::info('🤖 Calling OpenAI API', [
            'model' => $model,
            'message_count' => count($messages),
            'supports_json_mode' => $this->supportsJsonMode($model),
        ]);

        $response = openai()->chat()->create($requestData);

        Log::info('✅ OpenAI API response received', [
            'model' => $model,
            'tokens_used' => $response['usage'] ?? null,
        ]);

        return $response;
    }

    /**
     * Call OpenAI and extract JSON response
     */
    protected function callOpenAIForJson(array $messages, array $options = []): ?array
    {
        $response = $this->callOpenAI($messages, $options);
        $responseContent = $response['choices'][0]['message']['content'];

        $aiResponse = $this->extractJsonFromResponse($responseContent);

        if (!$aiResponse) {
            Log::error('❌ Failed to parse AI response', [
                'response_preview' => substr($responseContent, 0, 200),
            ]);
        }

        return $aiResponse;
    }
}
