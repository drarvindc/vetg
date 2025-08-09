<?php
ob_start();
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Picqer\Barcode\BarcodeGeneratorPNG;

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
file_put_contents('debug.log', '[' . date('Y-m-d H:i:s') . '] Starting add_pet.php' . PHP_EOL, FILE_APPEND);

require 'db_connect.php';
require 'generate_unique_id.php';

if (!file_exists('vendor/autoload.php')) {
    file_put_contents('debug.log', '[' . date('Y-m-d H:i:s') . '] Error: vendor/autoload.php not found' . PHP_EOL, FILE_APPEND);
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Composer autoload not found']);
    exit;
}
require 'vendor/autoload.php';

if (!extension_loaded('gd')) {
    file_put_contents('debug.log', '[' . date('Y-m-d H:i:s') . '] Error: GD extension not loaded' . PHP_EOL, FILE_APPEND);
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'GD extension required']);
    exit;
}

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        throw new Exception('Invalid JSON input');
    }

    $mobile = $data['mobile'] ?? '';
    if (empty($mobile)) {
        throw new Exception('Mobile number required');
    }

    file_put_contents('debug.log', '[' . date('Y-m-d H:i:s') . '] Mobile: ' . $mobile . PHP_EOL, FILE_APPEND);

    $stmt = $pdo->prepare("SELECT o.owner_id, o.first_name, o.middle_name, o.last_name, o.locality 
                           FROM Owners o 
                           JOIN Mobile_Numbers m ON o.owner_id = m.owner_id 
                           WHERE m.mobile_number = ?");
    $stmt->execute([$mobile]);
    $owner = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$owner) {
        $stmt = $pdo->prepare("INSERT INTO Owners (first_name, last_name, locality) VALUES (?, ?, ?)");
        $stmt->execute(['', '', '']);
        $owner_id = $pdo->lastInsertId();
        $stmt = $pdo->prepare("INSERT INTO Mobile_Numbers (owner_id, mobile_number) VALUES (?, ?)");
        $stmt->execute([$owner_id, $mobile]);
        $owner_name = '';
        $owner_first_name = '';
        $owner_middle_name = '';
        $owner_last_name = '';
        $owner_locality = '';
        file_put_contents('debug.log', '[' . date('Y-m-d H:i:s') . '] New owner created: ' . $owner_id . PHP_EOL, FILE_APPEND);
    } else {
        $owner_id = $owner['owner_id'];
        $owner_first_name = $owner['first_name'] ?? '';
        $owner_middle_name = $owner['middle_name'] ?? '';
        $owner_last_name = $owner['last_name'] ?? '';
        $owner_locality = $owner['locality'] ?? '';
        $owner_name = trim($owner_first_name . ' ' . $owner_middle_name . ' ' . $owner_last_name);
        file_put_contents('debug.log', '[' . date('Y-m-d H:i:s') . '] Existing owner: ' . $owner_id . PHP_EOL, FILE_APPEND);
    }

    $stmt = $pdo->prepare("SELECT mobile_number FROM Mobile_Numbers WHERE owner_id = ?");
    $stmt->execute([$owner_id]);
    $mobile_numbers = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $stmt = $pdo->prepare("SELECT pet_id, unique_id, pet_name, species, breed, gender, dob, DATEDIFF(CURDATE(), dob) AS age_days 
                           FROM Pets 
                           WHERE owner_id = ? LIMIT 1");
    $stmt->execute([$owner_id]);
    $pet = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($pet) {
        $unique_id = $pet['unique_id'];
        $pet_name = $pet['pet_name'] ?? '';
        $species = $pet['species'] ?? '';
        $breed = $pet['breed'] ?? '';
        $gender = $pet['gender'] ?? '';
        $dob = $pet['dob'] ?? '';
        $pet_age_days = $pet['age_days'] ?? 0;
        file_put_contents('debug.log', '[' . date('Y-m-d H:i:s') . '] Existing pet: ' . $unique_id . PHP_EOL, FILE_APPEND);
    } else {
        $unique_id = generateUniqueID($pdo);
        $pet_name = '';
        $species = 'Canine';
        $breed = '';
        $gender = 'Male';
        $dob = '';
        $pet_age_days = 0;
        $stmt = $pdo->prepare("INSERT INTO Pets (owner_id, unique_id, pet_name, species, breed, gender, dob) 
                               VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$owner_id, $unique_id, $pet_name, $species, $breed, $gender, $dob ?: null]);
        file_put_contents('debug.log', '[' . date('Y-m-d H:i:s') . '] New pet created: ' . $unique_id . PHP_EOL, FILE_APPEND);
    }

    $pet_age_years = floor($pet_age_days / 365);
    $pet_age_remaining_days = $pet_age_days % 365;
    $pet_age_months = floor($pet_age_remaining_days / 30);
    $pet_age_days = $pet_age_remaining_days % 30;

    $qr_path = 'uploads/qr/' . $unique_id . '.png';
    if (!file_exists('uploads/qr/')) {
        if (!mkdir('uploads/qr/', 0755, true)) {
            file_put_contents('debug.log', '[' . date('Y-m-d H:i:s') . '] Error: Failed to create QR directory' . PHP_EOL, FILE_APPEND);
            $qr_path = '';
        }
    }
    if ($qr_path) {
        try {
            $qr = QrCode::create($unique_id)->setSize(300);
            $writer = new PngWriter();
            $result = $writer->write($qr);
            if (!file_put_contents($qr_path, $result->getString())) {
                throw new Exception('Failed to save QR code');
            }
            chmod($qr_path, 0644);
            file_put_contents('debug.log', '[' . date('Y-m-d H:i:s') . '] QR generated: ' . $qr_path . PHP_EOL, FILE_APPEND);
        } catch (Exception $e) {
            file_put_contents('debug.log', '[' . date('Y-m-d H:i:s') . '] QR generation failed: ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
            $qr_path = '';
        }
    }

    $barcode_path = 'uploads/barcode/' . $unique_id . '.png';
    if (!file_exists('uploads/barcode/')) {
        if (!mkdir('uploads/barcode/', 0755, true)) {
            file_put_contents('debug.log', '[' . date('Y-m-d H:i:s') . '] Error: Failed to create barcode directory' . PHP_EOL, FILE_APPEND);
            $barcode_path = '';
        }
    }
    if ($barcode_path) {
        try {
            $generator = new BarcodeGeneratorPNG();
            $barcode_data = $generator->getBarcode($unique_id, $generator::TYPE_CODE_128, 2, 50);
            if (!file_put_contents($barcode_path, $barcode_data)) {
                throw new Exception('Failed to save barcode');
            }
            chmod($barcode_path, 0644);
            file_put_contents('debug.log', '[' . date('Y-m-d H:i:s') . '] Barcode generated: ' . $qr_path . PHP_EOL, FILE_APPEND);
        } catch (Exception $e) {
            file_put_contents('debug.log', '[' . date('Y-m-d H:i:s') . '] Barcode generation failed: ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
            $barcode_path = '';
        }
    }

    $response = [
        'success' => true,
        'unique_id' => $unique_id,
        'qr_path' => file_exists($qr_path) ? $qr_path : '',
        'barcode_path' => file_exists($barcode_path) ? $barcode_path : '',
        'pet_name' => $pet_name,
        'species' => $species,
        'breed' => $breed,
        'gender' => $gender,
        'dob' => $dob,
        'pet_age_years' => $pet_age_years,
        'pet_age_months' => $pet_age_months,
        'pet_age_days' => $pet_age_days,
        'owner_name' => $owner_name,
        'owner_first_name' => $owner_first_name,
        'owner_middle_name' => $owner_middle_name,
        'owner_last_name' => $owner_last_name,
        'owner_locality' => $owner_locality,
        'mobile_numbers' => $mobile_numbers
    ];
    file_put_contents('debug.log', '[' . date('Y-m-d H:i:s') . '] Response: ' . json_encode($response) . PHP_EOL, FILE_APPEND);

    ob_end_clean();
    echo json_encode($response);
} catch (Exception $e) {
    file_put_contents('debug.log', '[' . date('Y-m-d H:i:s') . '] Error: ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>