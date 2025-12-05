<?php

declare(strict_types=1);

namespace SBAO\Programming;

use SBAO\Client\ApiClient;
use SBAO\Exception\ApiException;
use SBAO\Exception\InvalidInputException;

/**
 * Service for code generation, analysis, and debugging using ChatGPT API.
 *
 * @package SBAO\Programming
 */
class ProgrammingService
{
    private ApiClient $apiClient;
    private ProgrammingConfig $config;

    /**
     * Create a new Programming service instance.
     *
     * @param ApiClient $apiClient The API client instance
     * @param ProgrammingConfig $config The Programming configuration
     */
    public function __construct(ApiClient $apiClient, ProgrammingConfig $config)
    {
        $this->apiClient = $apiClient;
        $this->config = $config;
    }

    /**
     * Generate code from a description.
     *
     * @param string $description The code generation request description
     * @param string|null $language Optional programming language
     * @return string The generated code with explanation
     * @throws InvalidInputException
     * @throws ApiException
     */
    public function generateCode(string $description, ?string $language = null): string
    {
        $this->validateInput($description);
        $language = $language ?? $this->config->getDefaultLanguage();

        $systemPrompt = $this->buildSystemPrompt($language, 'generate');
        $userPrompt = $description;

        return $this->makeRequest($systemPrompt, $userPrompt);
    }

    /**
     * Analyze existing code.
     *
     * @param string $code The code snippet to analyze
     * @param string|null $language Optional programming language
     * @return string The analysis including bugs, improvements, or explanations
     * @throws InvalidInputException
     * @throws ApiException
     */
    public function analyzeCode(string $code, ?string $language = null): string
    {
        $this->validateInput($code);
        $language = $language ?? $this->detectLanguage($code);

        $systemPrompt = $this->buildSystemPrompt($language, 'analyze');
        $userPrompt = "Analyze the following code:\n\n```{$language}\n{$code}\n```";

        return $this->makeRequest($systemPrompt, $userPrompt);
    }

    /**
     * Debug code issues.
     *
     * @param string $code The code with errors
     * @param string|null $errorMessage Optional error message or description
     * @param string|null $language Optional programming language
     * @return string Debugging suggestions and fixes
     * @throws InvalidInputException
     * @throws ApiException
     */
    public function debugCode(string $code, ?string $errorMessage = null, ?string $language = null): string
    {
        $this->validateInput($code);
        $language = $language ?? $this->detectLanguage($code);

        $systemPrompt = $this->buildSystemPrompt($language, 'debug');
        $userPrompt = "Debug the following code";
        if ($errorMessage !== null) {
            $userPrompt .= " with error: {$errorMessage}";
        }
        $userPrompt .= ":\n\n```{$language}\n{$code}\n```";

        return $this->makeRequest($systemPrompt, $userPrompt);
    }

    /**
     * Make an API request.
     *
     * @param string $systemPrompt
     * @param string $userPrompt
     * @return string
     * @throws ApiException
     */
    private function makeRequest(string $systemPrompt, string $userPrompt): string
    {
        $response = $this->apiClient->post('chat/completions', [
            'model' => $this->config->getModel(),
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $systemPrompt,
                ],
                [
                    'role' => 'user',
                    'content' => $userPrompt,
                ],
            ],
            'temperature' => $this->config->getTemperature(),
            'max_tokens' => $this->config->getMaxTokens(),
        ]);

        return $this->parseResponse($response);
    }

    /**
     * Build system prompt based on language and task type.
     *
     * @param string|null $language
     * @param string $taskType
     * @return string
     */
    private function buildSystemPrompt(?string $language, string $taskType): string
    {
        $prompt = 'You are an expert programming assistant. ';

        if ($language !== null) {
            $prompt .= "Specialize in {$language} programming. ";
        }

        switch ($taskType) {
            case 'generate':
                $prompt .= 'Generate clean, well-documented code based on the user\'s requirements.';
                break;
            case 'analyze':
                $prompt .= 'Analyze code for bugs, improvements, and provide clear explanations.';
                break;
            case 'debug':
                $prompt .= 'Debug code issues and provide fixes with explanations.';
                break;
        }

        if ($this->config->getCodeStyle() !== null) {
            $prompt .= ' Follow this code style: ' . $this->config->getCodeStyle();
        }

        return $prompt;
    }

    /**
     * Detect programming language from code snippet.
     *
     * @param string $code
     * @return string|null
     */
    private function detectLanguage(string $code): ?string
    {
        // Simple heuristics for common languages
        $patterns = [
            'php' => ['<?php', '->', '::'],
            'javascript' => ['function', '=>', 'const ', 'let ', 'var '],
            'python' => ['def ', 'import ', 'print(', 'if __name__'],
            'java' => ['public class', 'public static void main'],
            'csharp' => ['using System', 'namespace ', 'public class'],
        ];

        foreach ($patterns as $lang => $indicators) {
            foreach ($indicators as $indicator) {
                if (stripos($code, $indicator) !== false) {
                    return $lang;
                }
            }
        }

        return null;
    }

    /**
     * Validate the input.
     *
     * @param string $input
     * @return void
     * @throws InvalidInputException
     */
    private function validateInput(string $input): void
    {
        if (empty(trim($input))) {
            throw new InvalidInputException('Code input cannot be empty');
        }
    }

    /**
     * Parse the API response to extract code or analysis.
     *
     * @param array $response
     * @return string
     * @throws ApiException
     */
    private function parseResponse(array $response): string
    {
        if (!isset($response['choices'][0]['message']['content'])) {
            throw new ApiException('Invalid API response: missing content');
        }

        return trim($response['choices'][0]['message']['content']);
    }
}

