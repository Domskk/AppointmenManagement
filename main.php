<?php

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// config
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/cors.php';
require_once __DIR__ . '/config/database.php';

// models
require_once __DIR__ . '/models/Response.php';
require_once __DIR__ . '/models/JWT.php';
require_once __DIR__ . '/models/Encryption.php';

// middleware
require_once __DIR__ . '/middleware/AuthMiddleware.php';

// controllers
require_once __DIR__ . '/controllers/UserController.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/ServiceController.php';
require_once __DIR__ . '/controllers/SlotController.php';
require_once __DIR__ . '/controllers/AppointmentController.php';
require_once __DIR__ . '/controllers/AdminController.php';
require_once __DIR__ . '/controllers/ReportController.php';

// routes
require_once __DIR__ . '/routes/api.php';