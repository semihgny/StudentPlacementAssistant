<?php
session_start();
header('Content-Type: application/json');
require '../config.php';
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Giriş yapmalısınız.']);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['id'];
    $liste_adi = trim($_POST['liste_adi'] ?? '');
    $tercihler_json = $_POST['tercihler_json'] ?? '[]';
    if (empty($liste_adi) || empty($tercihler_json)) {
        echo json_encode(['success' => false, 'message' => 'Liste adı veya tercihler boş olamaz.']);
        exit;
    }
    $stmt = $conn->prepare("INSERT INTO yeni_kontenjan_listeleri (user_id, liste_adi, tercihler_json) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $liste_adi, $tercihler_json);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Yeni kontenjan listeniz başarıyla kaydedildi.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Veritabanına kaydederken bir hata oluştu.']);
    }
    $stmt->close();
    $conn->close();
}
?>