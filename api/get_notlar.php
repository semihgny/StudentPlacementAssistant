<?php
session_start();
header('Content-Type: application/json');
require '../config.php';
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode([]);
    exit;
}
$user_id = $_SESSION['id'];
$notlar = [];
$sql = "SELECT n.not_id, n.not_metni, n.kayit_tarihi, u.universite_adi
        FROM notlar AS n 
        JOIN universiteler AS u ON n.universite_id = u.id
        WHERE n.user_id = ? 
        ORDER BY n.kayit_tarihi DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $notlar[] = $row;
    }
}
echo json_encode($notlar);
$conn->close();
?>