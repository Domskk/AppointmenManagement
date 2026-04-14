<?php

$conn = (new Connection())->connect();
$method = $_SERVER['REQUEST_METHOD'];
$request_path = trim($_REQUEST['request'] ?? '', '/');
$parts = explode('/', $request_path);

// Remove 'api' prefix if present
if (!empty($parts) && $parts[0] === 'api') {
    array_shift($parts);
}

$resource = $parts[0] ?? '';
$sub      = $parts[1] ?? '';
$id       = $parts[2] ?? null;

switch ($resource) {

    // ====================== AUTH ======================
    case 'auth':
        if ($method === 'POST') {
            $data = json_decode(file_get_contents("php://input"), true) ?? [];

            if ($sub === 'register') {
                AuthController::register($conn);
            } elseif ($sub === 'login') {
                AuthController::login($conn);
            } else {
                Response::error('Endpoint not found', 404);
            }
        } else {
            Response::error('Method not allowed', 405);
        }
        break;

    // ====================== USERS ======================
    case 'users':
        if ($sub === 'profile') {
            if ($method === 'GET') {
                UserController::getProfile($conn);
            } elseif ($method === 'PUT') {
                UserController::updateProfile($conn);
            } else {
                Response::error('Method not allowed', 405);
            }
        } else {
            Response::error('Endpoint not found', 404);
        }
        break;

    // ====================== SERVICES ======================
    case 'services':
        if ($method === 'GET') {
            ServiceController::getAll($conn);
        } elseif ($method === 'POST') {
            ServiceController::create($conn);
        } else {
            Response::error('Method not allowed', 405);
        }
        break;

    // ====================== SLOTS ======================
    case 'slots':
        if ($method === 'POST') {
            SlotController::create($conn);
        } else {
            Response::error('Method not allowed', 405);
        }
        break;

    // ====================== APPOINTMENTS ======================
    case 'appointments':
        if ($method === 'POST') {
            AppointmentController::create($conn);
        } elseif ($method === 'GET' && $sub === 'user' && $id !== null) {
            AppointmentController::getByUser($conn, (string)$id);
        } else {
            Response::error('Endpoint not found or method not allowed', 404);
        }
        break;

    // ====================== ADMIN ======================
    case 'admin':
        if ($method === 'GET') {
            if ($sub === 'appointments') {
                AdminController::getAppointments($conn);
            } elseif ($sub === 'slots') {
                AdminController::getSlots($conn);
            } else {
                Response::error('Endpoint not found', 404);
            }
        } else {
            Response::error('Method not allowed', 405);
        }
        break;

    // ====================== REPORTS ======================
    case 'reports':
        if ($method === 'GET') {
            if ($sub === 'wait-time') {
                ReportController::waitTime($conn);
            } elseif ($sub === 'service-demand') {
                ReportController::serviceDemand($conn);
            } else {
                Response::error('Endpoint not found', 404);
            }
        } else {
            Response::error('Method not allowed', 405);
        }
        break;

    // ====================== DEFAULT ======================
    default:
        Response::error('Endpoint not found', 404);
        break;
}
