<?php

declare(strict_types=1);

namespace SBAO\Programming;

/**
 * Configuration class for Programming service.
 *
 * @package SBAO\Programming
 */
class ProgrammingConfig
{
    private const DEFAULT_MODEL = 'gpt-4';
    private const DEFAULT_TEMPERATURE = 0.2;
    private const DEFAULT_MAX_TOKENS = 2000;

    private string $model;
    private float $temperature;
    private int $maxTokens;
    private ?string $defaultLanguage;
    private ?string $codeStyle;

    /**
     * Create a new Programming configuration instance.
     *
     * @param string $model The ChatGPT model to use
     * @param float $temperature The temperature parameter (0.0-2.0)
     * @param int $maxTokens Maximum tokens for the response
     * @param string|null $defaultLanguage Default programming language
     * @param string|null $codeStyle Code style preferences
     */
    public function __construct(
        string $model = self::DEFAULT_MODEL,
        float $temperature = self::DEFAULT_TEMPERATURE,
        int $maxTokens = self::DEFAULT_MAX_TOKENS,
        ?string $defaultLanguage = null,
        ?string $codeStyle = null
    ) {
        $this->model = $model;
        $this->temperature = max(0.0, min(2.0, $temperature));
        $this->maxTokens = $maxTokens;
        $this->defaultLanguage = $defaultLanguage;
        $this->codeStyle = $codeStyle;
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
     * Get the default programming language.
     *
     * @return string|null
     */
    public function getDefaultLanguage(): ?string
    {
        return $this->defaultLanguage;
    }

    /**
     * Get the code style preferences.
     *
     * @return string|null
     */
    public function getCodeStyle(): ?string
    {
        return $this->codeStyle;
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

