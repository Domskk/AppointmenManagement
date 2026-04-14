<?php

class ServiceController {

    // POST /api/services (Admin only)
    public static function create(PDO $conn): void {
        AuthMiddleware::checkAdmin();
        $data = request_body();

        $name     = $data['name']        ?? '';
        $desc     = $data['description'] ?? '';
        $duration = $data['duration']    ?? 0;

        if (empty($name) || empty($duration)) {
            Response::error('Service name and duration are required', 400);
            return;
        }

        $result = execute($conn, 'CALL insertService(?, ?, ?)', [$name, $desc, $duration], 'one');

        Response::success(['service_id' => $result['service_id'] ?? null], 'Service created successfully');
    }

    // GET /api/services
    public static function getAll(PDO $conn): void {
        Response::success(execute($conn, 'CALL getAllActiveService()'));
    }

}