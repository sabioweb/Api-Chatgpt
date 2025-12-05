<?php

declare(strict_types=1);

namespace SBAO\Ocr;

use SBAO\Client\ApiClient;
use SBAO\Exception\ApiException;
use SBAO\Exception\InvalidImageException;

/**
 * Service for extracting text from images using ChatGPT Vision API.
 *
 * @package SBAO\Ocr
 */
class OcrService
{
    private ApiClient $apiClient;
    private ImageValidator $validator;
    private OcrConfig $config;

    /**
     * Create a new OCR service instance.
     *
     * @param ApiClient $apiClient The API client instance
     * @param OcrConfig $config The OCR configuration
     * @param ImageValidator|null $validator Optional validator instance
     */
    public function __construct(
        ApiClient $apiClient,
        OcrConfig $config,
        ?ImageValidator $validator = null
    ) {
        $this->apiClient = $apiClient;
        $this->config = $config;
        $this->validator = $validator ?? new ImageValidator();
    }

    /**
     * Extract text from an image file.
     *
     * @param string $filePath The path to the image file
     * @return string The extracted text content
     * @throws InvalidImageException
     * @throws ApiException
     */
    public function extractFromFile(string $filePath): string
    {
        $this->validator->validateFile($filePath);
        $base64Image = $this->fileToBase64($filePath);
        return $this->extractFromBase64($base64Image);
    }

    /**
     * Extract text from a base64-encoded image string.
     *
     * @param string $base64String The base64-encoded image string
     * @return string The extracted text content
     * @throws InvalidImageException
     * @throws ApiException
     */
    public function extractFromBase64(string $base64String): string
    {
        $this->validator->validateBase64($base64String);

        // Remove data URI prefix if present
        $base64String = preg_replace('/^data:image\/[^;]+;base64,/', '', $base64String);

        $response = $this->apiClient->post('chat/completions', [
            'model' => $this->config->getModel(),
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => 'Extract all text from this image. Return only the extracted text without any additional explanation.',
                        ],
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => 'data:image/jpeg;base64,' . $base64String,
                            ],
                        ],
                    ],
                ],
            ],
            'max_tokens' => $this->config->getMaxTokens(),
        ]);

        return $this->parseResponse($response);
    }

    /**
     * Convert an image file to base64 string.
     *
     * @param string $filePath
     * @return string
     */
    private function fileToBase64(string $filePath): string
    {
        $imageData = file_get_contents($filePath);
        if ($imageData === false) {
            throw new InvalidImageException("Failed to read image file: {$filePath}");
        }

        $mimeType = mime_content_type($filePath);
        if ($mimeType === false) {
            $mimeType = 'image/jpeg';
        }

        return base64_encode($imageData);
    }

    /**
     * Parse the API response to extract text content.
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

