<?php
session_start();
header('Content-Type: application/json');
require '../config.php';
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Bu işlemi yapmak için giriş yapmalısınız.']);
    exit;
}
$user_id = $_SESSION['id'];
$universite_id = intval($_POST['universite_id'] ?? 0);
$not_metni = trim($_POST['not_metni'] ?? '');
if ($universite_id > 0 && !empty($not_metni)) {
    $stmt_check = $conn->prepare("SELECT not_id FROM notlar WHERE user_id = ? AND universite_id = ?");
    $stmt_check->bind_param("ii", $user_id, $universite_id);
    $stmt_check->execute();
    $stmt_check->store_result();
    if ($stmt_check->num_rows > 0) {
        $stmt_update = $conn->prepare("UPDATE notlar SET not_metni = ? WHERE user_id = ? AND universite_id = ?");
        $stmt_update->bind_param("sii", $not_metni, $user_id, $universite_id);
        if ($stmt_update->execute()) {
            $response = ['success' => true, 'message' => 'Not başarıyla güncellendi.'];
        } else {
            $response = ['success' => false, 'message' => 'Not güncellenirken bir hata oluştu.'];
        }
        $stmt_update->close();
    } else {
        $stmt_insert = $conn->prepare("INSERT INTO notlar (user_id, universite_id, not_metni) VALUES (?, ?, ?)");
        $stmt_insert->bind_param("iis", $user_id, $universite_id, $not_metni);
        if ($stmt_insert->execute()) {
            $response = ['success' => true, 'message' => 'Not başarıyla kaydedildi.'];
        } else {
            $response = ['success' => false, 'message' => 'Not kaydedilirken bir hata oluştu.'];
        }
        $stmt_insert->close();
    }
    $stmt_check->close();
} else {
    $response = ['success' => false, 'message' => 'Üniversite ID veya not metni eksik.'];
}
echo json_encode($response);
$conn->close();
?>