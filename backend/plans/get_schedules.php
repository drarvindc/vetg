<?php
ob_start();
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
file_put_contents('../debug.log', '[' . date('Y-m-d H:i:s') . '] Starting plans/get_schedules.php' . PHP_EOL, FILE_APPEND);

require '../db_connect.php';

header('Content-Type: application/json');

try {
    $pet_id = $_POST['pet_id'] ?? '';

    if (empty($pet_id)) {
        throw new Exception('Pet ID required');
    }

    // Check if next_due column exists
    $columns = $pdo->query("SHOW COLUMNS FROM Schedules LIKE 'next_due'")->rowCount() > 0 ? 'next_due' : 'NULL AS next_due';

    $stmt = $pdo->prepare("SELECT schedule_id, type, treatment_name, date_administered, $columns, notes 
                           FROM Schedules WHERE pet_id = ?");
    $stmt->execute([$pet_id]);
    $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);

    file_put_contents('../debug.log', '[' . date('Y-m-d H:i:s') . '] Schedules fetched for pet: ' . $pet_id . PHP_EOL, FILE_APPEND);
    ob_end_clean();
    echo json_encode(['success' => true, 'schedules' => $schedules]);
} catch (Exception $e) {
    file_put_contents('../debug.log', '[' . date('Y-m-d H:i:s') . '] Error: ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>