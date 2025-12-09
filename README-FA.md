# پکیج چند مدلی ChatGPT

یک پکیج PHP خالص OOP برای یکپارچه‌سازی چند مدلی ChatGPT که از مدل‌های OCR، ریاضیات، برنامه‌نویسی، چت‌بات و گفتار پشتیبانی می‌کند. این پکیج مستقل از فریمورک است، از طریق Composer قابل نصب است و می‌تواند در پروژه‌های PHP مستقل و برنامه‌های Laravel استفاده شود.

## ویژگی‌ها

- **مدل OCR**: استخراج متن از تصاویر با استفاده از ChatGPT Vision API
- **مدل ریاضیات**: حل مسائل و معادلات ریاضی
- **مدل برنامه‌نویسی**: تولید کد، تحلیل و کمک به دیباگ
- **مدل چت‌بات**: تعاملات هوش مصنوعی مکالمه‌ای
- **مدل گفتار**: تبدیل گفتار به متن (رونویسی صوتی) و تبدیل متن به گفتار (سنتز صوتی)
- **مستقل از فریمورک**: در Laravel، Symfony یا PHP مستقل کار می‌کند
- **قابل نصب با Composer**: پشتیبانی از PSR-4 autoloading
- **مدیریت خطای جامع**: Exception های سفارشی برای سناریوهای خطای مختلف

## نیازمندی‌ها

- PHP 8.1 یا بالاتر
- Composer
- کلید API ChatGPT

## نصب

```bash
composer require sabioweb/api-chatgpt
```

یا به `composer.json` خود اضافه کنید:

```json
{
    "require": {
        "sabioweb/api-chatgpt": "^1.0"
    }
}
```

## استفاده

### راه‌اندازی اولیه

```php
use SBAO\Factory\ServiceFactory;

$factory = new ServiceFactory('your-api-key-here');
```

### مدل OCR

استخراج متن از تصاویر:

```php
// استفاده از factory با تنظیمات پیش‌فرض
$ocrService = $factory->createOcrService();

// استخراج از فایل تصویر
$text = $ocrService->extractFromFile('/path/to/image.jpg');

// استخراج از رشته base64
$base64Image = 'data:image/jpeg;base64,/9j/4AAQSkZJRg...';
$text = $ocrService->extractFromBase64($base64Image);

// استفاده از تنظیمات سفارشی
use SBAO\Ocr\OcrConfig;

$config = new OcrConfig(
    model: 'gpt-4-vision-preview',
    quality: 'high',
    maxTokens: 500
);
$ocrService = $factory->createOcrService($config);
```

### مدل ریاضیات

حل مسائل ریاضی:

```php
// استفاده از factory با تنظیمات پیش‌فرض
$mathService = $factory->createMathematicsService();

// حل یک معادله
$solution = $mathService->solve('Solve for x: 2x + 5 = 15');

// حل یک مسئله کلامی
$solution = $mathService->solve('If a train travels 120 km in 2 hours, what is its average speed?');

// استفاده از تنظیمات سفارشی
use SBAO\Mathematics\MathematicsConfig;

$config = new MathematicsConfig(
    model: 'gpt-4',
    temperature: 0.3,
    maxTokens: 1000
);
$mathService = $factory->createMathematicsService($config);
```

### مدل برنامه‌نویسی

تولید، تحلیل و دیباگ کد:

```php
// استفاده از factory با تنظیمات پیش‌فرض
$programmingService = $factory->createProgrammingService();

// تولید کد
$code = $programmingService->generateCode(
    'Create a PHP function to calculate factorial',
    'php'
);

// تحلیل کد
$analysis = $programmingService->analyzeCode(
    '<?php function test() { return $x; }',
    'php'
);

// دیباگ کد
$debug = $programmingService->debugCode(
    '<?php function divide($a, $b) { return $a / $b; }',
    'Division by zero error',
    'php'
);

// استفاده از تنظیمات سفارشی
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

### مدل چت‌بات

تعاملات هوش مصنوعی مکالمه‌ای:

```php
// استفاده از factory با تنظیمات پیش‌فرض
$chatBot = $factory->createChatBotService();

// پیام واحد
$response = $chatBot->chat('Hello, how are you?');

// مکالمه چند مرحله‌ای (تاریخچه به صورت خودکار حفظ می‌شود)
$response1 = $chatBot->chat('What is the capital of France?');
$response2 = $chatBot->chat('What is its population?');

// دریافت تاریخچه مکالمه
$history = $chatBot->getHistory();

// پاک کردن تاریخچه
$chatBot->clearHistory();

// استفاده از تنظیمات سفارشی
use SBAO\ChatBot\ChatBotConfig;

$config = new ChatBotConfig(
    model: 'gpt-4',
    temperature: 0.7,
    maxTokens: 1000,
    systemPrompt: 'You are a helpful assistant.',
    maxHistory: 20
);
$chatBot = $factory->createChatBotService($config);

// استفاده از تاریخچه مکالمه سفارشی
$history = [
    ['role' => 'user', 'content' => 'Hello'],
    ['role' => 'assistant', 'content' => 'Hi there!'],
];
$response = $chatBot->chat('What is 2+2?', $history);
```

### مدل گفتار

تبدیل گفتار به متن و متن به گفتار:

```php
// استفاده از factory با تنظیمات پیش‌فرض

// گفتار به متن: تبدیل صدا به متن
$speechToText = $factory->createSpeechToTextService();

// رونویسی از فایل صوتی
$text = $speechToText->transcribeFromFile('/path/to/audio.mp3');

// رونویسی از رشته base64
$base64Audio = 'data:audio/mp3;base64,UklGRiQAAABXQVZFZm10...';
$text = $speechToText->transcribeFromBase64($base64Audio, 'audio.mp3');

// متن به گفتار: تبدیل متن به صدا
$textToSpeech = $factory->createTextToSpeechService();

// سنتز متن به صدا (برمی‌گرداند داده باینری)
$audioData = $textToSpeech->synthesize('سلام دنیا!', 'nova', 'mp3', 1.0);

// سنتز و ذخیره در فایل
$textToSpeech->synthesizeToFile(
    'سلام دنیا!',
    '/path/to/output.mp3',
    'nova',  // صدا: alloy, echo, fable, onyx, nova, shimmer
    'mp3',   // فرمت: mp3, opus, aac, flac
    1.0      // سرعت: 0.25 تا 4.0
);

// استفاده از تنظیمات سفارشی
use SBAO\Speech\SpeechConfig;

// پیکربندی گفتار به متن
$sttConfig = new SpeechConfig(
    sttModel: 'whisper-1',
    sttLanguage: 'fa',
    sttResponseFormat: 'json',
    sttTemperature: 0.0
);
$speechToText = $factory->createSpeechToTextService($sttConfig);

// پیکربندی متن به گفتار
$ttsConfig = new SpeechConfig(
    ttsModel: 'tts-1',
    ttsVoice: 'nova',
    ttsSpeed: 1.0,
    ttsFormat: 'mp3'
);
$textToSpeech = $factory->createTextToSpeechService($ttsConfig);
```

## مدیریت خطا

پکیج Exception های سفارشی برای سناریوهای خطای مختلف ارائه می‌دهد:

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
    // یا برای گفتار به متن:
    // $text = $speechToText->transcribeFromFile('/path/to/audio.mp3');
} catch (InvalidImageException $e) {
    // مدیریت تصویر نامعتبر
    echo "Invalid image: " . $e->getMessage();
} catch (InvalidAudioException $e) {
    // مدیریت صوتی نامعتبر
    echo "Invalid audio: " . $e->getMessage();
} catch (AuthenticationException $e) {
    // مدیریت خطای احراز هویت
    echo "Authentication failed: " . $e->getMessage();
} catch (RateLimitException $e) {
    // مدیریت محدودیت نرخ
    echo "Rate limit exceeded. Retry after: " . $e->getRetryAfter();
} catch (NetworkException $e) {
    // مدیریت خطاهای شبکه
    echo "Network error: " . $e->getMessage();
} catch (ApiException $e) {
    // مدیریت سایر خطاهای API
    echo "API error: " . $e->getMessage();
}
```

## گزینه‌های پیکربندی

### پیکربندی OCR

- `model`: مدل ChatGPT برای استفاده (پیش‌فرض: `gpt-4-vision-preview`)
- `quality`: تنظیم کیفیت تصویر (پیش‌فرض: `high`)
- `maxTokens`: حداکثر توکن برای پاسخ (پیش‌فرض: `300`)

### پیکربندی ریاضیات

- `model`: مدل ChatGPT برای استفاده (پیش‌فرض: `gpt-4`)
- `temperature`: پارامتر دما 0.0-2.0 (پیش‌فرض: `0.3`)
- `maxTokens`: حداکثر توکن برای پاسخ (پیش‌فرض: `1000`)

### پیکربندی برنامه‌نویسی

- `model`: مدل ChatGPT برای استفاده (پیش‌فرض: `gpt-4`)
- `temperature`: پارامتر دما 0.0-2.0 (پیش‌فرض: `0.2`)
- `maxTokens`: حداکثر توکن برای پاسخ (پیش‌فرض: `2000`)
- `defaultLanguage`: زبان برنامه‌نویسی پیش‌فرض (اختیاری)
- `codeStyle`: ترجیحات سبک کد (اختیاری)

### پیکربندی چت‌بات

- `model`: مدل ChatGPT برای استفاده (پیش‌فرض: `gpt-4`)
- `temperature`: پارامتر دما 0.0-2.0 (پیش‌فرض: `0.7`)
- `maxTokens`: حداکثر توکن برای پاسخ (پیش‌فرض: `1000`)
- `systemPrompt`: پیام سیستم برای چت‌بات (اختیاری)
- `maxHistory`: حداکثر تعداد پیام در تاریخچه (پیش‌فرض: `20`)

### پیکربندی گفتار

**گزینه‌های گفتار به متن:**
- `sttModel`: مدل Whisper برای استفاده (پیش‌فرض: `whisper-1`)
- `sttLanguage`: راهنمای زبان (اختیاری، مثلاً `fa`, `en`, `de`)
- `sttResponseFormat`: فرمت پاسخ (پیش‌فرض: `json`)
- `sttTemperature`: پارامتر دما 0.0-1.0 (پیش‌فرض: `0.0`)

**گزینه‌های متن به گفتار:**
- `ttsModel`: مدل TTS برای استفاده (پیش‌فرض: `tts-1`)
- `ttsVoice`: انتخاب صدا - `alloy`, `echo`, `fable`, `onyx`, `nova`, `shimmer` (پیش‌فرض: `alloy`)
- `ttsSpeed`: ضریب سرعت 0.25-4.0 (پیش‌فرض: `1.0`)
- `ttsFormat`: فرمت خروجی - `mp3`, `opus`, `aac`, `flac` (پیش‌فرض: `mp3`)

## فرمت‌های تصویر پشتیبانی شده

- JPEG/JPG
- PNG
- GIF
- WebP

حداکثر اندازه تصویر: 20MB

## فرمت‌های صوتی پشتیبانی شده

**فرمت‌های ورودی گفتار به متن:**
- MP3
- WAV
- M4A
- MP4
- MPEG
- MPGA
- WebM

حداکثر اندازه فایل صوتی: 25MB

**فرمت‌های خروجی متن به گفتار:**
- MP3
- Opus
- AAC
- FLAC

**صداهای متن به گفتار:**
- `alloy` - صدای خنثی و متعادل
- `echo` - صدای واضح و رسا
- `fable` - صدای گرم و بیانگر
- `onyx` - صدای عمیق و معتبر
- `nova` - صدای روشن و پرانرژی
- `shimmer` - صدای نرم و ملایم

## نیازمندی‌های API

- کلید API معتبر ChatGPT
- Endpoint API: `https://api.openai.com/v1/`
- محدودیت نرخ: تابع سیاست‌های محدودیت نرخ OpenAI

## مجوز

GPL-3.0-or-later

## مشارکت

مشارکت‌ها خوش‌آمد هستند! لطفاً مطمئن شوید که همه کدها از استانداردهای PSR-12 پیروی می‌کنند و شامل تست‌های مناسب هستند.

## پشتیبانی

برای مشکلات و سوالات، لطفاً یک issue در مخزن پروژه باز کنید.

