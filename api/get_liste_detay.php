<?php
session_start();
header('Content-Type: application/json');
require '../config.php';
$liste_id = intval($_GET['liste_id'] ?? 0);
$detaylar = [];
if ($liste_id > 0) {
    $user_id = $_SESSION['id'] ?? 0;
    $sql = "SELECT u.id, u.universite_adi, u.bolum_adi, u.puan_2024
            FROM tercih_listesi_ogeleri AS tlo
            JOIN universiteler AS u ON tlo.universite_id = u.id
            JOIN tercih_listeleri AS tl ON tlo.liste_id = tl.liste_id
            WHERE tlo.liste_id = ? AND tl.user_id = ?
            ORDER BY tlo.sira ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $liste_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $detaylar[] = $row;
        }
    }
}
echo json_encode($detaylar);
$conn->close();
?>