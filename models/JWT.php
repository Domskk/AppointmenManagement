<?php
class JWT {

    public static function createToken(array $data): string {
        $header  = self::encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
        $payload = self::encode(json_encode(array_merge($data, [
            'iat' => time(),
            'exp' => time() + JWT_EXPIRY,
        ])));
        $signature = self::encode(
            hash_hmac('sha256', "$header.$payload", JWT_SECRET, true)
        );
        return "$header.$payload.$signature";
    }

    public static function verifyToken(string $token): ?array {
        $parts = explode('.', $token);
        if (count($parts) !== 3) return null;

        [$header, $payload, $sig] = $parts;

        $expected = self::encode(
            hash_hmac('sha256', "$header.$payload", JWT_SECRET, true)
        );
        if (!hash_equals($expected, $sig)) return null;

        $data = json_decode(self::decode($payload), true);
        if (!$data || empty($data['exp']) || $data['exp'] < time()) return null;

        return $data;
    }

    public static function getToken(): ?string {
        $headers = getallheaders();
        $auth    = $headers['Authorization'] ?? $headers['authorization'] ?? '';
        if (str_starts_with($auth, 'Bearer ')) {
            return trim(substr($auth, 7));
        }
        return null;
    }

    private static function encode(string $data): string {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function decode(string $data): string {
        $padded = str_pad(
            strtr($data, '-_', '+/'),
            strlen($data) + (4 - strlen($data) % 4) % 4,
            '='
        );
        return base64_decode($padded);
    }
}