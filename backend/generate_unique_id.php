<?php
require 'db_connect.php';

function generateUniqueID($pdo) {
    $yearPrefix = date('y');
    $stmt = $pdo->prepare("SELECT MAX(CAST(SUBSTR(unique_id, 3) AS UNSIGNED)) AS max_id FROM Pets WHERE unique_id LIKE ?");
    $stmt->execute(["$yearPrefix%"]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $nextID = ($result['max_id'] ?? 0) + 1;
    return $yearPrefix . str_pad($nextID, 4, '0', STR_PAD_LEFT);
}
?>