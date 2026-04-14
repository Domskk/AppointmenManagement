<?php
namespace App\Controllers;

use PDO;
use App\Models\Response;
use App\Middleware\AuthMiddleware;
class ReportController {

    // GET /api/reports/wait-time
    public static function waitTime(PDO $conn): void {
        AuthMiddleware::checkAdmin();

        Response::success(execute($conn, 'CALL getReport()'));
    }

    // GET /api/reports/service-demand
    public static function serviceDemand(PDO $conn): void {
        AuthMiddleware::checkAdmin();

        Response::success(execute($conn, 'CALL getServiceReport()'));
    }

}