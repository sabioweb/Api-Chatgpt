<?php

declare(strict_types=1);

namespace SBAO\Client;

use SBAO\Exception\ApiException;
use SBAO\Exception\AuthenticationException;
use SBAO\Exception\NetworkException;
use SBAO\Exception\RateLimitException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

/**
 * Unified API client for communicating with ChatGPT API endpoints.
 *
 * @package SBAO\Client
 */
class ApiClient
{
    private const BASE_URI = 'https://api.openai.com/v1/';
    private const MAX_RETRIES = 3;
    private const RETRY_DELAY = 1000; // milliseconds

    private Client $httpClient;
    private string $apiKey;

    /**
     * Create a new API client instance.
     *
     * @param string $apiKey The ChatGPT API key
     * @param Client|null $httpClient Optional HTTP client for testing
     */
    public function __construct(string $apiKey, ?Client $httpClient = null)
    {
        $this->apiKey = $apiKey;
        $this->httpClient = $httpClient ?? new Client([
            'base_uri' => self::BASE_URI,
            'timeout' => 30.0,
        ]);
    }

    /**
     * Make a POST request to the ChatGPT API.
     *
     * @param string $endpoint The API endpoint (e.g., 'chat/completions')
     * @param array $data The request payload
     * @return array The decoded JSON response
     * @throws ApiException
     * @throws AuthenticationException
     * @throws NetworkException
     * @throws RateLimitException
     */
    public function post(string $endpoint, array $data): array
    {
        $attempt = 0;
        $lastException = null;

        while ($attempt < self::MAX_RETRIES) {
            try {
                $response = $this->httpClient->post($endpoint, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->apiKey,
                        'Content-Type' => 'application/json',
                    ],
                    'json' => $data,
                ]);

                return $this->parseResponse($response);
            } catch (RequestException $e) {
                $lastException = $e;
                $response = $e->getResponse();

                if ($response !== null) {
                    $statusCode = $response->getStatusCode();
                    $errorData = $this->parseErrorResponse($response);

                    // Handle rate limiting
                    if ($statusCode === 429) {
                        $rateLimitException = new RateLimitException(
                            $errorData['message'] ?? 'Rate limit exceeded',
                            $statusCode
                        );

                        if (isset($errorData['retry_after'])) {
                            $rateLimitException->setRetryAfter((int)$errorData['retry_after']);
                        } elseif ($response->hasHeader('Retry-After')) {
                            $retryAfter = (int)$response->getHeaderLine('Retry-After');
                            $rateLimitException->setRetryAfter(time() + $retryAfter);
                        }

                        throw $rateLimitException;
                    }

                    // Handle authentication errors
                    if ($statusCode === 401) {
                        throw new AuthenticationException(
                            $errorData['message'] ?? 'Authentication failed',
                            $statusCode,
                            $e
                        );
                    }

                    // Handle API errors
                    if ($statusCode >= 400 && $statusCode < 500) {
                        throw new ApiException(
                            $errorData['message'] ?? 'API request failed',
                            $statusCode,
                            $e
                        );
                    }

                    // Retry on server errors (5xx)
                    if ($statusCode >= 500 && $attempt < self::MAX_RETRIES - 1) {
                        $attempt++;
                        usleep(self::RETRY_DELAY * 1000 * $attempt); // Exponential backoff
                        continue;
                    }

                    throw new ApiException(
                        $errorData['message'] ?? 'API request failed',
                        $statusCode,
                        $e
                    );
                }

                // Network errors
                if ($e instanceof GuzzleException) {
                    if ($attempt < self::MAX_RETRIES - 1) {
                        $attempt++;
                        usleep(self::RETRY_DELAY * 1000 * $attempt);
                        continue;
                    }
                    throw new NetworkException(
                        'Network error: ' . $e->getMessage(),
                        0,
                        $e
                    );
                }

                throw new NetworkException(
                    'Unexpected error: ' . $e->getMessage(),
                    0,
                    $e
                );
            }
        }

        // If we get here, all retries failed
        if ($lastException instanceof NetworkException) {
            throw $lastException;
        }

        throw new NetworkException(
            'Failed to complete request after ' . self::MAX_RETRIES . ' attempts',
            0,
            $lastException
        );
    }

    /**
     * Parse a successful API response.
     *
     * @param ResponseInterface $response
     * @return array
     * @throws ApiException
     */
    private function parseResponse(ResponseInterface $response): array
    {
        $body = (string)$response->getBody();
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ApiException(
                'Invalid JSON response: ' . json_last_error_msg(),
                $response->getStatusCode()
            );
        }

        return $data;
    }

    /**
     * Parse an error response from the API.
     *
     * @param ResponseInterface $response
     * @return array
     */
    private function parseErrorResponse(ResponseInterface $response): array
    {
        $body = (string)$response->getBody();
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'message' => 'Unknown error',
                'status' => $response->getStatusCode(),
            ];
        }

        return $data['error'] ?? $data;
    }
}

