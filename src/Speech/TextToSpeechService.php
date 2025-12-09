<?php

declare(strict_types=1);

namespace SBAO\Speech;

use SBAO\Client\ApiClient;
use SBAO\Exception\ApiException;
use SBAO\Exception\InvalidAudioException;
use SBAO\Exception\InvalidInputException;

/**
 * Service for converting text to audio files using ChatGPT TTS API.
 *
 * @package SBAO\Speech
 */
class TextToSpeechService
{
    private ApiClient $apiClient;
    private AudioValidator $validator;
    private SpeechConfig $config;

    /**
     * Create a new Text-to-Speech service instance.
     *
     * @param ApiClient $apiClient The API client instance
     * @param SpeechConfig $config The Speech configuration
     * @param AudioValidator|null $validator Optional validator instance
     */
    public function __construct(
        ApiClient $apiClient,
        SpeechConfig $config,
        ?AudioValidator $validator = null
    ) {
        $this->apiClient = $apiClient;
        $this->config = $config;
        $this->validator = $validator ?? new AudioValidator();
    }

    /**
     * Convert text to audio and return as binary data.
     *
     * @param string $text The text content to convert
     * @param string|null $voice Optional voice override
     * @param string|null $format Optional output format override
     * @param float|null $speed Optional speed override
     * @return string The binary audio data
     * @throws InvalidInputException
     * @throws InvalidAudioException
     * @throws ApiException
     */
    public function synthesize(
        string $text,
        ?string $voice = null,
        ?string $format = null,
        ?float $speed = null
    ): string {
        $this->validateInput($text);

        $voice = $voice ?? $this->config->getTtsVoice();
        $format = $format ?? $this->config->getTtsFormat();
        $speed = $speed ?? $this->config->getTtsSpeed();

        $this->validator->validateVoice($voice);
        $this->validator->validateOutputFormat($format);

        $requestData = [
            'model' => $this->config->getTtsModel(),
            'input' => $text,
            'voice' => $voice,
            'response_format' => $format,
            'speed' => $speed,
        ];

        return $this->apiClient->postBinary('audio/speech', $requestData);
    }

    /**
     * Convert text to audio and save to file.
     *
     * @param string $text The text content to convert
     * @param string $outputPath The path where to save the audio file
     * @param string|null $voice Optional voice override
     * @param string|null $format Optional output format override
     * @param float|null $speed Optional speed override
     * @return void
     * @throws InvalidInputException
     * @throws InvalidAudioException
     * @throws ApiException
     */
    public function synthesizeToFile(
        string $text,
        string $outputPath,
        ?string $voice = null,
        ?string $format = null,
        ?float $speed = null
    ): void {
        $audioData = $this->synthesize($text, $voice, $format, $speed);

        $result = file_put_contents($outputPath, $audioData);
        if ($result === false) {
            throw new ApiException("Failed to save audio file to: {$outputPath}");
        }
    }

    /**
     * Validate text input.
     *
     * @param string $text
     * @return void
     * @throws InvalidInputException
     */
    private function validateInput(string $text): void
    {
        if (empty(trim($text))) {
            throw new InvalidInputException('Text input cannot be empty');
        }

        // OpenAI TTS API has a maximum input length
        $maxLength = 4096; // characters
        if (mb_strlen($text) > $maxLength) {
            throw new InvalidInputException(
                "Text input exceeds maximum length of {$maxLength} characters"
            );
        }
    }
}

