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
    $liste_id = intval($_POST['liste_id'] ?? 0);
    $liste_adi = trim($_POST['liste_adi'] ?? '');
    $tercihler = json_decode($_POST['tercihler'] ?? '[]', true);
    if (empty($liste_adi) || empty($liste_id) || !is_array($tercihler)) {
        echo json_encode(['success' => false, 'message' => 'Eksik veya geçersiz veri.']);
        exit;
    }
    $stmt_check = $conn->prepare("SELECT liste_id FROM tercih_listeleri WHERE liste_id = ? AND user_id = ?");
    $stmt_check->bind_param("ii", $liste_id, $user_id);
    $stmt_check->execute();
    if ($stmt_check->get_result()->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Bu listeyi düzenleme yetkiniz yok.']);
        exit;
    }
    $conn->begin_transaction();
    try {
        $stmt_update_name = $conn->prepare("UPDATE tercih_listeleri SET liste_adi = ? WHERE liste_id = ?");
        $stmt_update_name->bind_param("si", $liste_adi, $liste_id);
        $stmt_update_name->execute();
        $stmt_delete_items = $conn->prepare("DELETE FROM tercih_listesi_ogeleri WHERE liste_id = ?");
        $stmt_delete_items->bind_param("i", $liste_id);
        $stmt_delete_items->execute();
        $stmt_insert_items = $conn->prepare("INSERT INTO tercih_listesi_ogeleri (liste_id, universite_id, sira) VALUES (?, ?, ?)");
        foreach ($tercihler as $index => $universite_id) {
            $sira = $index + 1;
            $stmt_insert_items->bind_param("iii", $liste_id, $universite_id, $sira);
            $stmt_insert_items->execute();
        }
        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Liste başarıyla güncellendi.']);
    } catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Veritabanı hatası: ' . $exception->getMessage()]);
    }
}
?>