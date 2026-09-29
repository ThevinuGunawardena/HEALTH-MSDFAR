<?php

namespace backend\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\web\NotFoundHttpException;

final class SecurityHelper extends Component
{
    private const ENCRYPTION_CONTEXT = 'dfar-secure-record-id-v1';

    /**
     * Encrypt a model's numeric primary key into a URL-safe token.
     */
    public static function encryptId(
        string $modelClass,
        int|string $id
    ): string {
        $id = (string) $id;

        if ($id === '' || !ctype_digit($id)) {
            throw new InvalidConfigException(
                'A valid numeric record ID is required.'
            );
        }

        $payload = json_encode(
            [
                'version' => 1,
                'model' => $modelClass,
                'id' => $id,
            ],
            JSON_THROW_ON_ERROR
        );

        $encrypted = Yii::$app->security->encryptByKey(
            $payload,
            self::getEncryptionKey(),
            self::ENCRYPTION_CONTEXT
        );

        return self::base64UrlEncode($encrypted);
    }

    /**
     * Decrypt a URL token and return the original numeric ID.
     */
    public static function decryptId(
    string $token,
    string $expectedModelClass
): int {
    try {
        if (
            $token === ''
            || strlen($token) > 2048
            || preg_match('/^[A-Za-z0-9_-]+$/D', $token) !== 1
        ) {
            throw new \RuntimeException(
                'Invalid token format.'
            );
        }

        $encrypted = self::base64UrlDecode($token);

        $decrypted = Yii::$app->security->decryptByKey(
            $encrypted,
            self::getEncryptionKey(),
            self::ENCRYPTION_CONTEXT
        );

        if ($decrypted === false) {
            throw new \RuntimeException(
                'Token decryption failed.'
            );
        }

        $payload = json_decode(
            $decrypted,
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        if (!is_array($payload)) {
            throw new \RuntimeException(
                'The decrypted token payload is not an array.'
            );
        }

        if (($payload['version'] ?? null) !== 1) {
            throw new \RuntimeException(
                'Invalid token version.'
            );
        }

        if (
            !isset($payload['model'])
            || $payload['model'] !== $expectedModelClass
        ) {
            throw new \RuntimeException(
                'The token model does not match the expected model.'
            );
        }

        if (
            !isset($payload['id'])
            || !ctype_digit((string) $payload['id'])
        ) {
            throw new \RuntimeException(
                'The token does not contain a valid numeric ID.'
            );
        }

        $recordId = (int) $payload['id'];

        if ($recordId <= 0) {
            throw new \RuntimeException(
                'The decrypted record ID is invalid.'
            );
        }

        return $recordId;
    } catch (\Throwable $exception) {
        /*
         * Log only safe diagnostic information.
         *
         * Do not log:
         * - The full token
         * - The encryption key
         * - The decrypted payload
         */
        $encodedKey = Yii::$app->params['secureIdKey'] ?? null;

        $decodedKey = is_string($encodedKey)
            ? base64_decode($encodedKey, true)
            : false;

        Yii::error([
            'message' => $exception->getMessage(),
            'exceptionClass' => get_class($exception),
            'expectedModelClass' => $expectedModelClass,
            'tokenLength' => strlen($token),
            'tokenPrefix' => strlen($token) >= 8
                ? substr($token, 0, 8)
                : '[short-token]',
            'encodedKeyLength' => is_string($encodedKey)
                ? strlen($encodedKey)
                : 0,
            'decodedKeyLength' => is_string($decodedKey)
                ? strlen($decodedKey)
                : 0,
        ], 'secure-id-decryption');

        throw new NotFoundHttpException(
            'The requested record was not found.'
        );
    }
}
    private static function getEncryptionKey(): string
    {
        $encodedKey = Yii::$app->params['secureIdKey'] ?? null;

        if (!is_string($encodedKey) || $encodedKey === '') {
            throw new InvalidConfigException(
                'The secureIdKey parameter is not configured.'
            );
        }

        $key = base64_decode($encodedKey, true);

        if ($key === false || strlen($key) < 32) {
            throw new InvalidConfigException(
                'secureIdKey must be a Base64-encoded key '
                . 'containing at least 32 bytes.'
            );
        }

        return $key;
    }

    private static function base64UrlEncode(
        string $value
    ): string {
        return rtrim(
            strtr(
                base64_encode($value),
                '+/',
                '-_'
            ),
            '='
        );
    }

    private static function base64UrlDecode(
        string $value
    ): string {
        $remainder = strlen($value) % 4;

        if ($remainder !== 0) {
            $value .= str_repeat(
                '=',
                4 - $remainder
            );
        }

        $decoded = base64_decode(
            strtr(
                $value,
                '-_',
                '+/'
            ),
            true
        );

        if ($decoded === false) {
            throw new \RuntimeException(
                'Invalid token encoding.'
            );
        }

        return $decoded;
    }
}