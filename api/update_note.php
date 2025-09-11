<?php
session_start();
header('Content-Type: application/json');
require '../config.php';
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Giriş yapmalısınız.']); exit;
}
$user_id = $_SESSION['id'];
$not_id = intval($_POST['not_id'] ?? 0);
$not_metni = trim($_POST['not_metni'] ?? '');
if ($not_id > 0 && !empty($not_metni)) {
    $stmt = $conn->prepare("UPDATE notlar SET not_metni = ? WHERE not_id = ? AND user_id = ?");
    $stmt->bind_param("sii", $not_metni, $not_id, $user_id);
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Not güncellendi.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Not bulunamadı veya bu notu düzenleme yetkiniz yok.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Bir veritabanı hatası oluştu.']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Geçersiz not ID veya boş not metni.']);
}
$conn->close();
?>