<?php

declare(strict_types=1);

namespace SBAO\ChatBot;

/**
 * Configuration class for Chat Bot service.
 *
 * @package SBAO\ChatBot
 */
class ChatBotConfig
{
    private const DEFAULT_MODEL = 'gpt-4';
    private const DEFAULT_TEMPERATURE = 0.7;
    private const DEFAULT_MAX_TOKENS = 1000;
    private const DEFAULT_MAX_HISTORY = 20;

    private string $model;
    private float $temperature;
    private int $maxTokens;
    private ?string $systemPrompt;
    private int $maxHistory;

    /**
     * Create a new Chat Bot configuration instance.
     *
     * @param string $model The ChatGPT model to use
     * @param float $temperature The temperature parameter (0.0-2.0)
     * @param int $maxTokens Maximum tokens for the response
     * @param string|null $systemPrompt Optional system prompt
     * @param int $maxHistory Maximum number of messages to keep in history
     */
    public function __construct(
        string $model = self::DEFAULT_MODEL,
        float $temperature = self::DEFAULT_TEMPERATURE,
        int $maxTokens = self::DEFAULT_MAX_TOKENS,
        ?string $systemPrompt = null,
        int $maxHistory = self::DEFAULT_MAX_HISTORY
    ) {
        $this->model = $model;
        $this->temperature = max(0.0, min(2.0, $temperature));
        $this->maxTokens = $maxTokens;
        $this->systemPrompt = $systemPrompt;
        $this->maxHistory = $maxHistory;
    }

    /**
     * Get the model name.
     *
     * @return string
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * Get the temperature parameter.
     *
     * @return float
     */
    public function getTemperature(): float
    {
        return $this->temperature;
    }

    /**
     * Get the maximum tokens.
     *
     * @return int
     */
    public function getMaxTokens(): int
    {
        return $this->maxTokens;
    }

    /**
     * Get the system prompt.
     *
     * @return string|null
     */
    public function getSystemPrompt(): ?string
    {
        return $this->systemPrompt;
    }

    /**
     * Get the maximum history length.
     *
     * @return int
     */
    public function getMaxHistory(): int
    {
        return $this->maxHistory;
    }

    /**
     * Create a default configuration instance.
     *
     * @return self
     */
    public static function default(): self
    {
        return new self();
    }
}

