<?php
ob_start();
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
file_put_contents('../debug.log', '[' . date('Y-m-d H:i:s') . '] Starting plans/add_plan.php' . PHP_EOL, FILE_APPEND);

require '../db_connect.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $plan_id = $_POST['plan_id'] ?? '';
    $plan_name = $_POST['plan_name'] ?? '';
    $description = $_POST['description'] ?? '';
    $steps = $_POST['steps'] ?? [];

    if (empty($plan_name) && $plan_id) {
        throw new Exception('Plan name required');
    }

    file_put_contents('../debug.log', '[' . date('Y-m-d H:i:s') . '] Processing plan: ' . ($plan_name ?: 'Standalone Treatment') . ', plan_id=' . ($plan_id ?: 'new') . PHP_EOL, FILE_APPEND);

    // Begin transaction
    $pdo->beginTransaction();

    if ($plan_id) {
        // Update existing plan
        $stmt = $pdo->prepare("UPDATE TreatmentPlans SET plan_name = ?, description = ? WHERE plan_id = ?");
        $stmt->execute([$plan_name, $description, $plan_id]);

        // Delete existing steps
        $stmt = $pdo->prepare("DELETE FROM PlanSteps WHERE plan_id = ?");
        $stmt->execute([$plan_id]);
    } elseif ($plan_name) {
        // Insert new plan
        $stmt = $pdo->prepare("INSERT INTO TreatmentPlans (plan_name, description) VALUES (?, ?)");
        $stmt->execute([$plan_name, $description]);
        $plan_id = $pdo->lastInsertId();
    }

    // Insert steps
    foreach ($steps as $step) {
        $type = $step['type'] ?? '';
        $treatment_name = $step['treatment_name'] ?? '';
        $spacing_days = (int)($step['spacing_days'] ?? 0);
        $duration_months = (int)($step['duration_months'] ?? null);
        $species_tags = isset($step['species_tags']) ? implode(',', (array)$step['species_tags']) : 'All';

        if (empty($type) || empty($treatment_name)) {
            continue;
        }

        $stmt = $pdo->prepare("INSERT INTO PlanSteps (plan_id, type, treatment_name, spacing_days, duration_months, species_tags) 
                               VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$plan_id ?: null, $type, $treatment_name, $spacing_days, $duration_months, $species_tags]);
    }

    $pdo->commit();
    file_put_contents('../debug.log', '[' . date('Y-m-d H:i:s') . '] Plan ' . ($plan_id ? 'updated' : 'added') . ': ' . ($plan_id ?: 'treatment') . PHP_EOL, FILE_APPEND);
    ob_end_clean();
    echo json_encode(['success' => true, 'message' => 'Plan ' . ($plan_id ? 'updated' : 'added') . ' successfully']);
} catch (Exception $e) {
    $pdo->rollBack();
    file_put_contents('../debug.log', '[' . date('Y-m-d H:i:s') . '] Error: ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>