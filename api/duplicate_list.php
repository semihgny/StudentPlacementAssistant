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
    $stmt_get_original = $conn->prepare("SELECT liste_adi FROM tercih_listeleri WHERE liste_id = ? AND user_id = ?");
    $stmt_get_original->bind_param("ii", $orijinal_liste_id, $user_id);
    $stmt_get_original->execute();
    $result_original = $stmt_get_original->get_result();
    if ($result_original->num_rows === 0) {
        throw new Exception('Liste bulunamadı veya bu listeyi kopyalama yetkiniz yok.');
    }
    $orijinal_liste = $result_original->fetch_assoc();
    $yeni_liste_adi = $orijinal_liste['liste_adi'] . ' (Kopya)';
    $stmt_new_list = $conn->prepare("INSERT INTO tercih_listeleri (user_id, liste_adi) VALUES (?, ?)");
    $stmt_new_list->bind_param("is", $user_id, $yeni_liste_adi);
    $stmt_new_list->execute();
    $yeni_liste_id = $conn->insert_id;
    $stmt_get_items = $conn->prepare("SELECT universite_id, sira FROM tercih_listesi_ogeleri WHERE liste_id = ?");
    $stmt_get_items->bind_param("i", $orijinal_liste_id);
    $stmt_get_items->execute();
    $items = $stmt_get_items->get_result()->fetch_all(MYSQLI_ASSOC);
    if (!empty($items)) {
        $stmt_insert_item = $conn->prepare("INSERT INTO tercih_listesi_ogeleri (liste_id, universite_id, sira) VALUES (?, ?, ?)");
        foreach ($items as $item) {
            $stmt_insert_item->bind_param("iii", $yeni_liste_id, $item['universite_id'], $item['sira']);
            $stmt_insert_item->execute();
        }
    }
    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Liste başarıyla kopyalandı.']);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Bir hata oluştu: ' . $e->getMessage()]);
}
$conn->close();
?>