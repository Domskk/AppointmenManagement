<?php
class Encryption {

    private const METHOD  = 'aes-256-gcm';
    private const IV_LEN  = 12;
    private const TAG_LEN = 16;

    public static function encrypt(string $text): ?string {
        if ($text === '') return null;

        $iv  = random_bytes(self::IV_LEN);
        $tag = '';

        $cipher = openssl_encrypt(
            $text, self::METHOD, ENC_KEY,
            OPENSSL_RAW_DATA, $iv, $tag, '', self::TAG_LEN
        );

        if ($cipher === false) return null;

        // Layout: [ IV 12 bytes ][ ciphertext ][ tag 16 bytes ]
        return base64_encode($iv . $cipher . $tag);
    }

    public static function decrypt(string $encrypted): ?string {
        if ($encrypted === '') return null;

        $raw = base64_decode($encrypted, true);

        if ($raw === false || strlen($raw) < self::IV_LEN + 1 + self::TAG_LEN) {
            return null;
        }

        $iv     = substr($raw, 0, self::IV_LEN);
        $tag    = substr($raw, -self::TAG_LEN);
        $cipher = substr($raw, self::IV_LEN, -self::TAG_LEN);

        $plain = openssl_decrypt(
            $cipher, self::METHOD, ENC_KEY,
            OPENSSL_RAW_DATA, $iv, $tag
        );

        return $plain === false ? null : $plain;
    }
}