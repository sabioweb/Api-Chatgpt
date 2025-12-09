<?php

declare(strict_types=1);

namespace SBAO\Factory;

use SBAO\ChatBot\ChatBotConfig;
use SBAO\ChatBot\ChatBotService;
use SBAO\Client\ApiClient;
use SBAO\Mathematics\MathematicsConfig;
use SBAO\Mathematics\MathematicsService;
use SBAO\Ocr\OcrConfig;
use SBAO\Ocr\OcrService;
use SBAO\Programming\ProgrammingConfig;
use SBAO\Programming\ProgrammingService;
use SBAO\Speech\SpeechConfig;
use SBAO\Speech\SpeechToTextService;
use SBAO\Speech\TextToSpeechService;

/**
 * Factory for creating service instances with different configurations.
 *
 * @package SBAO\Factory
 */
class ServiceFactory
{
    private string $apiKey;

    /**
     * Create a new service factory instance.
     *
     * @param string $apiKey The ChatGPT API key
     */
    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Create an OCR service instance.
     *
     * @param OcrConfig|null $config Optional OCR configuration
     * @return OcrService
     */
    public function createOcrService(?OcrConfig $config = null): OcrService
    {
        $apiClient = new ApiClient($this->apiKey);
        $config = $config ?? OcrConfig::default();

        return new OcrService($apiClient, $config);
    }

    /**
     * Create a Mathematics service instance.
     *
     * @param MathematicsConfig|null $config Optional Mathematics configuration
     * @return MathematicsService
     */
    public function createMathematicsService(?MathematicsConfig $config = null): MathematicsService
    {
        $apiClient = new ApiClient($this->apiKey);
        $config = $config ?? MathematicsConfig::default();

        return new MathematicsService($apiClient, $config);
    }

    /**
     * Create a Programming service instance.
     *
     * @param ProgrammingConfig|null $config Optional Programming configuration
     * @return ProgrammingService
     */
    public function createProgrammingService(?ProgrammingConfig $config = null): ProgrammingService
    {
        $apiClient = new ApiClient($this->apiKey);
        $config = $config ?? ProgrammingConfig::default();

        return new ProgrammingService($apiClient, $config);
    }

    /**
     * Create a Chat Bot service instance.
     *
     * @param ChatBotConfig|null $config Optional Chat Bot configuration
     * @return ChatBotService
     */
    public function createChatBotService(?ChatBotConfig $config = null): ChatBotService
    {
        $apiClient = new ApiClient($this->apiKey);
        $config = $config ?? ChatBotConfig::default();

        return new ChatBotService($apiClient, $config);
    }

    /**
     * Create a Speech-to-Text service instance.
     *
     * @param SpeechConfig|null $config Optional Speech configuration
     * @return SpeechToTextService
     */
    public function createSpeechToTextService(?SpeechConfig $config = null): SpeechToTextService
    {
        $apiClient = new ApiClient($this->apiKey);
        $config = $config ?? SpeechConfig::default();

        return new SpeechToTextService($apiClient, $config);
    }

    /**
     * Create a Text-to-Speech service instance.
     *
     * @param SpeechConfig|null $config Optional Speech configuration
     * @return TextToSpeechService
     */
    public function createTextToSpeechService(?SpeechConfig $config = null): TextToSpeechService
    {
        $apiClient = new ApiClient($this->apiKey);
        $config = $config ?? SpeechConfig::default();

        return new TextToSpeechService($apiClient, $config);
    }
}

