<?php
ob_start();
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
file_put_contents('debug.log', '[' . date('Y-m-d H:i:s') . '] Starting update_pet.php' . PHP_EOL, FILE_APPEND);

require 'db_connect.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $unique_id = $_POST['unique_id'] ?? '';
    $pet_name = $_POST['pet_name'] ?? '';
    $species = $_POST['species'] ?? '';
    $breed = $_POST['breed'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $dob = $_POST['dob'] ?? '';
    $age_years = (int)($_POST['age_years'] ?? 0);
    $age_months = (int)($_POST['age_months'] ?? 0);
    $age_days = (int)($_POST['age_days'] ?? 0);
    $first_name = $_POST['first_name'] ?? '';
    $middle_name = $_POST['middle_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $locality = $_POST['locality'] ?? '';
    $mobile_numbers = $_POST['mobile_numbers'] ?? [];

    if (empty($unique_id)) {
        throw new Exception('Unique ID required');
    }

    file_put_contents('debug.log', '[' . date('Y-m-d H:i:s') . '] Updating pet: ' . $unique_id . PHP_EOL, FILE_APPEND);

    // Calculate DOB from age if DOB is empty
    if (empty($dob) && ($age_years > 0 || $age_months > 0 || $age_days > 0)) {
        $today = new DateTime();
        $today->modify("-$age_years years -$age_months months -$age_days days");
        $dob = $today->format('Y-m-d');
    }

    // Find pet by unique_id
    $stmt = $pdo->prepare("SELECT pet_id, owner_id FROM Pets WHERE unique_id = ?");
    $stmt->execute([$unique_id]);
    $pet = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pet) {
        throw new Exception('Pet not found');
    }

    $pet_id = $pet['pet_id'];
    $owner_id = $pet['owner_id'];

    // Update pet details
    $stmt = $pdo->prepare("UPDATE Pets SET pet_name = ?, species = ?, breed = ?, gender = ?, dob = ? WHERE pet_id = ?");
    $stmt->execute([$pet_name, $species, $breed, $gender, $dob ?: null, $pet_id]);
    file_put_contents('debug.log', '[' . date('Y-m-d H:i:s') . '] Pet updated: ' . $pet_id . PHP_EOL, FILE_APPEND);

    // Update owner details
    $stmt = $pdo->prepare("UPDATE Owners SET first_name = ?, middle_name = ?, last_name = ?, locality = ? WHERE owner_id = ?");
    $stmt->execute([$first_name, $middle_name, $last_name, $locality, $owner_id]);
    file_put_contents('debug.log', '[' . date('Y-m-d H:i:s') . '] Owner updated: ' . $owner_id . PHP_EOL, FILE_APPEND);

    // Update mobile numbers
    $stmt = $pdo->prepare("DELETE FROM Mobile_Numbers WHERE owner_id = ?");
    $stmt->execute([$owner_id]);
    foreach ($mobile_numbers as $mobile) {
        if (!empty($mobile)) {
            $stmt = $pdo->prepare("INSERT INTO Mobile_Numbers (owner_id, mobile_number) VALUES (?, ?)");
            $stmt->execute([$owner_id, $mobile]);
        }
    }
    file_put_contents('debug.log', '[' . date('Y-m-d H:i:s') . '] Mobiles updated for owner: ' . $owner_id . PHP_EOL, FILE_APPEND);

    echo json_encode(['success' => true, 'message' => 'Pet and owner updated']);
} catch (Exception $e) {
    file_put_contents('debug.log', '[' . date('Y-m-d H:i:s') . '] Error: ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>