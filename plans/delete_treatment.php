<?php
ob_start();
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
file_put_contents('../debug.log', '[' . date('Y-m-d H:i:s') . '] Starting plans/delete_treatment.php' . PHP_EOL, FILE_APPEND);

require '../db_connect.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $step_id = $_POST['step_id'] ?? '';

    file_put_contents('../debug.log', '[' . date('Y-m-d H:i:s') . '] Deleting treatment: step_id=' . $step_id . PHP_EOL, FILE_APPEND);

    if (empty($step_id)) {
        throw new Exception('Step ID required');
    }

    // Validate step_id
    $stmt = $pdo->prepare("SELECT step_id FROM PlanSteps WHERE step_id = ? AND plan_id IS NULL");
    $stmt->execute([$step_id]);
    if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception('Treatment not found for step_id: ' . $step_id);
    }

    // Delete treatment
    $stmt = $pdo->prepare("DELETE FROM PlanSteps WHERE step_id = ? AND plan_id IS NULL");
    $stmt->execute([$step_id]);

    file_put_contents('../debug.log', '[' . date('Y-m-d H:i:s') . '] Treatment deleted: step_id=' . $step_id . PHP_EOL, FILE_APPEND);
    ob_end_clean();
    echo json_encode(['success' => true, 'message' => 'Treatment deleted successfully']);
} catch (Exception $e) {
    file_put_contents('../debug.log', '[' . date('Y-m-d H:i:s') . '] Error: ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>