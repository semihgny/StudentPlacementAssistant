<?php
header('Content-Type: application/json');
require '../config.php';
$universiteler = [];
$sql = "SELECT id, universite_adi, bolum_adi, puan_2024, tur FROM universiteler";
$result = $conn->query($sql);
if ($result) {
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $universiteler[] = $row;
        }
    }
} else {
    echo json_encode(['error' => 'SQL sorgusu başarısız: ' . $conn->error]);
    $conn->close();
    exit;
}
echo json_encode($universiteler);
$conn->close();
?>