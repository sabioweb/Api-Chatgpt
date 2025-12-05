<?php

declare(strict_types=1);

namespace SBAO\Mathematics;

use SBAO\Client\ApiClient;
use SBAO\Exception\ApiException;
use SBAO\Exception\InvalidInputException;

/**
 * Service for solving mathematical problems using ChatGPT API.
 *
 * @package SBAO\Mathematics
 */
class MathematicsService
{
    private ApiClient $apiClient;
    private MathematicsConfig $config;

    /**
     * Create a new Mathematics service instance.
     *
     * @param ApiClient $apiClient The API client instance
     * @param MathematicsConfig $config The Mathematics configuration
     */
    public function __construct(ApiClient $apiClient, MathematicsConfig $config)
    {
        $this->apiClient = $apiClient;
        $this->config = $config;
    }

    /**
     * Solve a mathematical problem.
     *
     * @param string $problem The mathematical problem or equation
     * @return string The solution with step-by-step explanation
     * @throws InvalidInputException
     * @throws ApiException
     */
    public function solve(string $problem): string
    {
        $this->validateInput($problem);

        $response = $this->apiClient->post('chat/completions', [
            'model' => $this->config->getModel(),
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a mathematics expert. Solve mathematical problems step by step and provide clear explanations.',
                ],
                [
                    'role' => 'user',
                    'content' => $problem,
                ],
            ],
            'temperature' => $this->config->getTemperature(),
            'max_tokens' => $this->config->getMaxTokens(),
        ]);

        return $this->parseResponse($response);
    }

    /**
     * Validate the input problem.
     *
     * @param string $problem
     * @return void
     * @throws InvalidInputException
     */
    private function validateInput(string $problem): void
    {
        if (empty(trim($problem))) {
            throw new InvalidInputException('Mathematical problem cannot be empty');
        }
    }

    /**
     * Parse the API response to extract the solution.
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

