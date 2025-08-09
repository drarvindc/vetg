<?php
ob_start();
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
file_put_contents('../debug.log', '[' . date('Y-m-d H:i:s') . '] Starting plans/delete_schedule.php' . PHP_EOL, FILE_APPEND);

require '../db_connect.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $schedule_id = $_POST['schedule_id'] ?? '';

    if (empty($schedule_id)) {
        throw new Exception('Schedule ID required');
    }

    file_put_contents('../debug.log', '[' . date('Y-m-d H:i:s') . '] Deleting schedule: ' . $schedule_id . PHP_EOL, FILE_APPEND);

    // Delete reminders
    $stmt = $pdo->prepare("DELETE FROM Reminders WHERE schedule_id = ?");
    $stmt->execute([$schedule_id]);

    // Delete schedule
    $stmt = $pdo->prepare("DELETE FROM Schedules WHERE schedule_id = ?");
    $stmt->execute([$schedule_id]);

    ob_end_clean();
    echo json_encode(['success' => true, 'message' => 'Schedule deleted successfully']);
} catch (Exception $e) {
    file_put_contents('../debug.log', '[' . date('Y-m-d H:i:s') . '] Error: ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>