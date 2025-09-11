<?php
session_start();
header('Content-Type: application/json');
require '../config.php';
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Giriş yapmalısınız.']); exit;
}
$user_id = $_SESSION['id'];
$liste_id = intval($_POST['liste_id'] ?? 0);
if ($liste_id > 0) {
    $stmt = $conn->prepare("DELETE FROM tercih_listeleri WHERE liste_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $liste_id, $user_id);
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Liste silindi.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Liste bulunamadı veya bu listeyi silme yetkiniz yok.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Bir veritabanı hatası oluştu.']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Geçersiz liste ID.']);
}
$conn->close();
?>