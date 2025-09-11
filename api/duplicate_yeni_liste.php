<?php
session_start();
header('Content-Type: application/json');
require '../config.php';
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Giriş yapmalısınız.']);
    exit;
}
$user_id = $_SESSION['id'];
$orijinal_liste_id = intval($_POST['liste_id'] ?? 0);
if ($orijinal_liste_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Geçersiz liste ID.']);
    exit;
}
$conn->begin_transaction();
try {
    $stmt_get_original = $conn->prepare("SELECT liste_adi, tercihler_json FROM yeni_kontenjan_listeleri WHERE liste_id = ? AND user_id = ?");
    $stmt_get_original->bind_param("ii", $orijinal_liste_id, $user_id);
    $stmt_get_original->execute();
    $result_original = $stmt_get_original->get_result();
    if ($result_original->num_rows === 0) {
        throw new Exception('Liste bulunamadı veya bu listeyi kopyalama yetkiniz yok.');
    }
    $orijinal_liste = $result_original->fetch_assoc();
    $yeni_liste_adi = $orijinal_liste['liste_adi'] . ' (Kopya)';
    $tercihler_json = $orijinal_liste['tercihler_json'];
    $stmt_new_list = $conn->prepare("INSERT INTO yeni_kontenjan_listeleri (user_id, liste_adi, tercihler_json) VALUES (?, ?, ?)");
    $stmt_new_list->bind_param("iss", $user_id, $yeni_liste_adi, $tercihler_json);
    $stmt_new_list->execute();
    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Liste başarıyla kopyalandı.']);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Bir hata oluştu: ' . $e->getMessage()]);
}
$conn->close();
?>