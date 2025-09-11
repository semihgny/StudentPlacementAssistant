<?php
session_start();
header('Content-Type: application/json');
require '../config.php';
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['not_metni' => null]);
    exit;
}
$user_id = $_SESSION['id'];
$universite_id = intval($_GET['universite_id'] ?? 0);
$response = ['not_metni' => null];
if ($universite_id > 0) {
    $stmt = $conn->prepare("SELECT not_metni FROM notlar WHERE user_id = ? AND universite_id = ?");
    $stmt->bind_param("ii", $user_id, $universite_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $response['not_metni'] = $row['not_metni'];
    }
}
echo json_encode($response);
$conn->close();
?>