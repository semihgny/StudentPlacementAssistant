<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require '../config.php';
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode([]);
    exit;
}
$user_id = $_SESSION['id'];
$listeler = [];
$sql = "SELECT liste_id, liste_adi, olusturma_tarihi, tercihler_json FROM yeni_kontenjan_listeleri WHERE user_id = ? ORDER BY olusturma_tarihi DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $listeler[] = $row;
    }
}
echo json_encode($listeler);
$conn->close();
?>