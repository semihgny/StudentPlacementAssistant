document.addEventListener('DOMContentLoaded', () => {
    const kayitliListelerAccordion = document.getElementById('kayitli-listeler-accordion');
    window.listeleriYukle = async function() {
        try {
            const response = await fetch('api/get_tercih_listeleri.php');
            const listeler = await response.json();
            kayitliListelerAccordion.innerHTML = '';
            if (listeler.length > 0) {
                listeler.forEach((liste, index) => {
                    const guvenliListeAdi = liste.liste_adi.replace(/'/g, "\\'").replace(/"/g, "&quot;");
                    const listeKarti = `
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading-${index}">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-${index}" onclick="listeDetayiniGetir(${liste.liste_id}, '${guvenliListeAdi}')">
                                    <span class="fw-bold me-2">${liste.liste_adi}</span> 
                                    <small class="text-muted">(${new Date(liste.olusturma_tarihi).toLocaleDateString('tr-TR')})</small>
                                </button>
                            </h2>
                            <div id="collapse-${index}" class="accordion-collapse collapse" data-bs-parent="#kayitli-listeler-accordion">
                                <div class="accordion-body" id="liste-detay-${liste.liste_id}"><div class="text-center"><div class="spinner-border spinner-border-sm"></div></div></div>
                                <div class="card-footer text-end bg-white">
                                    <button class="btn btn-info btn-sm text-white" onclick="listeyiKopyala(${liste.liste_id})">Kopyala</button>
                                    <button class="btn btn-secondary btn-sm" onclick="listeyiDuzenle(${liste.liste_id}, '${guvenliListeAdi}')">Düzenle</button>
                                    <button class="btn btn-danger btn-sm" onclick="listeyiSil(${liste.liste_id})">Sil</button>
                                </div>
                            </div>
                        </div>
                    `;
                    kayitliListelerAccordion.innerHTML += listeKarti;
                });
            } else {
                kayitliListelerAccordion.innerHTML = `<div class="alert alert-info">Henüz kaydedilmiş bir tercih listeniz yok. <a href="index.php" class="alert-link">Hemen bir tane oluşturun.</a></div>`;
            }
        } catch (error) {
            console.error('Listeler yüklenirken hata:', error);
            kayitliListelerAccordion.innerHTML = '<div class="alert alert-danger">Listeler yüklenirken bir hata oluştu.</div>';
        }
    }
    window.listeyiKopyala = async function(liste_id) {
        if (!confirm('Bu liste, sonuna "(Kopya)" ekiyle çoğaltılacaktır. Onaylıyor musunuz?')) return;
        const formData = new FormData();
        formData.append('liste_id', liste_id);
        const response = await fetch('api/duplicate_list.php', { method: 'POST', body: formData });
        const result = await response.json();
        if (result.success) {
            alert('Liste başarıyla kopyalandı.');
            listeleriYukle();
        } else {
            alert('Hata: ' + result.message);
        }
    };
    window.listeyiDuzenle = function(liste_id, liste_adi) {
        localStorage.setItem('edit_liste_id', liste_id);
        localStorage.setItem('edit_liste_adi', liste_adi);
        window.location.href = 'index.php';
    };
    window.listeDetayiniGetir = async (liste_id, liste_adi) => {
        const detayAlani = document.getElementById(`liste-detay-${liste_id}`);
        if (detayAlani.querySelector('ol')) return; 
        try {
            const response = await fetch(`api/get_liste_detay.php?liste_id=${liste_id}`);
            const detaylar = await response.json();
            if (detaylar.length > 0) {
                let html = '<ol class="list-group list-group-numbered">';
                detaylar.forEach(detay => {
                    html += `<li class="list-group-item">${detay.universite_adi} - ${detay.bolum_adi} (${detay.puan_2024})</li>`;
                });
                html += '</ol>';
                const guvenliListeAdi = liste_adi.replace(/'/g, "\\'").replace(/"/g, "&quot;");
                html += `<div class="text-end mt-3"><button class="btn btn-warning btn-sm" onclick="listeyiIndir(${liste_id}, '${guvenliListeAdi}')"><i class="bi bi-download me-2"></i>Listeyi İndir (.txt)</button></div>`;
                detayAlani.innerHTML = html;
            } else {
                detayAlani.innerHTML = '<p class="text-muted">Bu liste boş.</p>';
            }
        } catch (error) {
            console.error('Liste detayı getirilirken hata:', error);
        }
    };
    window.listeyiSil = async function(liste_id) {
        if (confirm('Bu tercih listesini kalıcı olarak silmek istediğinizden emin misiniz?')) {
            const formData = new FormData();
            formData.append('liste_id', liste_id);
            const response = await fetch('api/delete_list.php', { method: 'POST', body: formData });
            const result = await response.json();
            if(result.success) {
                alert('Liste başarıyla silindi.');
                listeleriYukle();
            } else {
                alert('Hata: ' + result.message);
            }
        }
    };
    window.listeyiIndir = async function(liste_id, liste_adi) {
        try {
            const response = await fetch(`api/get_liste_detay.php?liste_id=${liste_id}`);
            const detaylar = await response.json();
            if (detaylar.length === 0) { alert('İndirilecek bir içerik bulunamadı.'); return; }
            let dosyaIcerigi = `${liste_adi}\n===============================\n\n`;
            detaylar.forEach((tercih, index) => {
                dosyaIcerigi += `${index + 1}. ${tercih.universite_adi} - ${tercih.bolum_adi}\n`;
            });
            const blob = new Blob([dosyaIcerigi], { type: 'text/plain;charset=utf-8' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            const safeFileName = liste_adi.replace(/[^a-z0-9]/gi, '_').toLowerCase();
            link.download = `${safeFileName}.txt`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        } catch (error) {
            alert('Liste indirilirken bir hata oluştu.');
            console.error("İndirme Hatası:", error);
        }
    };
    listeleriYukle();
});