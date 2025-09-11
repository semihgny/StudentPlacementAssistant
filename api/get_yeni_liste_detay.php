<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require '../config.php';
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode([]); exit;
}
$user_id = $_SESSION['id'];
$liste_id = intval($_GET['liste_id'] ?? 0);
$detaylar = [];
if ($liste_id > 0) {
    $stmt = $conn->prepare("SELECT tercihler_json FROM yeni_kontenjan_listeleri WHERE liste_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $liste_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $detaylar = json_decode($row['tercihler_json'], true);
    }
}
echo json_encode($detaylar);
$conn->close();
?>