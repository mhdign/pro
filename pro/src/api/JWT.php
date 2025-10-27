<?php
// src/api/JWT.php
namespace App\Api;

use Exception;

class JWT
{
    private string $secret;
    private string $algorithm;
    private int $expirationTime;

    public function __construct()
    {
        $config = require __DIR__ . '/../../config/app.php';
        $this->secret = $config['jwt']['secret'];
        $this->algorithm = $config['jwt']['algorithm'];
        $this->expirationTime = $this->parseExpirationTime($config['jwt']['expires_in']);
    }

    private function parseExpirationTime(string $timeString): int
    {
        $timeString = strtolower(trim($timeString));
        
        if (strpos($timeString, 'h') !== false) {
            return (int) $timeString * 3600;
        } elseif (strpos($timeString, 'd') !== false) {
            return (int) $timeString * 86400;
        } elseif (strpos($timeString, 'm') !== false) {
            return (int) $timeString * 60;
        } else {
            return (int) $timeString;
        }
    }

    public function encode(array $payload): string
    {
        $header = [
            'typ' => 'JWT',
            'alg' => $this->algorithm
        ];

        $payload['iat'] = time();
        $payload['exp'] = time() + $this->expirationTime;

        $headerEncoded = $this->base64UrlEncode(json_encode($header));
        $payloadEncoded = $this->base64UrlEncode(json_encode($payload));

        $signature = hash_hmac(
            'sha256',
            $headerEncoded . '.' . $payloadEncoded,
            $this->secret,
            true
        );

        $signatureEncoded = $this->base64UrlEncode($signature);

        return $headerEncoded . '.' . $payloadEncoded . '.' . $signatureEncoded;
    }

    public function decode(string $token): array
    {
        $parts = explode('.', $token);
        
        if (count($parts) !== 3) {
            throw new Exception('Invalid token format');
        }

        [$headerEncoded, $payloadEncoded, $signatureEncoded] = $parts;

        // Verify signature
        $signature = $this->base64UrlDecode($signatureEncoded);
        $expectedSignature = hash_hmac(
            'sha256',
            $headerEncoded . '.' . $payloadEncoded,
            $this->secret,
            true
        );

        if (!hash_equals($signature, $expectedSignature)) {
            throw new Exception('Invalid token signature');
        }

        $payload = json_decode($this->base64UrlDecode($payloadEncoded), true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid token payload');
        }

        // Check expiration
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            throw new Exception('Token has expired');
        }

        return $payload;
    }

    public function validate(string $token): bool
    {
        try {
            $this->decode($token);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getPayload(string $token): ?array
    {
        try {
            return $this->decode($token);
        } catch (Exception $e) {
            return null;
        }
    }

    public function refresh(string $token): string
    {
        $payload = $this->decode($token);
        
        // Remove expiration claims
        unset($payload['iat'], $payload['exp']);
        
        return $this->encode($payload);
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function base64UrlDecode(string $data): string
    {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }

    public function generateRefreshToken(array $payload): string
    {
        $config = require __DIR__ . '/../../config/app.php';
        $refreshExpirationTime = $this->parseExpirationTime($config['jwt']['refresh_expires_in']);
        
        $header = [
            'typ' => 'JWT',
            'alg' => $this->algorithm
        ];

        $payload['iat'] = time();
        $payload['exp'] = time() + $refreshExpirationTime;
        $payload['type'] = 'refresh';

        $headerEncoded = $this->base64UrlEncode(json_encode($header));
        $payloadEncoded = $this->base64UrlEncode(json_encode($payload));

        $signature = hash_hmac(
            'sha256',
            $headerEncoded . '.' . $payloadEncoded,
            $this->secret,
            true
        );

        $signatureEncoded = $this->base64UrlEncode($signature);

        return $headerEncoded . '.' . $payloadEncoded . '.' . $signatureEncoded;
    }
}
