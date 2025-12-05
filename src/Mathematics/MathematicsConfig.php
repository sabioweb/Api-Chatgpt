<?php

declare(strict_types=1);

namespace SBAO\Mathematics;

/**
 * Configuration class for Mathematics service.
 *
 * @package SBAO\Mathematics
 */
class MathematicsConfig
{
    private const DEFAULT_MODEL = 'gpt-4';
    private const DEFAULT_TEMPERATURE = 0.3;
    private const DEFAULT_MAX_TOKENS = 1000;

    private string $model;
    private float $temperature;
    private int $maxTokens;

    /**
     * Create a new Mathematics configuration instance.
     *
     * @param string $model The ChatGPT model to use
     * @param float $temperature The temperature parameter (0.0-2.0)
     * @param int $maxTokens Maximum tokens for the response
     */
    public function __construct(
        string $model = self::DEFAULT_MODEL,
        float $temperature = self::DEFAULT_TEMPERATURE,
        int $maxTokens = self::DEFAULT_MAX_TOKENS
    ) {
        $this->model = $model;
        $this->temperature = max(0.0, min(2.0, $temperature));
        $this->maxTokens = $maxTokens;
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
     * Create a default configuration instance.
     *
     * @return self
     */
    public static function default(): self
    {
        return new self();
    }
}

