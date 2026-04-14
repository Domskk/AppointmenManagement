<?php
namespace App\Controllers;

use PDO;
use App\Models\Response;
use App\Models\Encryption;
use App\Middleware\AuthMiddleware;
class AppointmentController {

    public static function create(PDO $conn): void {
        $user = AuthMiddleware::checkAuth();
        $data = request_body();

        $serviceId = $data['service_id'] ?? 0;
        $slotId    = $data['slot_id']    ?? 0;
        $notes     = $data['notes']      ?? '';

        if (empty($serviceId) || empty($slotId)) {
            Response::error('Service ID and Slot ID are required', 400);
            return;
        }

        $notesEncrypted = $notes ? Encryption::encrypt($notes) : null;

        // Check slot exists and has capacity
        $slot = execute($conn, 'CALL getSlotById(?)', [$slotId], 'one');

        if (!$slot) {
            Response::error('Slot not found', 404);
            return;
        }

        if (($slot['available_capacity'] ?? 0) < 1) {
            Response::error('Slot is full', 400);
            return;
        }

        // Get next queue number
        $count = execute($conn, 'CALL getApptCountBySlot(?)', [$slotId], 'one');
        $queue = ($count['total'] ?? 0) + 1;

        // Insert appointment
        $result = execute($conn, 'CALL insertAppointment(?, ?, ?, ?, ?, ?, ?)', [
            $user['id'],
            $serviceId,
            $slotId,
            $queue,
            $slot['slot_date'],
            $slot['slot_time'],
            $notesEncrypted
        ], 'one');

        execute($conn, 'CALL updateSlotCapacity(?)', [$slotId], 'none');

        Response::success([
            'appointment_id' => $result['appointment_id'] ?? null,
            'queue_number'   => $queue
        ], 'Appointment created successfully');
    }

    // GET /api/appointments/user/{user_id}
    public static function getByUser(PDO $conn, string $userId): void {
        AuthMiddleware::checkAuth();

        $appointments = execute($conn, 'CALL getUserAppt(?)', [$userId]);

        // Decrypt notes
        foreach ($appointments as &$appt) {
            if (!empty($appt['notes'])) {
                $appt['notes'] = Encryption::decrypt($appt['notes']);
            }
        }

        Response::success($appointments);
    }

}