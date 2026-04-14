<?php

class SlotController {

    // POST /api/slots (Admin only)
    public static function create(PDO $conn): void {
        AuthMiddleware::checkAdmin();
        $data = request_body();

        $serviceId = $data['service_id'] ?? 0;
        $slotDate  = $data['slot_date']  ?? '';
        $slotTime  = $data['slot_time']  ?? '';
        $capacity  = $data['capacity']   ?? 1;

        if (empty($serviceId) || empty($slotDate) || empty($slotTime)) {
            Response::error('Service ID, date and time are required', 400);
            return;
        }

        $result = execute($conn, 'CALL insertSlot(?, ?, ?, ?)', [
            $serviceId, $slotDate, $slotTime, $capacity
        ], 'one');

        Response::success(['slot_id' => $result['slot_id'] ?? null], 'Slot created successfully');
    }

}