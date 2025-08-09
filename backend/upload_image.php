<?php
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pet_id = $_POST['pet_id'] ?? '';
    $uploadDir = 'uploads/images/';
    if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);

    if (isset($_FILES['image']) && $pet_id) {
        $fileName = basename($_FILES['image']['name']);
        $targetPath = $uploadDir . time() . '_' . $fileName;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $stmt = $pdo->prepare("INSERT INTO Pet_Images (pet_id, image_path) VALUES (?, ?)");
            $stmt->execute([$pet_id, $targetPath]);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }
}
?>