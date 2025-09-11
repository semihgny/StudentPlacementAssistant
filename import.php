<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require 'config.php'; 
$dosyaYolu = 'universiteler.csv'; 
$conn->query("SET FOREIGN_KEY_CHECKS=0;");
echo "Yabancı anahtar kontrolleri geçici olarak durduruldu.<br>";
$conn->query("TRUNCATE TABLE `notlar`;");
$conn->query("TRUNCATE TABLE `tercih_listesi_ogeleri`;");
$conn->query("TRUNCATE TABLE `tercih_listeleri`;");
$conn->query("TRUNCATE TABLE `universiteler`;");
echo "Tüm tablolar (`notlar`, `tercih_listeleri`, `tercih_listesi_ogeleri`, `universiteler`) temizlendi.<br>";
$conn->query("SET FOREIGN_KEY_CHECKS=1;");
echo "Yabancı anahtar kontrolleri tekrar aktifleştirildi.<br><hr>";
if (($handle = fopen($dosyaYolu, "r")) !== FALSE) {
    fgetcsv($handle, 1000, ","); 
    $eklenen_satir_sayisi = 0;
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $universite_adi = $conn->real_escape_string($data[0]);
        $bolum_adi = $conn->real_escape_string($data[1]);
        $puan_2024 = (float)$data[2];
        $tur = $conn->real_escape_string($data[3]);
        $sql = "INSERT INTO universiteler (universite_adi, bolum_adi, puan_2024, tur) VALUES ('$universite_adi', '$bolum_adi', '$puan_2024', '$tur')";
        if ($conn->query($sql)) {
            $eklenen_satir_sayisi++;
        } else {
            echo "Hata: " . $sql . "<br>" . $conn->error . "<br>";
        }
    }
    fclose($handle);
    echo "<h2>Veri aktarımı başarıyla tamamlandı!</h2>";
    echo "<p>Toplam $eklenen_satir_sayisi adet üniversite/bölüm bilgisi veritabanına eklendi.</p>";
} else {
    echo "Hata: CSV dosyası ($dosyaYolu) açılamadı. Dosyanın sunucuda doğru yerde ve adının `universiteler.csv` olduğundan emin olun.";
}
$conn->close();
echo "<hr><b>GÜVENLİK UYARISI:</b> Bu işlem bittiğine göre, lütfen sunucunuzdan hem `import.php` hem de `universiteler.csv` dosyalarını silin.";
?>