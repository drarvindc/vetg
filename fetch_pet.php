<?php
ob_start();
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
file_put_contents('debug.log', '[' . date('Y-m-d H:i:s') . '] Starting fetch_pet.php' . PHP_EOL, FILE_APPEND);

require './db_connect.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $search_term = trim($_POST['search_term'] ?? '');
    $search_type = trim($_POST['search_type'] ?? '');

    file_put_contents('debug.log', '[' . date('Y-m-d H:i:s') . '] Search type: ' . $search_type . ', term: ' . $search_term . PHP_EOL, FILE_APPEND);

    if (empty($search_term) || empty($search_type)) {
        throw new Exception('Invalid search term or type');
    }

    $query = '';
    if ($search_type === 'unique_id') {
        $query = "SELECT p.pet_id, p.unique_id, p.pet_name, p.species, p.breed, p.gender, p.dob,
                         TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) AS pet_age_years,
                         TIMESTAMPDIFF(MONTH, p.dob, CURDATE()) % 12 AS pet_age_months,
                         TIMESTAMPDIFF(DAY, DATE_ADD(p.dob, INTERVAL TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) YEAR), CURDATE()) AS pet_age_days,
                         o.first_name, o.middle_name, o.last_name, o.locality,
                         GROUP_CONCAT(m.mobile_number) AS mobile_numbers,
                         p.qr_path, p.barcode_path
                  FROM Pets p
                  JOIN Owners o ON p.owner_id = o.owner_id
                  LEFT JOIN Mobile_Numbers m ON o.owner_id = m.owner_id
                  WHERE p.unique_id = ?
                  GROUP BY p.pet_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$search_term]);
    } elseif ($search_type === 'mobile') {
        $query = "SELECT p.pet_id, p.unique_id, p.pet_name, p.species, p.breed, p.gender, p.dob,
                         TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) AS pet_age_years,
                         TIMESTAMPDIFF(MONTH, p.dob, CURDATE()) % 12 AS pet_age_months,
                         TIMESTAMPDIFF(DAY, DATE_ADD(p.dob, INTERVAL TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) YEAR), CURDATE()) AS pet_age_days,
                         o.first_name, o.middle_name, o.last_name, o.locality,
                         GROUP_CONCAT(m.mobile_number) AS mobile_numbers,
                         p.qr_path, p.barcode_path
                  FROM Pets p
                  JOIN Owners o ON p.owner_id = o.owner_id
                  JOIN Mobile_Numbers m ON o.owner_id = m.owner_id
                  WHERE m.mobile_number = ?
                  GROUP BY p.pet_id";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$search_term]);
    } else {
        throw new Exception('Invalid search type');
    }

    $pet = $stmt->fetch(PDO::FETCH_ASSOC);
    ob_end_clean();
    if ($pet) {
        $pet['mobile_numbers'] = $pet['mobile_numbers'] ? explode(',', $pet['mobile_numbers']) : [];
        file_put_contents('debug.log', '[' . date('Y-m-d H:i:s') . '] Pet found: ' . json_encode($pet) . PHP_EOL, FILE_APPEND);
        echo json_encode(['success' => true, 'pet' => $pet]);
    } else {
        file_put_contents('debug.log', '[' . date('Y-m-d H:i:s') . '] Pet not found for search_term=' . $search_term . PHP_EOL, FILE_APPEND);
        echo json_encode(['success' => false, 'message' => 'Pet not found']);
    }
} catch (Exception $e) {
    file_put_contents('debug.log', '[' . date('Y-m-d H:i:s') . '] Error: ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>