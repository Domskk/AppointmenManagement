<?php
class AuthMiddleware {

    public static function checkAuth(): array {
        $token = JWT::getToken();

        if (!$token) {
            Response::error('No token provided', 401);
        }

        $user = JWT::verifyToken($token);

        if (!$user) {
            Response::error('Invalid or expired token', 401);
        }

        return $user;
    }

    public static function checkAdmin(): array {
        $user = self::checkAuth();

        if (($user['role'] ?? '') !== 'admin') {
            Response::error('Admin access required', 403);
        }

        return $user;
    }
}