<?php
ob_start();
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
file_put_contents('../debug.log', '[' . date('Y-m-d H:i:s') . '] Starting plans/record_schedule.php' . PHP_EOL, FILE_APPEND);

require '../db_connect.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $schedule_id = $_POST['schedule_id'] ?? '';
    $date_administered = $_POST['date_administered'] ?? '';

    file_put_contents('../debug.log', '[' . date('Y-m-d H:i:s') . '] Recording schedule: schedule_id=' . $schedule_id . ', date_administered=' . $date_administered . PHP_EOL, FILE_APPEND);

    if (empty($schedule_id) || empty($date_administered)) {
        throw new Exception('Schedule ID and date administered required');
    }

    // Begin transaction
    $pdo->beginTransaction();

    // Fetch original schedule details
    $stmt = $pdo->prepare("SELECT pet_id, plan_id, type, treatment_name, notes FROM Schedules WHERE schedule_id = ?");
    $stmt->execute([$schedule_id]);
    $schedule = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$schedule) {
        throw new Exception('Schedule not found for schedule_id: ' . $schedule_id);
    }

    // Fetch duration_months from PlanSteps, matching plan_id and spacing_days
    $stmt = $pdo->prepare("SELECT duration_months, spacing_days FROM PlanSteps 
                           WHERE (plan_id = ? OR (? IS NULL AND plan_id IS NULL)) 
                           AND type = ? AND treatment_name = ? 
                           ORDER BY CASE WHEN plan_id = ? THEN 0 ELSE 1 END, 
                                    CASE WHEN treatment_name = ? THEN 0 ELSE 1 END, 
                                    spacing_days DESC LIMIT 1");
    $stmt->execute([$schedule['plan_id'], $schedule['plan_id'], $schedule['type'], $schedule['treatment_name'], $schedule['plan_id'], $schedule['treatment_name']]);
    $treatment = $stmt->fetch(PDO::FETCH_ASSOC);
    $duration_months = $treatment['duration_months'] ?? null;

    file_put_contents('../debug.log', '[' . date('Y-m-d H:i:s') . '] Fetched duration_months: ' . ($duration_months ?: 'NULL') . ', spacing_days: ' . ($treatment['spacing_days'] ?: 'NULL') . ' for plan_id=' . ($schedule['plan_id'] ?: 'NULL') . ', type=' . $schedule['type'] . ', treatment_name=' . $schedule['treatment_name'] . PHP_EOL, FILE_APPEND);

    // Update original schedule
    $stmt = $pdo->prepare("UPDATE Schedules SET date_administered = ?, next_due = NULL WHERE schedule_id = ?");
    $stmt->execute([$date_administered, $schedule_id]);
    file_put_contents('../debug.log', '[' . date('Y-m-d H:i:s') . '] Updated original schedule: ' . $schedule_id . ' with administered ' . $date_administered . PHP_EOL, FILE_APPEND);

    // Create new renewal if duration_months is set
    if ($duration_months) {
        $next_due = new DateTime($date_administered);
        $next_due->modify("+{$duration_months} months");
        $next_due_date = $next_due->format('Y-m-d');

        $stmt = $pdo->prepare("INSERT INTO Schedules (pet_id, plan_id, type, treatment_name, date_administered, next_due, notes) 
                               VALUES (?, ?, ?, ?, NULL, ?, ?)");
        $stmt->execute([$schedule['pet_id'], $schedule['plan_id'], $schedule['type'], $schedule['treatment_name'], $next_due_date, $schedule['notes']]);
        $new_schedule_id = $pdo->lastInsertId();
        file_put_contents('../debug.log', '[' . date('Y-m-d H:i:s') . '] Created new renewal: ' . $new_schedule_id . ' with next_due ' . $next_due_date . PHP_EOL, FILE_APPEND);

        // Insert new reminder
        $stmt = $pdo->prepare("INSERT INTO Reminders (schedule_id, due_date, method) VALUES (?, ?, 'sms')");
        $stmt->execute([$new_schedule_id, $next_due_date]);
    }

    $pdo->commit();

    file_put_contents('../debug.log', '[' . date('Y-m-d H:i:s') . '] Schedule recorded: ' . $schedule_id . PHP_EOL, FILE_APPEND);
    ob_end_clean();
    echo json_encode(['success' => true, 'message' => 'Schedule recorded successfully']);
} catch (Exception $e) {
    $pdo->rollBack();
    file_put_contents('../debug.log', '[' . date('Y-m-d H:i:s') . '] Error: ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>