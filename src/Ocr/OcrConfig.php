<?php

declare(strict_types=1);

namespace SBAO\Ocr;

/**
 * Configuration class for OCR service.
 *
 * @package SBAO\Ocr
 */
class OcrConfig
{
    private const DEFAULT_MODEL = 'gpt-4-vision-preview';
    private const DEFAULT_QUALITY = 'high';
    private const DEFAULT_MAX_TOKENS = 300;

    private string $model;
    private string $quality;
    private int $maxTokens;

    /**
     * Create a new OCR configuration instance.
     *
     * @param string $model The ChatGPT model to use
     * @param string $quality The image quality setting
     * @param int $maxTokens Maximum tokens for the response
     */
    public function __construct(
        string $model = self::DEFAULT_MODEL,
        string $quality = self::DEFAULT_QUALITY,
        int $maxTokens = self::DEFAULT_MAX_TOKENS
    ) {
        $this->model = $model;
        $this->quality = $quality;
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
     * Get the quality setting.
     *
     * @return string
     */
    public function getQuality(): string
    {
        return $this->quality;
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

