<?php
ob_start();
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
file_put_contents('debug.log', '[' . date('Y-m-d H:i:s') . '] Starting generate_pdf.php' . PHP_EOL, FILE_APPEND);

require_once 'vendor/autoload.php';

use \TCPDF;

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $unique_id = $_POST['unique_id'] ?? '';
    $pet_name = $_POST['pet_name'] ?? '';
    $species = $_POST['species'] ?? '';
    $breed = $_POST['breed'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $age_years = (int)($_POST['age_years'] ?? 0);
    $age_months = (int)($_POST['age_months'] ?? 0);
    $owner_name = $_POST['owner_name'] ?? '';
    $mobile_numbers = $_POST['mobile_numbers'] ?? '';
    $visit_date = $_POST['visit_date'] ?? '';
    $barcode_path = $_POST['barcode_path'] ?? '';

    if (empty($unique_id)) {
        throw new Exception('Unique ID required');
    }

    file_put_contents('debug.log', '[' . date('Y-m-d H:i:s') . '] Generating PDF for: ' . $unique_id . PHP_EOL, FILE_APPEND);

    // Initialize TCPDF
    $pdf = new TCPDF('P', 'mm', 'A5', true, 'UTF-8', false);
    $pdf->SetCreator('Veterinary Clinic');
    $pdf->SetAuthor('Veterinary Clinic');
    $pdf->SetTitle('Prescription');
    $pdf->SetMargins(10, 10, 10);
    $pdf->SetAutoPageBreak(true, 10);
    $pdf->AddPage();

    // Set font
    $pdf->SetFont('helvetica', '', 12);

    // Add letterhead image
    $letterhead_path = 'uploads/letterhead.png';
    if (file_exists($letterhead_path)) {
        $pdf->Image($letterhead_path, 10, 10, 128, 0, '', '', '', false, 300);
        file_put_contents('debug.log', '[' . date('Y-m-d H:i:s') . '] Letterhead image added: ' . $letterhead_path . PHP_EOL, FILE_APPEND);
    } else {
        file_put_contents('debug.log', '[' . date('Y-m-d H:i:s') . '] Warning: Letterhead image not found: ' . $letterhead_path . PHP_EOL, FILE_APPEND);
    }

    // Position fields (adjust Y-coordinates to avoid overlapping letterhead)
    $y = 50; // Start below assumed letterhead height
    $pdf->SetXY(10, $y);
    $pdf->Write(0, 'Pet Name: ' . $pet_name);
    $y += 8;
    $pdf->SetXY(10, $y);
    $pdf->Write(0, 'Owner Name: ' . $owner_name);
    $y += 8;
    $pdf->SetXY(10, $y);
    $pdf->Write(0, 'Species: ' . $species);
    $y += 8;
    $pdf->SetXY(10, $y);
    $pdf->Write(0, 'Breed: ' . $breed);
    $y += 8;
    $pdf->SetXY(10, $y);
    $pdf->Write(0, 'Pet ID: ' . $unique_id);
    $y += 8;
    $pdf->SetXY(10, $y);
    $pdf->Write(0, 'Sex: ' . $gender);
    $y += 8;
    $pdf->SetXY(10, $y);
    $pdf->Write(0, 'Age: ' . $age_years . ' Yr ' . $age_months . ' mths');
    $y += 8;
    $pdf->SetXY(10, $y);
    $pdf->Write(0, 'Mobile: ' . $mobile_numbers);
    $y += 8;
    $pdf->SetXY(10, $y);
    $pdf->Write(0, 'Date: ' . $visit_date);
    $y += 15;

    // Add barcode
    if (file_exists($barcode_path)) {
        $pdf->Image($barcode_path, 24, $y, 100, 0, '', '', '', false, 300);
        file_put_contents('debug.log', '[' . date('Y-m-d H:i:s') . '] Barcode added: ' . $barcode_path . PHP_EOL, FILE_APPEND);
    } else {
        $pdf->write1DBarcode($unique_id, 'C128', 24, $y, 100, 10, 0.4, [], 'N');
        file_put_contents('debug.log', '[' . date('Y-m-d H:i:s') . '] Barcode generated directly: ' . $unique_id . PHP_EOL, FILE_APPEND);
    }
    $y += 20;

    // Add footer
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetXY(10, 190); // Position at bottom
    $pdf->Write(0, 'Shop 1, Popular Nagar Shopping Complex, Warje, Pune - 411058', '', false, 'C');

    // Output PDF
    ob_end_clean();
    $pdf->Output('prescription_' . $unique_id . '.pdf', 'D');
} catch (Exception $e) {
    file_put_contents('debug.log', '[' . date('Y-m-d H:i:s') . '] Error: ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
    ob_end_clean();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'PDF generation error: ' . $e->getMessage()]);
}
?>