<?php
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $setting_id = $data['setting_id'];
    $is_required = $data['is_required'] ? 1 : 0;
    $field_options = $data['field_options'] ?? '';

    $stmt = $pdo->prepare("UPDATE Settings SET is_required = ?, field_options = ? WHERE setting_id = ?");
    $stmt->execute([$is_required, $field_options, $setting_id]);
    echo json_encode(['success' => true]);
}
?>