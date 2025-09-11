<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require 'config.php';
$csvDosyaAdi = 'yeni_kontenjanlar.csv';
$tabloAdi = 'yeni_kontenjan_datalari';
$conn->query("TRUNCATE TABLE `$tabloAdi`");
echo "Mevcut `$tabloAdi` tablosu temizlendi.<br><hr>";
if (($handle = fopen($csvDosyaAdi, "r")) !== FALSE) {
    fgetcsv($handle); 
    $eklenen_satir_sayisi = 0;
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $universite_adi = $conn->real_escape_string($data[0]);
        $bolum_adi = $conn->real_escape_string($data[1]);
        $kontenjan = (int)$data[2];
        $tur = 'diger'; 
        if (stripos($bolum_adi, 'Bilgisayar Mühendisliği') !== false) {
            $tur = 'bilgisayar';
        } elseif (stripos($bolum_adi, 'Yazılım Mühendisliği') !== false) {
            $tur = 'yazilim';
        }
        $stmt = $conn->prepare("INSERT INTO $tabloAdi (universite_adi, bolum_adi, kontenjan, tur) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssis", $universite_adi, $bolum_adi, $kontenjan, $tur);
        if ($stmt->execute()) {
            $eklenen_satir_sayisi++;
        } else {
            echo "Hata: " . $stmt->error . "<br>";
        }
    }
    fclose($handle);
    echo "<h2>Yeni kontenjan verisi aktarımı tamamlandı!</h2>";
    echo "<p>Toplam $eklenen_satir_sayisi adet kayıt `$tabloAdi` tablosuna eklendi.</p>";
} else {
    echo "Hata: CSV dosyası ($csvDosyaAdi) açılamadı.";
}
$conn->close();
echo "<hr><b>GÜVENLİK UYARISI:</b> Lütfen sunucunuzdan bu dosyayı (`import_yeni.php`) ve CSV dosyasını silin.";
?>