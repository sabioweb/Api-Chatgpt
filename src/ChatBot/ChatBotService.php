<?php

declare(strict_types=1);

namespace SBAO\ChatBot;

use SBAO\Client\ApiClient;
use SBAO\Exception\ApiException;
use SBAO\Exception\InvalidInputException;

/**
 * Service for conversational AI interactions using ChatGPT API.
 *
 * @package SBAO\ChatBot
 */
class ChatBotService
{
    private ApiClient $apiClient;
    private ChatBotConfig $config;
    private array $conversationHistory = [];

    /**
     * Create a new Chat Bot service instance.
     *
     * @param ApiClient $apiClient The API client instance
     * @param ChatBotConfig $config The Chat Bot configuration
     */
    public function __construct(ApiClient $apiClient, ChatBotConfig $config)
    {
        $this->apiClient = $apiClient;
        $this->config = $config;
    }

    /**
     * Send a single message and get a response.
     *
     * @param string $message The user message
     * @param array|null $history Optional conversation history (overrides internal history)
     * @return string The AI response
     * @throws InvalidInputException
     * @throws ApiException
     */
    public function chat(string $message, ?array $history = null): string
    {
        $this->validateMessage($message);

        $messages = $this->buildMessages($message, $history);
        $response = $this->apiClient->post('chat/completions', [
            'model' => $this->config->getModel(),
            'messages' => $messages,
            'temperature' => $this->config->getTemperature(),
            'max_tokens' => $this->config->getMaxTokens(),
        ]);

        $aiResponse = $this->parseResponse($response);

        // Update conversation history
        $this->addToHistory('user', $message);
        $this->addToHistory('assistant', $aiResponse);

        return $aiResponse;
    }

    /**
     * Get the current conversation history.
     *
     * @return array
     */
    public function getHistory(): array
    {
        return $this->conversationHistory;
    }

    /**
     * Clear the conversation history.
     *
     * @return void
     */
    public function clearHistory(): void
    {
        $this->conversationHistory = [];
    }

    /**
     * Set the conversation history.
     *
     * @param array $history The conversation history
     * @return void
     * @throws InvalidInputException
     */
    public function setHistory(array $history): void
    {
        $this->validateHistory($history);
        $this->conversationHistory = $history;
    }

    /**
     * Build messages array for API request.
     *
     * @param string $message
     * @param array|null $history
     * @return array
     */
    private function buildMessages(string $message, ?array $history): array
    {
        $messages = [];

        // Add system prompt if configured
        if ($this->config->getSystemPrompt() !== null) {
            $messages[] = [
                'role' => 'system',
                'content' => $this->config->getSystemPrompt(),
            ];
        }

        // Add conversation history
        $historyToUse = $history ?? $this->conversationHistory;
        $historyToUse = $this->limitHistory($historyToUse);

        foreach ($historyToUse as $item) {
            if (isset($item['role']) && isset($item['content'])) {
                $messages[] = [
                    'role' => $item['role'],
                    'content' => $item['content'],
                ];
            }
        }

        // Add current message
        $messages[] = [
            'role' => 'user',
            'content' => $message,
        ];

        return $messages;
    }

    /**
     * Limit conversation history to maximum length.
     *
     * @param array $history
     * @return array
     */
    private function limitHistory(array $history): array
    {
        $maxHistory = $this->config->getMaxHistory();
        if (count($history) <= $maxHistory) {
            return $history;
        }

        // Keep the most recent messages
        return array_slice($history, -$maxHistory);
    }

    /**
     * Add a message to conversation history.
     *
     * @param string $role
     * @param string $content
     * @return void
     */
    private function addToHistory(string $role, string $content): void
    {
        $this->conversationHistory[] = [
            'role' => $role,
            'content' => $content,
        ];

        // Limit history length
        $this->conversationHistory = $this->limitHistory($this->conversationHistory);
    }

    /**
     * Validate a message.
     *
     * @param string $message
     * @return void
     * @throws InvalidInputException
     */
    private function validateMessage(string $message): void
    {
        if (empty(trim($message))) {
            throw new InvalidInputException('Message cannot be empty');
        }

        // Check message length (rough estimate: 4 characters per token, max tokens ~100k)
        $maxLength = 400000; // Very generous limit
        if (strlen($message) > $maxLength) {
            throw new InvalidInputException(
                "Message length ({$maxLength} characters) exceeds maximum allowed length"
            );
        }
    }

    /**
     * Validate conversation history format.
     *
     * @param array $history
     * @return void
     * @throws InvalidInputException
     */
    private function validateHistory(array $history): void
    {
        foreach ($history as $item) {
            if (!is_array($item)) {
                throw new InvalidInputException('Conversation history items must be arrays');
            }

            if (!isset($item['role']) || !isset($item['content'])) {
                throw new InvalidInputException(
                    'Conversation history items must have "role" and "content" keys'
                );
            }

            if (!in_array($item['role'], ['user', 'assistant', 'system'], true)) {
                throw new InvalidInputException(
                    'Invalid role in conversation history: ' . $item['role']
                );
            }

            if (!is_string($item['content'])) {
                throw new InvalidInputException('Conversation history content must be a string');
            }
        }
    }

    /**
     * Parse the API response to extract the chat message.
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

