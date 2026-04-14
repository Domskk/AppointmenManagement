<?php
namespace App\Controllers;

use PDO;
use App\Models\Response;
use App\Models\Encryption;
use App\Middleware\AuthMiddleware;
class UserController {

    // GET /api/users/profile
    public static function getProfile(PDO $conn): void {
        $user = AuthMiddleware::checkAuth();

        $data = execute($conn, 'CALL getUserById(?)', [$user['id']], 'one');

        if (!$data) {
            Response::error('User not found', 404);
            return;
        }

        if (!empty($data['email'])) $data['email'] = Encryption::decrypt($data['email']);
        if (!empty($data['phone'])) $data['phone'] = Encryption::decrypt($data['phone']);

        unset($data['password']);

        Response::success($data, 'Profile retrieved successfully');
    }

    // PUT /api/users/profile
    public static function updateProfile(PDO $conn): void {
        $user = AuthMiddleware::checkAuth();
        $data = request_body();

        $name           = $data['name']  ?? null;
        $phone          = $data['phone'] ?? null;
        $phoneEncrypted = $phone ? Encryption::encrypt($phone) : null;

        execute($conn, 'CALL updateUser(?, ?, ?)', [$name, $phoneEncrypted, $user['id']], 'none');

        Response::success([], 'Profile updated successfully');
    }

}