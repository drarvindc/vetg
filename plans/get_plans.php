<?php
ob_start();
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
file_put_contents('../debug.log', '[' . date('Y-m-d H:i:s') . '] Starting plans/get_plans.php' . PHP_EOL, FILE_APPEND);

require '../db_connect.php';

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'TreatmentPlans'");
    if ($stmt->rowCount() === 0) {
        throw new Exception('TreatmentPlans table does not exist');
    }

    // Include NULL plan_id for treatments
    $stmt = $pdo->query("SELECT plan_id, plan_name, description FROM TreatmentPlans WHERE plan_id IS NOT NULL UNION SELECT NULL, 'Standalone Treatments', ''");
    $plans = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($plans as &$plan) {
        // Check if species_tags column exists
        $species_tags = $pdo->query("SHOW COLUMNS FROM PlanSteps LIKE 'species_tags'")->rowCount() > 0 ? 'species_tags' : "'All' AS species_tags";
        $stmt = $pdo->prepare("SELECT step_id, type, treatment_name, spacing_days, duration_months, $species_tags 
                               FROM PlanSteps WHERE plan_id <=> ?");
        $stmt->execute([$plan['plan_id']]);
        $plan['steps'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    file_put_contents('../debug.log', '[' . date('Y-m-d H:i:s') . '] Plans fetched: ' . count($plans) . PHP_EOL, FILE_APPEND);
    ob_end_clean();
    echo json_encode(['success' => true, 'plans' => $plans]);
} catch (Exception $e) {
    file_put_contents('../debug.log', '[' . date('Y-m-d H:i:s') . '] Error: ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>