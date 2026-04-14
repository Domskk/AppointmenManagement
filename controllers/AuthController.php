<?php
namespace App\Controllers;

use PDO;
use PDOException;
use Exception;
use App\Models\JWT;
use App\Models\Response;
use App\Models\Encryption;
class AuthController {

    // POST /api/auth/register
    public static function register(PDO $conn): void {
        $data = request_body();

        $name     = $data['name']     ?? '';
        $email    = $data['email']    ?? '';
        $password = $data['password'] ?? '';
        $phone    = $data['phone']    ?? null;
        $role     = $data['role']     ?? 'user';

        if (empty($name) || empty($email) || empty($password)) {
            Response::error('Name, email and password are required', 400);
            return;
        }

        $emailEncrypted = Encryption::encrypt($email);
        $phoneEncrypted = $phone ? Encryption::encrypt($phone) : null;
        $passwordHash   = password_hash($password, PASSWORD_DEFAULT);

        try {
            $result = execute($conn, 'CALL registerUser(?, ?, ?, ?, ?)', [
                $name, $emailEncrypted, $passwordHash, $phoneEncrypted, $role
            ], 'one');

            Response::success(['user_id' => $result['user_id'] ?? null], 'Registration successful');

        } catch (PDOException $e) {
            if (str_contains($e->getMessage(), 'Duplicate')) {
                Response::error('Email already exists', 409);
            } else {
                Response::error('Registration failed: ' . $e->getMessage(), 500);
            }
        }
    }

    // POST /api/auth/login
    public static function login(PDO $conn): void {
        $data = request_body();

        $email    = $data['email']    ?? '';
        $password = $data['password'] ?? '';

        if (empty($email) || empty($password)) {
            Response::error('Email and password are required', 400);
            return;
        }

        try {
            $users = execute($conn, 'CALL getAllUsers()');

            $foundUser = null;
            foreach ($users as $u) {
                if (Encryption::decrypt($u['email'] ?? '') === $email) {
                    $foundUser = $u;
                    break;
                }
            }

            if (!$foundUser || !password_verify($password, $foundUser['password'] ?? '')) {
                Response::error('Invalid email or password', 401);
                return;
            }

            $token = JWT::createToken([
                'id'    => $foundUser['id'],
                'name'  => $foundUser['name'],
                'email' => $email,
                'role'  => $foundUser['role'] ?? 'user'
            ]);

            Response::success([
                'token' => $token,
                'user'  => [
                    'id'    => $foundUser['id'],
                    'name'  => $foundUser['name'],
                    'email' => $email,
                    'role'  => $foundUser['role'] ?? 'user'
                ]
            ], 'Login successful');

        } catch (Exception $e) {
            Response::error('Login failed', 500);
        }
    }

}