<?php

declare(strict_types=1);

namespace SBAO\Speech;

use SBAO\Exception\InvalidAudioException;

/**
 * Validates audio inputs for Speech processing.
 *
 * @package SBAO\Speech
 */
class AudioValidator
{
    private const SUPPORTED_INPUT_FORMATS = ['mp3', 'wav', 'm4a', 'mp4', 'mpeg', 'mpga', 'webm'];
    private const SUPPORTED_OUTPUT_FORMATS = ['mp3', 'opus', 'aac', 'flac'];
    private const MAX_FILE_SIZE = 25 * 1024 * 1024; // 25MB (OpenAI Whisper limit)

    /**
     * Validate an audio file path for Speech-to-Text.
     *
     * @param string $filePath The path to the audio file
     * @return void
     * @throws InvalidAudioException
     */
    public function validateFile(string $filePath): void
    {
        if (!file_exists($filePath)) {
            throw new InvalidAudioException("Audio file does not exist: {$filePath}");
        }

        if (!is_readable($filePath)) {
            throw new InvalidAudioException("Audio file is not readable: {$filePath}");
        }

        $this->validateFileSize($filePath);
        $this->validateFileFormat($filePath);
    }

    /**
     * Validate a base64-encoded audio string.
     *
     * @param string $base64String The base64-encoded audio string
     * @return void
     * @throws InvalidAudioException
     */
    public function validateBase64(string $base64String): void
    {
        // Remove data URI prefix if present
        $base64String = preg_replace('/^data:audio\/[^;]+;base64,/', '', $base64String);

        if (empty($base64String)) {
            throw new InvalidAudioException('Base64 string is empty');
        }

        // Validate base64 format
        if (!preg_match('/^[a-zA-Z0-9+\/]*={0,2}$/', $base64String)) {
            throw new InvalidAudioException('Invalid base64 string format');
        }

        // Decode and validate
        $decoded = base64_decode($base64String, true);
        if ($decoded === false) {
            throw new InvalidAudioException('Failed to decode base64 string');
        }

        $this->validateAudioSize(strlen($decoded));
    }

    /**
     * Validate output format for Text-to-Speech.
     *
     * @param string $format The output format
     * @return void
     * @throws InvalidAudioException
     */
    public function validateOutputFormat(string $format): void
    {
        $format = strtolower($format);
        if (!in_array($format, self::SUPPORTED_OUTPUT_FORMATS, true)) {
            throw new InvalidAudioException(
                "Unsupported output format: {$format}. Supported formats: " . implode(', ', self::SUPPORTED_OUTPUT_FORMATS)
            );
        }
    }

    /**
     * Validate voice option for Text-to-Speech.
     *
     * @param string $voice The voice option
     * @return void
     * @throws InvalidAudioException
     */
    public function validateVoice(string $voice): void
    {
        $validVoices = ['alloy', 'echo', 'fable', 'onyx', 'nova', 'shimmer'];
        $voice = strtolower($voice);
        if (!in_array($voice, $validVoices, true)) {
            throw new InvalidAudioException(
                "Invalid voice: {$voice}. Valid voices: " . implode(', ', $validVoices)
            );
        }
    }

    /**
     * Validate file size.
     *
     * @param string $filePath
     * @return void
     * @throws InvalidAudioException
     */
    private function validateFileSize(string $filePath): void
    {
        $fileSize = filesize($filePath);
        if ($fileSize === false) {
            throw new InvalidAudioException("Cannot determine file size: {$filePath}");
        }

        if ($fileSize > self::MAX_FILE_SIZE) {
            throw new InvalidAudioException(
                "Audio file size ({$fileSize} bytes) exceeds maximum allowed size (" . self::MAX_FILE_SIZE . " bytes)"
            );
        }
    }

    /**
     * Validate file format by extension.
     *
     * @param string $filePath
     * @return void
     * @throws InvalidAudioException
     */
    private function validateFileFormat(string $filePath): void
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        if (!in_array($extension, self::SUPPORTED_INPUT_FORMATS, true)) {
            throw new InvalidAudioException(
                "Unsupported audio format: {$extension}. Supported formats: " . implode(', ', self::SUPPORTED_INPUT_FORMATS)
            );
        }
    }

    /**
     * Validate audio size in bytes.
     *
     * @param int $size
     * @return void
     * @throws InvalidAudioException
     */
    private function validateAudioSize(int $size): void
    {
        if ($size > self::MAX_FILE_SIZE) {
            throw new InvalidAudioException(
                "Audio size ({$size} bytes) exceeds maximum allowed size (" . self::MAX_FILE_SIZE . " bytes)"
            );
        }
    }
}

