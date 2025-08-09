<?php
ob_start();
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
file_put_contents('../debug.log', '[' . date('Y-m-d H:i:s') . '] Starting plans/apply_plan.php' . PHP_EOL, FILE_APPEND);

require '../db_connect.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $pet_id = $_POST['pet_id'] ?? '';
    $plan_id = $_POST['plan_id'] ?? '';
    $start_date = $_POST['start_date'] ?? '';

    file_put_contents('../debug.log', '[' . date('Y-m-d H:i:s') . '] Applying plan: pet_id=' . $pet_id . ', plan_id=' . $plan_id . ', start_date=' . $start_date . PHP_EOL, FILE_APPEND);

    if (empty($pet_id) || empty($plan_id) || empty($start_date)) {
        throw new Exception('Pet ID, plan ID, and start date required');
    }

    // Validate pet
    $stmt = $pdo->prepare("SELECT pet_id, species FROM Pets WHERE pet_id = ?");
    $stmt->execute([$pet_id]);
    $pet = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$pet) {
        throw new Exception('Pet not found for pet_id: ' . $pet_id);
    }

    // Fetch plan steps
    $stmt = $pdo->prepare("SELECT type, treatment_name, spacing_days, duration_months, species_tags 
                           FROM PlanSteps WHERE plan_id = ? ORDER BY spacing_days");
    $stmt->execute([$plan_id]);
    $steps = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($steps)) {
        throw new Exception('No steps found for plan_id: ' . $plan_id);
    }

    // Begin transaction
    $pdo->beginTransaction();

    foreach ($steps as $step) {
        // Check species compatibility
        $tags = explode(',', $step['species_tags']);
        if (!in_array('All', $tags) && !in_array($pet['species'], $tags)) {
            continue; // Skip incompatible steps
        }

        // Calculate next_due
        $base_date = new DateTime($start_date);
        $base_date->modify("+{$step['spacing_days']} days");
        $next_due = $base_date->format('Y-m-d');
        if ($step['duration_months']) {
            $next_due_date = clone $base_date;
            $next_due_date->modify("+{$step['duration_months']} months");
            $next_due = $next_due_date->format('Y-m-d');
        }

        // Insert into Schedules with date_administered = NULL
        $stmt = $pdo->prepare("INSERT INTO Schedules (pet_id, plan_id, type, treatment_name, date_administered, next_due, notes) 
                               VALUES (?, ?, ?, ?, NULL, ?, '')");
        $stmt->execute([$pet_id, $plan_id, $step['type'], $step['treatment_name'], $next_due]);

        $schedule_id = $pdo->lastInsertId();
        file_put_contents('../debug.log', '[' . date('Y-m-d H:i:s') . '] Schedule added: schedule_id=' . $schedule_id . ', treatment=' . $step['treatment_name'] . ', date_administered=NULL, next_due=' . ($next_due ?: 'NULL') . PHP_EOL, FILE_APPEND);

        // Insert reminder if next_due is set
        if ($next_due) {
            $stmt = $pdo->prepare("INSERT INTO Reminders (schedule_id, due_date, method) VALUES (?, ?, 'sms')");
            $stmt->execute([$schedule_id, $next_due]);
        }
    }

    $pdo->commit();
    file_put_contents('../debug.log', '[' . date('Y-m-d H:i:s') . '] Plan applied successfully for pet_id=' . $pet_id . PHP_EOL, FILE_APPEND);
    ob_end_clean();
    echo json_encode(['success' => true, 'message' => 'Plan applied successfully']);
} catch (Exception $e) {
    $pdo->rollBack();
    file_put_contents('../debug.log', '[' . date('Y-m-d H:i:s') . '] Error: ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>