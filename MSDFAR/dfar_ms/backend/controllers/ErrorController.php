<?php

namespace backend\controllers;

use Throwable;
use Yii;
use backend\components\Controller;

use yii\web\HttpException;
use yii\web\Response;
use yii\web\ForbiddenHttpException;
use backend\config\UserTypeUtil;


class ErrorController extends Controller
{
    // public $layout = false;

    public function actionIndex(): string
    {
        $exception = Yii::$app->errorHandler->exception;

        $statusCode = $exception instanceof HttpException
            ? $exception->statusCode
            : 500;

        Yii::$app->response->format = Response::FORMAT_HTML;
        Yii::$app->response->statusCode = $statusCode;

        $referenceCode = $this->generateReferenceCode();

        // Store the reference code with the technical error in the server log.
        $this->logError($exception, $referenceCode, $statusCode);

        $errorContent = $this->getPublicErrorContent($statusCode);

        return $this->render('index', [
            'statusCode' => $statusCode,
            'title' => $errorContent['title'],
            'message' => $errorContent['message'],
            'referenceCode' => $referenceCode,
            'occurredAt' => Yii::$app->formatter->asDatetime(
                time(),
                'php:Y-m-d H:i:s T'
            ),
        ]);
    }

    
    private function generateReferenceCode(): string
    {
        try {
            $randomPart = strtoupper(
                substr(Yii::$app->security->generateRandomString(10), 0, 8)
            );
        } catch (Throwable $exception) {
            $randomPart = strtoupper(substr(md5(uniqid('', true)), 0, 8));
        }

        return 'ERR-' . date('Ymd-His') . '-' . $randomPart;
    }

    private function logError(
    ?\Throwable $exception,
    string $referenceCode,
    int $statusCode
): void {
    if ($exception === null) {
        return;
    }

    $route = Yii::$app->requestedRoute ?: Yii::$app->request->url;

    Yii::error(
        sprintf(
            "Error reference: %s\n" .
            "HTTP status: %d\n" .
            "Route: %s\n" .
            "Exception: %s\n" .
            "Message: %s\n" .
            "File: %s\n" .
            "Line: %d\n" .
            "Stack trace:\n%s",
            $referenceCode,
            $statusCode,
            $route,
            get_class($exception),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTraceAsString()
        ),
        'secure-error'
    );
}

    private function getPublicErrorContent(int $statusCode): array
{
    switch ($statusCode) {

        case 400:
            return [
                'title' => 'Invalid Request, අවලංගු ඉල්ලීම, தவறான கோரிக்கை',
                'message' => 'The information sent to the system is incorrect or incomplete. Please check your input and try again., පද්ධතියට එවන ලද තොරතුරු වැරදි හෝ අසම්පූර්ණ වේ. කරුණාකර ඔබ ඇතුළත් කළ තොරතුරු පරීක්ෂා කර නැවත උත්සාහ කරන්න., கணினிக்கு அனுப்பப்பட்ட தகவல் தவறாக அல்லது முழுமையற்றதாக உள்ளது. தயவுசெய்து உங்கள் உள்ளீட்டை சரிபார்த்து மீண்டும் முயற்சிக்கவும்.',
            ];

        case 401:
            return [
                'title' => 'Unauthorized, අවසර නොමැත, அங்கீகாரம் இல்லை',
                'message' => 'You are not allowed to perform this request. Please contact the system administrator if you believe this is incorrect., මෙම ඉල්ලීම ක්‍රියාත්මක කිරීමට ඔබට අවසර නොමැත. මෙය වැරදියි යැයි ඔබ සිතන්නේ නම් කරුණාකර පද්ධති පරිපාලක අමතන්න., இந்த கோரிக்கையை நிறைவேற்ற உங்களுக்கு அனுமதி இல்லை. இது தவறு என நீங்கள் நினைத்தால், தயவுசெய்து கணினி நிர்வாகியை தொடர்பு கொள்ளவும்.',
            ];

        case 403:
            return [
                'title' => 'Fobbiden, ප්‍රවේශය ප්‍රතික්ෂේප විය, அணுகல் மறுக்கப்பட்டது',
                'message' => 'You do not have sufficient permission to access this page. Please contact the system administrator if you believe this is incorrect., මෙම පිටුවට ප්‍රවේශ වීමට ඔබට ප්‍රමාණවත් අවසරයක් නොමැත. මෙය වැරදි යැයි ඔබ සිතන්නේ නම් කරුණාකර පද්ධති පරිපාලක අමතන්න., இந்தப் பக்கத்தை அணுக உங்களுக்கு போதிய அனுமதி இல்லை. இது தவறு எனக் கருதினால் தயவுசெய்து கணினி நிர்வாகியை தொடர்பு கொள்ளவும்.',
            ];

        case 404:
            return [
                'title' => 'Page Not Found, පිටුව හමු නොවීය, பக்கம் கிடைக்கவில்லை',
                'message' => 'The page you requested does not exist or may have been moved. Please check the URL and try again., ඔබ ඉල්ලූ පිටුව නොපවතී හෝ එය වෙනත් තැනකට ගෙන යා හැක. කරුණාකර URL එක පරීක්ෂා කර නැවත උත්සාහ කරන්න., நீங்கள் கோரிய பக்கம் இல்லை அல்லது வேறு இடத்திற்கு நகர்த்தப்பட்டிருக்கலாம். தயவுசெய்து URL ஐ சரிபார்த்து மீண்டும் முயற்சிக்கவும்.',
            ];

        case 405:
            return [
                'title' => 'Action Not Allowed, ක්‍රියාව අවසර නැත, செயல் அனுமதிக்கப்படவில்லை',
                'message' => 'The requested operation is not supported for this page., ඉල්ලූ ක්‍රියාව මෙම පිටුව සඳහා සහාය නොදක්වයි., கோரப்பட்ட செயல்பாடு இந்தப் பக்கத்திற்கு ஆதரிக்கப்படவில்லை.',
            ];

        case 408:
            return [
                'title' => 'Request Timeout, ඉල්ලීම කාලය ඉකුත් විය, கோரிக்கை நேரம் முடிந்தது',
                'message' => 'The request took too long to complete. Please try again., ඉල්ලීම සම්පූර්ණ කිරීමට වැඩි කාලයක් ගත විය. කරුණාකර නැවත උත්සාහ කරන්න., கோரிக்கையை முடிக்க அதிக நேரம் எடுத்தது. தயவுசெய்து மீண்டும் முயற்சிக்கவும்.',
            ];

        case 419:
            return [
                'title' => 'Session Expired, සැසිය කල් ඉකුත් විය, அமர்வு காலாவதியானது',
                'message' => 'Your session has expired due to inactivity. Please refresh the page and try again., ක්‍රියාකාරීත්වයක් නොමැති වීම හේතුවෙන් ඔබගේ සැසිය කල් ඉකුත් වී ඇත. කරුණාකර පිටුව නැවුම් කර නැවත උත්සාහ කරන්න., செயலற்ற தன்மையால் உங்கள் அமர்வு காலாவதியாகிவிட்டது. தயவுசெய்து பக்கத்தை புதுப்பித்து மீண்டும் முயற்சிக்கவும்.',
            ];

        case 422:
            return [
                'title' => 'Validation Failed, සත්‍යාපනය අසාර්ථක විය, சரிபார்ப்பு தோல்வியடைந்தது',
                'message' => 'Some information provided could not be processed. Please review the data and submit again., ලබා දුන් සමහර තොරතුරු සැකසීමට නොහැකි විය. කරුණාකර දත්ත සමාලෝචනය කර නැවත ඉදිරිපත් කරන්න., வழங்கப்பட்ட சில தகவல்களை செயலாக்க முடியவில்லை. தயவுசெய்து தரவை சரிபார்த்து மீண்டும் சமர்ப்பிக்கவும்.',
            ];

        case 429:
            return [
                'title' => 'Too Many Requests, ඉල්ලීම් ඉතා වැඩිය, அதிக கோரிக்கைகள்',
                'message' => 'Too many requests were sent to the system. Please wait a moment and try again., පද්ධතියට ඉතා වැඩි ඉල්ලීම් ගණනක් යවා ඇත. කරුණාකර ටික වේලාවක් රැඳී නැවත උත්සාහ කරන්න., கணினிக்கு அதிக அளவு கோரிக்கைகள் அனுப்பப்பட்டன. தயவுசெய்து சிறிது நேரம் காத்திருந்து மீண்டும் முயற்சிக்கவும்.',
            ];

        case 500:
            return [
                'title' => 'Internal System Error, අභ්‍යන්තර පද්ධති දෝෂයකි, உள் கணினி பிழை',
                'message' => 'An unexpected error occurred while processing your request. Please provide the reference code when contacting support., ඔබගේ ඉල්ලීම සැකසීමේදී අනපේක්ෂිත දෝෂයක් ඇති විය. සහාය අමතන විට කරුණාකර යොමු කේතය ලබා දෙන්න., உங்கள் கோரிக்கையை செயலாக்கும்போது எதிர்பாராத பிழை ஏற்பட்டது. ஆதரவை தொடர்பு கொள்ளும்போது தயவுசெய்து குறிப்பு குறியீட்டை வழங்கவும்.',
            ];

        case 502:
            return [
                'title' => 'Service Temporarily Unavailable, සේවාව තාවකාලිකව ලබා ගත නොහැක, சேவை தற்காலிகமாக கிடைக்கவில்லை',
                'message' => 'The system is currently unable to communicate with a required service. Please try again later., අවශ්‍ය සේවාවක් සමඟ සන්නිවේදනය කිරීමට පද්ධතියට දැනට නොහැකි වේ. කරුණාකර පසුව නැවත උත්සාහ කරන්න., தேவையான சேவையுடன் தொடர்பு கொள்ள கணினியால் தற்போது முடியவில்லை. தயவுசெய்து பின்னர் மீண்டும் முயற்சிக்கவும்.',
            ];

        case 503:
            return [
                'title' => 'System Maintenance, පද්ධති නඩත්තුව, கணினி பராமரிப்பு',
                'message' => 'The system is temporarily unavailable due to maintenance or high usage. Please try again later., නඩත්තු කටයුතු හෝ අධික භාවිතය හේතුවෙන් පද්ධතිය තාවකාලිකව ලබා ගත නොහැක. කරුණාකර පසුව නැවත උත්සාහ කරන්න., பராமரிப்பு அல்லது அதிக பயன்பாடு காரணமாக கணினி தற்காலிகமாக கிடைக்கவில்லை. தயவுசெய்து பின்னர் மீண்டும் முயற்சிக்கவும்.',
            ];

        default:
            return [
                'title' => 'Something Went Wrong, යම් දෝෂයක් සිදු විය, ஏதோ தவறு ஏற்பட்டது',
                'message' => 'The system could not complete your request due to an unexpected problem. Please try again or contact technical support with the reference code., අනපේක්ෂිත ගැටළුවක් හේතුවෙන් ඔබගේ ඉල්ලීම සම්පූර්ණ කිරීමට පද්ධතියට නොහැකි විය. කරුණාකර නැවත උත්සාහ කරන්න හෝ යොමු කේතය සමඟ තාක්ෂණික සහාය අමතන්න., எதிர்பாராத சிக்கல் காரணமாக உங்கள் கோரிக்கையை முடிக்க கணினியால் முடியவில்லை. தயவுசெய்து மீண்டும் முயற்சிக்கவும் அல்லது குறிப்பு குறியீட்டுடன் தொழில்நுட்ப ஆதரவை தொடர்பு கொள்ளவும்.',
            ];
    }
}
         public function actionReader()
{
     if (
        Yii::$app->user->isGuest ||
        !UserTypeUtil::hasType(11)
    ) {
        throw new ForbiddenHttpException(
            'You are not authorized to access the system error reader.'
        );
    }

    $this->layout = 'main';

    $referenceCode = strtoupper(trim(
        (string) Yii::$app->request->get('reference_code', '')
    ));

    $result = null;
    $errorMessage = null;
    $searched = false;

    if ($referenceCode !== '') {
        $searched = true;

        $isValidReference = preg_match(
            '/^ERR-\d{8}-\d{6}-[A-Z0-9_-]{6,20}$/',
            $referenceCode
        );

        if (!$isValidReference) {
            $errorMessage = 'Enter a valid error reference code.';
        } else {
            $result = $this->findErrorInLogFiles($referenceCode);

            if ($result === null) {
                $errorMessage =
                    'No matching error was found in the available log files.';
            }
        }
    }

    return $this->render('reader', [
        'referenceCode' => $referenceCode,
        'result' => $result,
        'errorMessage' => $errorMessage,
        'searched' => $searched,
    ]);
}

private function findErrorInLogFiles(
    string $referenceCode
): ?array {
    $logDirectory = Yii::getAlias('@runtime/logs');
    $mainLogFile = $logDirectory . DIRECTORY_SEPARATOR . 'app.log';

    /*
     * Search the current log and Yii rotated log files.
     */
    $logFiles = glob($mainLogFile . '*') ?: [];

    /*
     * Only accept app.log and app.log.N files.
     */
    $logFiles = array_filter(
        $logFiles,
        static function (string $file): bool {
            $fileName = basename($file);

            return $fileName === 'app.log' ||
                preg_match('/^app\.log\.\d+$/', $fileName) === 1;
        }
    );

    /*
     * Search newest log files first.
     */
    usort(
        $logFiles,
        static function (string $first, string $second): int {
            return filemtime($second) <=> filemtime($first);
        }
    );

    foreach ($logFiles as $logFile) {
        if (!is_file($logFile) || !is_readable($logFile)) {
            continue;
        }

        $entry = $this->extractMatchingLogEntry(
            $logFile,
            $referenceCode
        );

        if ($entry !== null) {
            return [
                'fileName' => basename($logFile),

                'modifiedAt' => date(
                    'Y-m-d H:i:s',
                    (int) filemtime($logFile)
                ),

                'content' => $this->redactSensitiveLogData(
                    $entry
                ),
            ];
        }
    }

    return null;
}

private function extractMatchingLogEntry(
    string $logFile,
    string $referenceCode
): ?string {
    $handle = fopen($logFile, 'rb');

    if ($handle === false) {
        return null;
    }

    $matchedLines = [];
    $referenceFound = false;
    $lineCount = 0;
    $maximumLines = 200;

    /*
     * Search only for the secure-error entry containing the exact
     * reference code.
     */
    $exactSearchText =
        '[error][secure-error] Error reference: ' . $referenceCode;

    /*
     * Actual Yii log entry format:
     * 2026-07-11 14:44:11 [IP][USER][SESSION][error][category]
     */
    $newLogEntryPattern =
        '/^\d{4}-\d{2}-\d{2}\s+' .
        '\d{2}:\d{2}:\d{2}\s+' .
        '\[[^\]]*\]\[[^\]]*\]\[[^\]]*\]' .
        '\[[^\]]*\]\[[^\]]*\]/';

    try {
        while (($line = fgets($handle)) !== false) {
            $isNewLogEntry = preg_match(
                $newLogEntryPattern,
                $line
            ) === 1;

            if (!$referenceFound) {
                /*
                 * Start only when the exact secure-error entry is found.
                 */
                if (strpos($line, $exactSearchText) !== false) {
                    $referenceFound = true;
                    $matchedLines[] = $line;
                    $lineCount = 1;
                }

                continue;
            }

            /*
             * After finding the requested reference, stop when the next
             * Yii log entry begins.
             */
            if ($isNewLogEntry) {
                break;
            }

            $matchedLines[] = $line;
            $lineCount++;

            if ($lineCount >= $maximumLines) {
                $matchedLines[] =
                    "\n[Output truncated after {$maximumLines} lines]\n";

                break;
            }
        }
    } finally {
        fclose($handle);
    }

    if (!$referenceFound) {
        return null;
    }

    return trim(implode('', $matchedLines));
}

private function redactSensitiveLogData(string $content): string
{
    $patterns = [
        /*
         * Authorization: Bearer abc123
         */
        '/(Authorization\s*:\s*Bearer\s+)[^\s]+/i'
            => '$1[REDACTED]',

        /*
         * Cookie: PHPSESSID=...
         */
        '/(Cookie\s*:\s*).+/i'
            => '$1[REDACTED]',

        /*
         * Set-Cookie headers
         */
        '/(Set-Cookie\s*:\s*).+/i'
            => '$1[REDACTED]',

        /*
         * Common key=value formats
         */
        '/\b(password|passwd|pwd|token|access_token|' .
        'refresh_token|secret|api_key|session_id|' .
        'PHPSESSID|_csrf)\b(\s*[:=]\s*)' .
        '([^\s,;\]\}]+)/i'
            => '$1$2[REDACTED]',

        /*
         * Common JSON formats
         */
        '/("(?:password|passwd|pwd|token|access_token|' .
        'refresh_token|secret|api_key|session_id|' .
        'PHPSESSID|_csrf)"\s*:\s*")[^"]*"/i'
            => '$1[REDACTED]"',
    ];

    $redactedContent = preg_replace(
        array_keys($patterns),
        array_values($patterns),
        $content
    );

    return $redactedContent ?? $content;
}
}