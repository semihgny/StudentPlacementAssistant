<?php
session_start();
header('Content-Type: application/json');
require '../config.php';
$response = ['success' => false, 'message' => 'Bilinmeyen bir hata oluştu.'];
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    $response['message'] = 'Bu işlemi yapmak için giriş yapmalısınız.';
    echo json_encode($response);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['id'];
    $liste_adi = trim($_POST['liste_adi'] ?? '');
    $tercihler_json = $_POST['tercihler'] ?? '[]';
    $tercihler = json_decode($tercihler_json, true);
    if (empty($liste_adi) || !is_array($tercihler) || empty($tercihler)) {
        $response['message'] = 'Liste adı veya tercihler boş olamaz.';
    } else {
        $conn->begin_transaction();
        try {
            $stmt = $conn->prepare("INSERT INTO tercih_listeleri (user_id, liste_adi) VALUES (?, ?)");
            $stmt->bind_param("is", $user_id, $liste_adi);
            $stmt->execute();
            $liste_id = $conn->insert_id; 
            $stmt_oge = $conn->prepare("INSERT INTO tercih_listesi_ogeleri (liste_id, universite_id, sira) VALUES (?, ?, ?)");
            foreach ($tercihler as $index => $universite_id) {
                $sira = $index + 1;
                $stmt_oge->bind_param("iii", $liste_id, $universite_id, $sira);
                $stmt_oge->execute();
            }
            $conn->commit();
            $response = ['success' => true, 'message' => 'Liste başarıyla kaydedildi.'];
        } catch (mysqli_sql_exception $exception) {
            $conn->rollback();
            $response['message'] = 'Veritabanı hatası: ' . $exception->getMessage();
        }
    }
}
echo json_encode($response);
$conn->close();
?>