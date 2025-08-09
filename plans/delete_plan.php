<?php
ob_start();
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
file_put_contents('../debug.log', '[' . date('Y-m-d H:i:s') . '] Starting plans/delete_plan.php' . PHP_EOL, FILE_APPEND);

require '../db_connect.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $plan_id = $_POST['plan_id'] ?? '';

    file_put_contents('../debug.log', '[' . date('Y-m-d H:i:s') . '] Deleting plan: plan_id=' . $plan_id . PHP_EOL, FILE_APPEND);

    if (empty($plan_id)) {
        throw new Exception('Plan ID required');
    }

    // Validate plan_id
    $stmt = $pdo->prepare("SELECT plan_id FROM TreatmentPlans WHERE plan_id = ?");
    $stmt->execute([$plan_id]);
    if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception('Plan not found for plan_id: ' . $plan_id);
    }

    // Begin transaction
    $pdo->beginTransaction();

    // Delete associated steps
    $stmt = $pdo->prepare("DELETE FROM PlanSteps WHERE plan_id = ?");
    $stmt->execute([$plan_id]);

    // Delete plan
    $stmt = $pdo->prepare("DELETE FROM TreatmentPlans WHERE plan_id = ?");
    $stmt->execute([$plan_id]);

    $pdo->commit();

    file_put_contents('../debug.log', '[' . date('Y-m-d H:i:s') . '] Plan deleted: plan_id=' . $plan_id . PHP_EOL, FILE_APPEND);
    ob_end_clean();
    echo json_encode(['success' => true, 'message' => 'Plan deleted successfully']);
} catch (Exception $e) {
    $pdo->rollBack();
    file_put_contents('../debug.log', '[' . date('Y-m-d H:i:s') . '] Error: ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>