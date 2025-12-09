# ChatGPT Multi-Model Package

A pure PHP OOP multi-model ChatGPT integration package supporting OCR, Mathematics, Programming, Chat Bot, and Speech models. This package is framework-agnostic, Composer-installable, and can be used in both standalone PHP projects and Laravel applications.

> **📖 [Read in Persian / خواندن به فارسی](README-FA.md)**

## Features

- **OCR Model**: Extract text from images using ChatGPT Vision API
- **Mathematics Model**: Solve mathematical problems and equations
- **Programming Model**: Code generation, analysis, and debugging assistance
- **Chat Bot Model**: Conversational AI interactions
- **Speech Model**: Speech-to-Text (audio transcription) and Text-to-Speech (audio synthesis)
- **Framework-agnostic**: Works in Laravel, Symfony, or standalone PHP
- **Composer-installable**: PSR-4 autoloading support
- **Comprehensive error handling**: Custom exceptions for different error scenarios

## Requirements

- PHP 8.1 or higher
- Composer
- ChatGPT API key

## Installation

```bash
composer require sabioweb/api-chatgpt
```

Or add to your `composer.json`:

```json
{
    "require": {
        "sabioweb/api-chatgpt": "^1.0"
    }
}
```

## Usage

### Basic Setup

```php
use SBAO\Factory\ServiceFactory;

$factory = new ServiceFactory('your-api-key-here');
```

### OCR Model

Extract text from images:

```php
// Using factory with default configuration
$ocrService = $factory->createOcrService();

// Extract from image file
$text = $ocrService->extractFromFile('/path/to/image.jpg');

// Extract from base64-encoded string
$base64Image = 'data:image/jpeg;base64,/9j/4AAQSkZJRg...';
$text = $ocrService->extractFromBase64($base64Image);

// Using custom configuration
use SBAO\Ocr\OcrConfig;

$config = new OcrConfig(
    model: 'gpt-4-vision-preview',
    quality: 'high',
    maxTokens: 500
);
$ocrService = $factory->createOcrService($config);
```

### Mathematics Model

Solve mathematical problems:

```php
// Using factory with default configuration
$mathService = $factory->createMathematicsService();

// Solve an equation
$solution = $mathService->solve('Solve for x: 2x + 5 = 15');

// Solve a word problem
$solution = $mathService->solve('If a train travels 120 km in 2 hours, what is its average speed?');

// Using custom configuration
use SBAO\Mathematics\MathematicsConfig;

$config = new MathematicsConfig(
    model: 'gpt-4',
    temperature: 0.3,
    maxTokens: 1000
);
$mathService = $factory->createMathematicsService($config);
```

### Programming Model

Generate, analyze, and debug code:

```php
// Using factory with default configuration
$programmingService = $factory->createProgrammingService();

// Generate code
$code = $programmingService->generateCode(
    'Create a PHP function to calculate factorial',
    'php'
);

// Analyze code
$analysis = $programmingService->analyzeCode(
    '<?php function test() { return $x; }',
    'php'
);

// Debug code
$debug = $programmingService->debugCode(
    '<?php function divide($a, $b) { return $a / $b; }',
    'Division by zero error',
    'php'
);

// Using custom configuration
use SBAO\Programming\ProgrammingConfig;

$config = new ProgrammingConfig(
    model: 'gpt-4',
    temperature: 0.2,
    maxTokens: 2000,
    defaultLanguage: 'php',
    codeStyle: 'PSR-12'
);
$programmingService = $factory->createProgrammingService($config);
```

### Chat Bot Model

Conversational AI interactions:

```php
// Using factory with default configuration
$chatBot = $factory->createChatBotService();

// Single message
$response = $chatBot->chat('Hello, how are you?');

// Multi-turn conversation (history is maintained automatically)
$response1 = $chatBot->chat('What is the capital of France?');
$response2 = $chatBot->chat('What is its population?');

// Get conversation history
$history = $chatBot->getHistory();

// Clear history
$chatBot->clearHistory();

// Using custom configuration
use SBAO\ChatBot\ChatBotConfig;

$config = new ChatBotConfig(
    model: 'gpt-4',
    temperature: 0.7,
    maxTokens: 1000,
    systemPrompt: 'You are a helpful assistant.',
    maxHistory: 20
);
$chatBot = $factory->createChatBotService($config);

// Using custom conversation history
$history = [
    ['role' => 'user', 'content' => 'Hello'],
    ['role' => 'assistant', 'content' => 'Hi there!'],
];
$response = $chatBot->chat('What is 2+2?', $history);
```

### Speech Model

Speech-to-Text and Text-to-Speech conversion:

```php
// Using factory with default configuration

// Speech-to-Text: Convert audio to text
$speechToText = $factory->createSpeechToTextService();

// Transcribe from audio file
$text = $speechToText->transcribeFromFile('/path/to/audio.mp3');

// Transcribe from base64-encoded audio
$base64Audio = 'data:audio/mp3;base64,UklGRiQAAABXQVZFZm10...';
$text = $speechToText->transcribeFromBase64($base64Audio, 'audio.mp3');

// Text-to-Speech: Convert text to audio
$textToSpeech = $factory->createTextToSpeechService();

// Synthesize text to audio (returns binary data)
$audioData = $textToSpeech->synthesize('Hello, world!', 'nova', 'mp3', 1.0);

// Synthesize and save to file
$textToSpeech->synthesizeToFile(
    'Hello, world!',
    '/path/to/output.mp3',
    'nova',  // voice: alloy, echo, fable, onyx, nova, shimmer
    'mp3',   // format: mp3, opus, aac, flac
    1.0      // speed: 0.25 to 4.0
);

// Using custom configuration
use SBAO\Speech\SpeechConfig;

// Speech-to-Text configuration
$sttConfig = new SpeechConfig(
    sttModel: 'whisper-1',
    sttLanguage: 'en',
    sttResponseFormat: 'json',
    sttTemperature: 0.0
);
$speechToText = $factory->createSpeechToTextService($sttConfig);

// Text-to-Speech configuration
$ttsConfig = new SpeechConfig(
    ttsModel: 'tts-1',
    ttsVoice: 'nova',
    ttsSpeed: 1.0,
    ttsFormat: 'mp3'
);
$textToSpeech = $factory->createTextToSpeechService($ttsConfig);
```

## Error Handling

The package provides custom exceptions for different error scenarios:

```php
use SBAO\Exception\InvalidImageException;
use SBAO\Exception\InvalidAudioException;
use SBAO\Exception\InvalidInputException;
use SBAO\Exception\ApiException;
use SBAO\Exception\NetworkException;
use SBAO\Exception\AuthenticationException;
use SBAO\Exception\RateLimitException;

try {
    $text = $ocrService->extractFromFile('/path/to/image.jpg');
    // Or for Speech-to-Text:
    // $text = $speechToText->transcribeFromFile('/path/to/audio.mp3');
} catch (InvalidImageException $e) {
    // Handle invalid image
    echo "Invalid image: " . $e->getMessage();
} catch (InvalidAudioException $e) {
    // Handle invalid audio
    echo "Invalid audio: " . $e->getMessage();
} catch (AuthenticationException $e) {
    // Handle authentication error
    echo "Authentication failed: " . $e->getMessage();
} catch (RateLimitException $e) {
    // Handle rate limiting
    echo "Rate limit exceeded. Retry after: " . $e->getRetryAfter();
} catch (NetworkException $e) {
    // Handle network errors
    echo "Network error: " . $e->getMessage();
} catch (ApiException $e) {
    // Handle other API errors
    echo "API error: " . $e->getMessage();
}
```

## Configuration Options

### OCR Configuration

- `model`: ChatGPT model to use (default: `gpt-4-vision-preview`)
- `quality`: Image quality setting (default: `high`)
- `maxTokens`: Maximum tokens for response (default: `300`)

### Mathematics Configuration

- `model`: ChatGPT model to use (default: `gpt-4`)
- `temperature`: Temperature parameter 0.0-2.0 (default: `0.3`)
- `maxTokens`: Maximum tokens for response (default: `1000`)

### Programming Configuration

- `model`: ChatGPT model to use (default: `gpt-4`)
- `temperature`: Temperature parameter 0.0-2.0 (default: `0.2`)
- `maxTokens`: Maximum tokens for response (default: `2000`)
- `defaultLanguage`: Default programming language (optional)
- `codeStyle`: Code style preferences (optional)

### Chat Bot Configuration

- `model`: ChatGPT model to use (default: `gpt-4`)
- `temperature`: Temperature parameter 0.0-2.0 (default: `0.7`)
- `maxTokens`: Maximum tokens for response (default: `1000`)
- `systemPrompt`: System prompt for the chat bot (optional)
- `maxHistory`: Maximum number of messages in history (default: `20`)

### Speech Configuration

**Speech-to-Text Options:**
- `sttModel`: Whisper model to use (default: `whisper-1`)
- `sttLanguage`: Language hint (optional, e.g., `en`, `fa`, `de`)
- `sttResponseFormat`: Response format (default: `json`)
- `sttTemperature`: Temperature parameter 0.0-1.0 (default: `0.0`)

**Text-to-Speech Options:**
- `ttsModel`: TTS model to use (default: `tts-1`)
- `ttsVoice`: Voice selection - `alloy`, `echo`, `fable`, `onyx`, `nova`, `shimmer` (default: `alloy`)
- `ttsSpeed`: Speed multiplier 0.25-4.0 (default: `1.0`)
- `ttsFormat`: Output format - `mp3`, `opus`, `aac`, `flac` (default: `mp3`)

## Supported Image Formats

- JPEG/JPG
- PNG
- GIF
- WebP

Maximum image size: 20MB

## Supported Audio Formats

**Speech-to-Text Input Formats:**
- MP3
- WAV
- M4A
- MP4
- MPEG
- MPGA
- WebM

Maximum audio file size: 25MB

**Text-to-Speech Output Formats:**
- MP3
- Opus
- AAC
- FLAC

**Text-to-Speech Voices:**
- `alloy` - Neutral, balanced voice
- `echo` - Clear, articulate voice
- `fable` - Warm, expressive voice
- `onyx` - Deep, authoritative voice
- `nova` - Bright, energetic voice
- `shimmer` - Soft, gentle voice

## API Requirements

- Valid ChatGPT API key
- API endpoint: `https://api.openai.com/v1/`
- Rate limits: Subject to OpenAI's rate limiting policies

## License

GPL-3.0-or-later

## Contributing

Contributions are welcome! Please ensure all code follows PSR-12 standards and includes appropriate tests.

## Support

For issues and questions, please open an issue on the project repository.

