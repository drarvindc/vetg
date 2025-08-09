<?php
ob_start();
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
file_put_contents('../debug.log', '[' . date('Y-m-d H:i:s') . '] Starting plans/add_treatment.php' . PHP_EOL, FILE_APPEND);

require '../db_connect.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Handle Treatment Maker (no pet_id) or Add Treatment (with pet_id)
    $pet_id = $_POST['pet_id'] ?? null;
    $treatment_id = $_POST['treatment_id'] ?? null;
    $type = $_POST['type'] ?? '';
    $treatment_name = $_POST['treatment_name'] ?? '';
    $duration_months = (int)($_POST['duration_months'] ?? null);
    $species_tags = isset($_POST['species_tags']) ? implode(',', (array)$_POST['species_tags']) : 'All';
    $notes = $_POST['notes'] ?? '';

    file_put_contents('../debug.log', '[' . date('Y-m-d H:i:s') . '] Adding treatment: pet_id=' . ($pet_id ?: 'none') . ', treatment_id=' . ($treatment_id ?: 'none') . ', type=' . $type . ', treatment_name=' . $treatment_name . ', species_tags=' . $species_tags . PHP_EOL, FILE_APPEND);

    if ($pet_id && empty($treatment_id)) {
        throw new Exception('Treatment ID required for applying treatment');
    }
    if (!$pet_id && (empty($type) || empty($treatment_name))) {
        throw new Exception('Type and treatment name required for Treatment Maker');
    }

    // Begin transaction
    $pdo->beginTransaction();

    if ($pet_id) {
        // Validate pet for Add Treatment
        $stmt = $pdo->prepare("SELECT pet_id, species FROM Pets WHERE pet_id = ?");
        $stmt->execute([$pet_id]);
        $pet = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$pet) {
            throw new Exception('Pet not found for pet_id: ' . $pet_id);
        }

        // Fetch treatment details
        $stmt = $pdo->prepare("SELECT type, treatment_name, duration_months, species_tags 
                               FROM PlanSteps WHERE step_id = ? AND plan_id IS NULL");
        $stmt->execute([$treatment_id]);
        $treatment = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$treatment) {
            throw new Exception('Treatment not found for treatment_id: ' . $treatment_id);
        }

        if (empty($treatment['treatment_name'])) {
            throw new Exception('Invalid treatment: empty name');
        }

        // Check species compatibility
        $tags = explode(',', $treatment['species_tags']);
        if (!in_array('All', $tags) && !in_array($pet['species'], $tags)) {
            throw new Exception('Treatment species tags do not match pet species');
        }

        // Calculate next_due based on today
        $next_due = null;
        if ($treatment['duration_months']) {
            $next_due_date = new DateTime();
            $next_due_date->modify("+{$treatment['duration_months']} months");
            $next_due = $next_due_date->format('Y-m-d');
        }

        // Insert into Schedules with date_administered = NULL
        $stmt = $pdo->prepare("INSERT INTO Schedules (pet_id, plan_id, type, treatment_name, date_administered, next_due, notes) 
                               VALUES (?, NULL, ?, ?, NULL, ?, ?)");
        $stmt->execute([$pet_id, $treatment['type'], $treatment['treatment_name'], $next_due, $notes]);

        $schedule_id = $pdo->lastInsertId();

        // Insert reminder if next_due is set
        if ($next_due) {
            $stmt = $pdo->prepare("INSERT INTO Reminders (schedule_id, due_date, method) VALUES (?, ?, 'sms')");
            $stmt->execute([$schedule_id, $next_due]);
        }
    } else {
        // For Treatment Maker, insert into PlanSteps with plan_id = NULL
        $stmt = $pdo->prepare("INSERT INTO PlanSteps (plan_id, type, treatment_name, spacing_days, duration_months, species_tags) 
                               VALUES (NULL, ?, ?, 0, ?, ?)");
        $stmt->execute([$type, $treatment_name, $duration_months, $species_tags]);
    }

    $pdo->commit();
    file_put_contents('../debug.log', '[' . date('Y-m-d H:i:s') . '] Treatment added' . ($pet_id ? ': schedule_id=' . $schedule_id : '') . PHP_EOL, FILE_APPEND);
    ob_end_clean();
    echo json_encode(['success' => true, 'message' => 'Treatment ' . ($pet_id ? 'applied' : 'added') . ' successfully']);
} catch (Exception $e) {
    $pdo->rollBack();
    file_put_contents('../debug.log', '[' . date('Y-m-d H:i:s') . '] Error: ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>