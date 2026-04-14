<?php
class Response {

    public static function success(mixed $data = null, string $message = 'Success'): void {
        echo json_encode([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }

    public static function error(string $message, int $code = 400): void {
        http_response_code($code);
        echo json_encode([
            'success' => false,
            'message' => $message,
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }
}