<?php include 'partials/header.php'; ?>
<div class="container mt-4">
    <div class="row">
        <div class="col-12 col-lg-8 mb-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h4 id="universite-listesi-baslik" class="mb-0">Üniversiteler (2024 Puanları)</h4>
                </div>
                <div class="card-body border-bottom">
                    <div id="kategori-filtreleri" class="btn-group w-100 mb-3" role="group">
                        <button type="button" class="btn btn-outline-primary active" id="hepsi-btn">Tümü</button>
                        <button type="button" class="btn btn-outline-primary" id="bilgisayar-btn">Bilgisayar Müh.</button>
                        <button type="button" class="btn btn-outline-primary" id="yazilim-btn">Yazılım Müh.</button>
                    </div>
                    <div class="row g-2 align-items-center">
                        <div class="col-md">
                            <label for="minPuan" class="form-label small">Minimum Puan</label>
                            <input type="number" class="form-control form-control-sm" id="minPuan" placeholder="Örn: 300">
                        </div>
                        <div class="col-md">
                            <label for="maxPuan" class="form-label small">Maksimum Puan</label>
                            <input type="number" class="form-control form-control-sm" id="maxPuan" placeholder="Örn: 350">
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="universiteDataTable" class="table table-striped table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Üniversite</th>
                                    <th>Bölüm</th>
                                    <th>Puan</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody id="universite-tablosu">
                                </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4 mb-4">
            <div class="tercih-listesi-wrapper">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h4 id="tercih-listesi-baslik" class="mb-0">Güncel Tercih Listem</h4>
                    </div>
                    <div class="card-body">
                        <ul id="tercih-sepeti" class="list-group">
                            </ul>
                    </div>
                    <div class="card-footer" id="islem-butonlari">
                        <div class="mb-2">
                             <input type="text" id="liste-adi" class="form-control" placeholder="Yeni Liste Adı Girin...">
                        </div>
                        <div class="d-grid gap-2">
                            <button id="kaydet-btn" class="btn btn-success">Listeyi Kaydet</button>
                            <button id="indir-btn" class="btn btn-warning">Listeyi Dışa Aktar (.txt)</button>
                            <label for="liste-yukle-input" class="btn btn-info text-white">Listeyi İçe Aktar (.txt)</label>
                            <input type="file" id="liste-yukle-input" accept=".txt" style="display: none;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="noteModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="noteModalTitle">Not Ekle/Düzenle</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p class="fw-bold" id="noteModalUniversityName"></p>
        <textarea id="noteTextarea" class="form-control" rows="8" placeholder="Notunuzu buraya yazın..."></textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
        <button type="button" id="saveNoteBtn" class="btn btn-primary">Notu Kaydet</button>
      </div>
    </div>
  </div>
</div>
<?php include 'partials/footer-scripts.php'; ?>
<script src="script.js"></script>
</body>
</html>