document.addEventListener('DOMContentLoaded', () => {
    const accordion = document.getElementById('yeni-listeler-accordion');
    async function listeleriYukle() {
        accordion.innerHTML = '<div class="text-center"><div class="spinner-border"></div></div>';
        try {
            const response = await fetch('api/get_yeni_listeler.php');
            const listeler = await response.json();
            accordion.innerHTML = '';
            if (listeler.length > 0) {
                listeler.forEach((liste, index) => {
                    const guvenliListeAdi = liste.liste_adi.replace(/'/g, "\\'").replace(/"/g, "&quot;");
                    const tercihlerData = JSON.stringify(liste.tercihler_json).replace(/'/g, "&apos;");
                    const listeKarti = `
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading-${index}">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-${index}" 
                                        data-tercihler='${tercihlerData}' onclick="listeDetayiniGetir(this)">
                                    <span class="fw-bold me-2">${liste.liste_adi}</span> 
                                    <small class="text-muted">(${new Date(liste.olusturma_tarihi).toLocaleDateString('tr-TR')})</small>
                                </button>
                            </h2>
                            <div id="collapse-${index}" class="accordion-collapse collapse" data-bs-parent="#yeni-listeler-accordion">
                                <div class="accordion-body"><div class="text-center"><div class="spinner-border spinner-border-sm"></div></div></div>
                                <div class="card-footer text-end bg-white">
                                    <button class="btn btn-info btn-sm text-white" onclick="listeyiKopyala(${liste.liste_id})">Kopyala</button>
                                    <button class="btn btn-secondary btn-sm" onclick="listeyiDuzenle(${liste.liste_id}, '${guvenliListeAdi}')">Düzenle</button>
                                    <button class="btn btn-danger btn-sm" onclick="listeyiSil(${liste.liste_id})">Sil</button>
                                </div>
                            </div>
                        </div>`;
                    accordion.innerHTML += listeKarti;
                });
            } else {
                accordion.innerHTML = `<div class="alert alert-info">Henüz kaydedilmiş bir simülasyon listeniz yok. <a href="yeni_kontenjanlar.php" class="alert-link">Hemen bir tane oluşturun.</a></div>`;
            }
        } catch (error) {
            console.error('Listeler yüklenirken hata:', error);
            accordion.innerHTML = '<div class="alert alert-danger">Listeler yüklenirken bir hata oluştu. Lütfen daha sonra tekrar deneyin.</div>';
        }
    }
    window.listeDetayiniGetir = (buttonElement) => {
        const accordionBody = buttonElement.closest('.accordion-header').nextElementSibling.querySelector('.accordion-body');
        if (accordionBody.querySelector('ol')) return; 
        try {
            const tercihlerJsonString = JSON.parse(buttonElement.dataset.tercihler);
            const tercihler = JSON.parse(tercihlerJsonString);
            if (tercihler && tercihler.length > 0) {
                let html = '<ol class="list-group list-group-numbered">';
                tercihler.forEach(tercih => {
                    html += `<li class="list-group-item">${tercih.universite_adi} - ${tercih.bolum_adi} (Kont: ${tercih.kontenjan})</li>`;
                });
                html += '</ol>';
                accordionBody.innerHTML = html;
            } else { 
                accordionBody.innerHTML = '<p class="text-muted">Bu liste boş.</p>'; 
            }
        } catch (e) {
            console.error("JSON parse hatası:", e);
            accordionBody.innerHTML = '<p class="text-danger">Liste içeriği görüntülenemedi.</p>';
        }
    };
    window.listeyiDuzenle = (liste_id, liste_adi) => {
        localStorage.setItem('edit_yeni_liste_id', liste_id);
        localStorage.setItem('edit_yeni_liste_adi', liste_adi);
        window.location.href = 'yeni_kontenjanlar.php';
    };
    window.listeyiSil = async (liste_id) => {
        if (confirm('Bu simülasyon listesini kalıcı olarak silmek istediğinizden emin misiniz?')) {
            const formData = new FormData();
            formData.append('liste_id', liste_id);
            const response = await fetch('api/delete_yeni_liste.php', { method: 'POST', body: formData });
            const result = await response.json();
            alert(result.message);
            if(result.success) { listeleriYukle(); }
        }
    };
    window.listeyiKopyala = async (liste_id) => {
        if (!confirm('Bu liste, sonuna "(Kopya)" ekiyle çoğaltılacaktır. Onaylıyor musunuz?')) return;
        const formData = new FormData();
        formData.append('liste_id', liste_id);
        const response = await fetch('api/duplicate_yeni_liste.php', { method: 'POST', body: formData });
        const result = await response.json();
        alert(result.message);
        if (result.success) { listeleriYukle(); }
    };
    listeleriYukle();
});