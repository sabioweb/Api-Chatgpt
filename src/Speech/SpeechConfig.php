<?php

declare(strict_types=1);

namespace SBAO\Speech;

/**
 * Configuration class for Speech service (Speech-to-Text and Text-to-Speech).
 *
 * @package SBAO\Speech
 */
class SpeechConfig
{
    // Speech-to-Text defaults
    private const DEFAULT_STT_MODEL = 'whisper-1';
    private const DEFAULT_STT_RESPONSE_FORMAT = 'json';
    private const DEFAULT_STT_TEMPERATURE = 0.0;

    // Text-to-Speech defaults
    private const DEFAULT_TTS_MODEL = 'tts-1';
    private const DEFAULT_TTS_VOICE = 'alloy';
    private const DEFAULT_TTS_SPEED = 1.0;
    private const DEFAULT_TTS_FORMAT = 'mp3';

    // Speech-to-Text configuration
    private string $sttModel;
    private ?string $sttLanguage;
    private string $sttResponseFormat;
    private float $sttTemperature;

    // Text-to-Speech configuration
    private string $ttsModel;
    private string $ttsVoice;
    private float $ttsSpeed;
    private string $ttsFormat;

    /**
     * Create a new Speech configuration instance.
     *
     * @param string|null $sttModel Speech-to-Text model (default: whisper-1)
     * @param string|null $sttLanguage Speech-to-Text language hint (optional)
     * @param string|null $sttResponseFormat Speech-to-Text response format (default: json)
     * @param float|null $sttTemperature Speech-to-Text temperature (default: 0.0)
     * @param string|null $ttsModel Text-to-Speech model (default: tts-1)
     * @param string|null $ttsVoice Text-to-Speech voice (default: alloy)
     * @param float|null $ttsSpeed Text-to-Speech speed (default: 1.0)
     * @param string|null $ttsFormat Text-to-Speech output format (default: mp3)
     */
    public function __construct(
        ?string $sttModel = null,
        ?string $sttLanguage = null,
        ?string $sttResponseFormat = null,
        ?float $sttTemperature = null,
        ?string $ttsModel = null,
        ?string $ttsVoice = null,
        ?float $ttsSpeed = null,
        ?string $ttsFormat = null
    ) {
        $this->sttModel = $sttModel ?? self::DEFAULT_STT_MODEL;
        $this->sttLanguage = $sttLanguage;
        $this->sttResponseFormat = $sttResponseFormat ?? self::DEFAULT_STT_RESPONSE_FORMAT;
        $this->sttTemperature = $sttTemperature ?? self::DEFAULT_STT_TEMPERATURE;
        $this->ttsModel = $ttsModel ?? self::DEFAULT_TTS_MODEL;
        $this->ttsVoice = $ttsVoice ?? self::DEFAULT_TTS_VOICE;
        $this->ttsSpeed = $ttsSpeed ?? self::DEFAULT_TTS_SPEED;
        $this->ttsFormat = $ttsFormat ?? self::DEFAULT_TTS_FORMAT;
    }

    /**
     * Get the Speech-to-Text model.
     *
     * @return string
     */
    public function getSttModel(): string
    {
        return $this->sttModel;
    }

    /**
     * Get the Speech-to-Text language hint.
     *
     * @return string|null
     */
    public function getSttLanguage(): ?string
    {
        return $this->sttLanguage;
    }

    /**
     * Get the Speech-to-Text response format.
     *
     * @return string
     */
    public function getSttResponseFormat(): string
    {
        return $this->sttResponseFormat;
    }

    /**
     * Get the Speech-to-Text temperature.
     *
     * @return float
     */
    public function getSttTemperature(): float
    {
        return $this->sttTemperature;
    }

    /**
     * Get the Text-to-Speech model.
     *
     * @return string
     */
    public function getTtsModel(): string
    {
        return $this->ttsModel;
    }

    /**
     * Get the Text-to-Speech voice.
     *
     * @return string
     */
    public function getTtsVoice(): string
    {
        return $this->ttsVoice;
    }

    /**
     * Get the Text-to-Speech speed.
     *
     * @return float
     */
    public function getTtsSpeed(): float
    {
        return $this->ttsSpeed;
    }

    /**
     * Get the Text-to-Speech output format.
     *
     * @return string
     */
    public function getTtsFormat(): string
    {
        return $this->ttsFormat;
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

