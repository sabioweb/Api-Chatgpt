<?php

declare(strict_types=1);

namespace SBAO\Exception;

/**
 * Exception thrown when API rate limit is exceeded.
 *
 * @package SBAO\Exception
 */
class RateLimitException extends ApiException
{
    /**
     * @var int|null The timestamp when the rate limit resets, if available.
     */
    private ?int $retryAfter = null;

    /**
     * Get the timestamp when the rate limit resets.
     *
     * @return int|null
     */
    public function getRetryAfter(): ?int
    {
        return $this->retryAfter;
    }

    /**
     * Set the timestamp when the rate limit resets.
     *
     * @param int|null $retryAfter
     * @return self
     */
    public function setRetryAfter(?int $retryAfter): self
    {
        $this->retryAfter = $retryAfter;
        return $this;
    }
}

