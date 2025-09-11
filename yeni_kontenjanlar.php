<?php 
$tur = $_GET['tur'] ?? 'hepsi';
$baslik = "Yeni Kontenjanlar (Puansız)";
if ($tur === 'bilgisayar') $baslik = "Yeni Kontenjanlar (Bilgisayar Müh.)";
if ($tur === 'yazilim') $baslik = "Yeni Kontenjanlar (Yazılım Müh.)";
include 'partials/header.php'; 
?>
<div class="container mt-4" data-tur="<?php echo htmlspecialchars($tur); ?>">
    <div class="row">
        <div class="col-12 col-lg-8 mb-4">
            <div class="card shadow-sm">
                <div class="card-header"><h4 class="mb-0"><?php echo $baslik; ?></h4></div>
                <div class="card-body border-bottom">
                    <div class="row g-2 align-items-center">
                        <div class="col-md">
                            <label for="uni_ara" class="form-label small">Üniversite Adına Göre Ara</label>
                            <input type="text" class="form-control form-control-sm" id="uni_ara" placeholder="Üniversite adı yazın...">
                        </div>
                        <div class="col-md">
                            <label for="bolum_ara" class="form-label small">Bölüm Adına Göre Ara</label>
                            <input type="text" class="form-control form-control-sm" id="bolum_ara" placeholder="Bölüm adı yazın...">
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="yeniDataTable" class="table table-striped" style="width:100%"></table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4 mb-4">
            <div class="card shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header"><h4 id="tercih-listesi-baslik" class="mb-0">Yeni Tercih Listem</h4></div>
                <div class="card-body"><ul id="tercih-sepeti" class="list-group"></ul></div>
                <div class="card-footer" id="islem-butonlari">
                    </div>
            </div>
        </div>
    </div>
</div>
<?php include 'partials/footer-scripts.php'; ?>
<script src="yeni_kontenjanlar.js"></script>
</body>
</html>