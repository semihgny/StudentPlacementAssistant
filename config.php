<?php
$servername = "localhost";
$username = "sem23euneycom_trc"; 
$password = "49hDT&U@FvYNF&eY"; 
$dbname = "sem23euneycom_trc"; 
$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8mb4");
if ($conn->connect_error) {
  die("Veritabanı bağlantısı başarısız: " . $conn->connect_error);
}
?>