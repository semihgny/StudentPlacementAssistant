<?php
session_start();
header('Content-Type: application/json');
require '../config.php';
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Giriş yapmalısınız.']); exit;
}
$user_id = $_SESSION['id'];
$not_id = intval($_POST['not_id'] ?? 0);
if ($not_id > 0) {
    $stmt = $conn->prepare("DELETE FROM notlar WHERE not_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $not_id, $user_id);
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Not silindi.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Not bulunamadı veya bu notu silme yetkiniz yok.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Bir veritabanı hatası oluştu.']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Geçersiz not ID.']);
}
$conn->close();
?>