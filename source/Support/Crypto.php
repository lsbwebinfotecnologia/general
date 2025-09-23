<?php

namespace Source\Support;

class Crypto
{
    private static function key(): string {
        $k = $_ENV['APP_KEY'] ?? getenv('APP_KEY') ?? '';
        if (strlen($k) < 32) { throw new \RuntimeException('APP_KEY ausente/curto'); }
        return hash('sha256', $k, true); // 32 bytes
    }

    public static function encrypt(string $plain): string {
        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $cipher = sodium_crypto_secretbox($plain, $nonce, self::key());
        return base64_encode($nonce . $cipher);
    }

    public static function decrypt(string $b64): string {
        $raw = base64_decode($b64, true);
        $nonce = substr($raw, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $cipher = substr($raw, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        return sodium_crypto_secretbox_open($cipher, $nonce, self::key());
    }
}