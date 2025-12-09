<?php

declare(strict_types=1);

namespace SBAO\Speech;

use SBAO\Client\ApiClient;
use SBAO\Exception\ApiException;
use SBAO\Exception\InvalidAudioException;
use SBAO\Exception\InvalidInputException;

/**
 * Service for converting audio files to text using ChatGPT Whisper API.
 *
 * @package SBAO\Speech
 */
class SpeechToTextService
{
    private ApiClient $apiClient;
    private AudioValidator $validator;
    private SpeechConfig $config;

    /**
     * Create a new Speech-to-Text service instance.
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
     * Transcribe audio from a file.
     *
     * @param string $filePath The path to the audio file
     * @return string The transcribed text content
     * @throws InvalidAudioException
     * @throws ApiException
     */
    public function transcribeFromFile(string $filePath): string
    {
        $this->validator->validateFile($filePath);

        $multipart = [
            [
                'name' => 'file',
                'contents' => fopen($filePath, 'r'),
                'filename' => basename($filePath),
            ],
            [
                'name' => 'model',
                'contents' => $this->config->getSttModel(),
            ],
            [
                'name' => 'response_format',
                'contents' => $this->config->getSttResponseFormat(),
            ],
            [
                'name' => 'temperature',
                'contents' => (string)$this->config->getSttTemperature(),
            ],
        ];

        if ($this->config->getSttLanguage() !== null) {
            $multipart[] = [
                'name' => 'language',
                'contents' => $this->config->getSttLanguage(),
            ];
        }

        $response = $this->apiClient->postMultipart('audio/transcriptions', $multipart);

        return $this->parseResponse($response);
    }

    /**
     * Transcribe audio from a base64-encoded string.
     *
     * @param string $base64String The base64-encoded audio string
     * @param string $filename The filename for the audio (required for API)
     * @return string The transcribed text content
     * @throws InvalidAudioException
     * @throws ApiException
     */
    public function transcribeFromBase64(string $base64String, string $filename = 'audio.mp3'): string
    {
        $this->validator->validateBase64($base64String);

        // Remove data URI prefix if present
        $base64String = preg_replace('/^data:audio\/[^;]+;base64,/', '', $base64String);

        // Decode base64
        $audioData = base64_decode($base64String, true);
        if ($audioData === false) {
            throw new InvalidAudioException('Failed to decode base64 string');
        }

        // Create temporary file
        $tempFile = tmpfile();
        if ($tempFile === false) {
            throw new InvalidAudioException('Failed to create temporary file');
        }

        $tempPath = stream_get_meta_data($tempFile)['uri'];
        file_put_contents($tempPath, $audioData);

        try {
            return $this->transcribeFromFile($tempPath);
        } finally {
            fclose($tempFile);
            if (file_exists($tempPath)) {
                @unlink($tempPath);
            }
        }
    }

    /**
     * Convert file path to base64 string.
     *
     * @param string $filePath The path to the audio file
     * @return string The base64-encoded audio string
     * @throws InvalidAudioException
     */
    private function fileToBase64(string $filePath): string
    {
        $audioData = file_get_contents($filePath);
        if ($audioData === false) {
            throw new InvalidAudioException("Failed to read audio file: {$filePath}");
        }

        return base64_encode($audioData);
    }

    /**
     * Parse the API response to extract transcribed text.
     *
     * @param array $response The API response
     * @return string The transcribed text
     * @throws ApiException
     */
    private function parseResponse(array $response): string
    {
        if (!isset($response['text'])) {
            throw new ApiException('Invalid API response: missing text field');
        }

        return $response['text'];
    }
}

