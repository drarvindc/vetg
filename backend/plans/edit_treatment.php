<?php
ob_start();
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
file_put_contents('../debug.log', '[' . date('Y-m-d H:i:s') . '] Starting plans/edit_treatment.php' . PHP_EOL, FILE_APPEND);

require '../db_connect.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $step_id = $_POST['step_id'] ?? '';
    $type = $_POST['type'] ?? '';
    $treatment_name = $_POST['treatment_name'] ?? '';
    $duration_months = (int)($_POST['duration_months'] ?? null);
    $species_tags = isset($_POST['species_tags']) ? implode(',', (array)$_POST['species_tags']) : 'All';

    file_put_contents('../debug.log', '[' . date('Y-m-d H:i:s') . '] Editing treatment: step_id=' . $step_id . ', type=' . $type . ', treatment_name=' . $treatment_name . ', species_tags=' . $species_tags . PHP_EOL, FILE_APPEND);

    if (empty($step_id) || empty($type) || empty($treatment_name)) {
        throw new Exception('Step ID, type, and treatment name required');
    }

    // Validate step_id
    $stmt = $pdo->prepare("SELECT step_id FROM PlanSteps WHERE step_id = ? AND plan_id IS NULL");
    $stmt->execute([$step_id]);
    if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
        throw new Exception('Treatment not found for step_id: ' . $step_id);
    }

    // Update treatment
    $stmt = $pdo->prepare("UPDATE PlanSteps SET type = ?, treatment_name = ?, duration_months = ?, species_tags = ? 
                           WHERE step_id = ? AND plan_id IS NULL");
    $stmt->execute([$type, $treatment_name, $duration_months, $species_tags, $step_id]);

    file_put_contents('../debug.log', '[' . date('Y-m-d H:i:s') . '] Treatment updated: step_id=' . $step_id . PHP_EOL, FILE_APPEND);
    ob_end_clean();
    echo json_encode(['success' => true, 'message' => 'Treatment updated successfully']);
} catch (Exception $e) {
    file_put_contents('../debug.log', '[' . date('Y-m-d H:i:s') . '] Error: ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>