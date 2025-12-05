<?php

declare(strict_types=1);

namespace SBAO\Ocr;

use SBAO\Exception\InvalidImageException;

/**
 * Validates image inputs for OCR processing.
 *
 * @package SBAO\Ocr
 */
class ImageValidator
{
    private const SUPPORTED_FORMATS = ['jpeg', 'jpg', 'png', 'gif', 'webp'];
    private const MAX_FILE_SIZE = 20 * 1024 * 1024; // 20MB

    /**
     * Validate an image file path.
     *
     * @param string $filePath The path to the image file
     * @return void
     * @throws InvalidImageException
     */
    public function validateFile(string $filePath): void
    {
        if (!file_exists($filePath)) {
            throw new InvalidImageException("Image file does not exist: {$filePath}");
        }

        if (!is_readable($filePath)) {
            throw new InvalidImageException("Image file is not readable: {$filePath}");
        }

        $this->validateFileSize($filePath);
        $this->validateFileFormat($filePath);
    }

    /**
     * Validate a base64-encoded image string.
     *
     * @param string $base64String The base64-encoded image string
     * @return void
     * @throws InvalidImageException
     */
    public function validateBase64(string $base64String): void
    {
        // Remove data URI prefix if present
        $base64String = preg_replace('/^data:image\/[^;]+;base64,/', '', $base64String);

        if (empty($base64String)) {
            throw new InvalidImageException('Base64 string is empty');
        }

        // Validate base64 format
        if (!preg_match('/^[a-zA-Z0-9+\/]*={0,2}$/', $base64String)) {
            throw new InvalidImageException('Invalid base64 string format');
        }

        // Decode and validate
        $decoded = base64_decode($base64String, true);
        if ($decoded === false) {
            throw new InvalidImageException('Failed to decode base64 string');
        }

        // Validate image format
        $imageInfo = @getimagesizefromstring($decoded);
        if ($imageInfo === false) {
            throw new InvalidImageException('Decoded base64 string is not a valid image');
        }

        $this->validateImageFormat($imageInfo[2]);
        $this->validateImageSize(strlen($decoded));
    }

    /**
     * Validate file size.
     *
     * @param string $filePath
     * @return void
     * @throws InvalidImageException
     */
    private function validateFileSize(string $filePath): void
    {
        $fileSize = filesize($filePath);
        if ($fileSize === false) {
            throw new InvalidImageException("Cannot determine file size: {$filePath}");
        }

        if ($fileSize > self::MAX_FILE_SIZE) {
            throw new InvalidImageException(
                "Image file size ({$fileSize} bytes) exceeds maximum allowed size (" . self::MAX_FILE_SIZE . " bytes)"
            );
        }
    }

    /**
     * Validate file format by extension and MIME type.
     *
     * @param string $filePath
     * @return void
     * @throws InvalidImageException
     */
    private function validateFileFormat(string $filePath): void
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        if (!in_array($extension, self::SUPPORTED_FORMATS, true)) {
            throw new InvalidImageException(
                "Unsupported image format: {$extension}. Supported formats: " . implode(', ', self::SUPPORTED_FORMATS)
            );
        }

        $imageInfo = @getimagesize($filePath);
        if ($imageInfo === false) {
            throw new InvalidImageException("File is not a valid image: {$filePath}");
        }

        $this->validateImageFormat($imageInfo[2]);
    }

    /**
     * Validate image format by IMAGETYPE constant.
     *
     * @param int $imageType The IMAGETYPE constant value
     * @return void
     * @throws InvalidImageException
     */
    private function validateImageFormat(int $imageType): void
    {
        $supportedTypes = [
            IMAGETYPE_JPEG,
            IMAGETYPE_PNG,
            IMAGETYPE_GIF,
            IMAGETYPE_WEBP,
        ];

        if (!in_array($imageType, $supportedTypes, true)) {
            throw new InvalidImageException('Unsupported image type');
        }
    }

    /**
     * Validate image size in bytes.
     *
     * @param int $size
     * @return void
     * @throws InvalidImageException
     */
    private function validateImageSize(int $size): void
    {
        if ($size > self::MAX_FILE_SIZE) {
            throw new InvalidImageException(
                "Image size ({$size} bytes) exceeds maximum allowed size (" . self::MAX_FILE_SIZE . " bytes)"
            );
        }
    }
}

