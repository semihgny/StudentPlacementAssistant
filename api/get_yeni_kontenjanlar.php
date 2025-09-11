<?php
header('Content-Type: application/json; charset=utf-8');
$csvFilePath = '../yeni_kontenjanlar.csv'; 
$data = [];
$error = null;
if (file_exists($csvFilePath)) {
    if (($handle = fopen($csvFilePath, 'r')) !== FALSE) {
        $headers = fgetcsv($handle);
        if ($headers !== false) {
            while (($row = fgetcsv($handle)) !== FALSE) {
                if (count($headers) == count($row)) {
                    $data[] = array_combine($headers, $row);
                }
            }
        }
        fclose($handle);
    } else {
        $error = "HATA: CSV dosyası var ama OKUNAMIYOR. Dosya izinlerini (permissions) kontrol edin (Örn: 644).";
    }
} else {
    $error = "HATA: CSV dosyası BULUNAMADI. Lütfen 'yeni_kontenjanlar.csv' adında bir dosyanın 'public_html' klasörünün içinde olduğundan emin olun.";
}
echo json_encode(['data' => $data, 'error' => $error]);
?>