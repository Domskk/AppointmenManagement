<?php
namespace App\Controllers;

use PDO;
use App\Models\Response;
use App\Middleware\AuthMiddleware;
class AdminController {

    // GET /api/admin/appointments
    public static function getAppointments(PDO $conn): void {
        AuthMiddleware::checkAdmin();

        $appointments = execute($conn, 'CALL getApptAdmin()');

        Response::success($appointments);
    }

    // GET /api/admin/slots
    public static function getSlots(PDO $conn): void {
        AuthMiddleware::checkAdmin();

        $slots = execute($conn, 'CALL getAllSlotsWithService()');

        Response::success($slots);
    }

}