<?php
session_start();
header('Content-Type: application/json');
require '../config.php';
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Giriş yapmalısınız.']); exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['id'];
    $liste_id = intval($_POST['liste_id'] ?? 0);
    $liste_adi = trim($_POST['liste_adi'] ?? '');
    $tercihler_json = $_POST['tercihler_json'] ?? '[]';
    if (empty($liste_adi) || empty($liste_id)) {
        echo json_encode(['success' => false, 'message' => 'Eksik veya geçersiz veri.']); exit;
    }
    $stmt = $conn->prepare("UPDATE yeni_kontenjan_listeleri SET liste_adi = ?, tercihler_json = ? WHERE liste_id = ? AND user_id = ?");
    $stmt->bind_param("ssii", $liste_adi, $tercihler_json, $liste_id, $user_id);
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Liste başarıyla güncellendi.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Liste bulunamadı veya değiştirilmedi.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Veritabanı hatası.']);
    }
    $stmt->close();
    $conn->close();
}
?>