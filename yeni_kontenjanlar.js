document.addEventListener('DOMContentLoaded', () => {
    const tercihSepeti = document.getElementById('tercih-sepeti');
    const yeniDataTable = $('#yeniDataTable');
    const tercihListesiBaslik = document.getElementById('tercih-listesi-baslik');
    const islemButonlariDiv = document.getElementById('islem-butonlari');
    let dataTableInstance;
    let tumUniversiteler = [];
    let mevcutTercihler = [];
    let duzenlemeModu = false;
    let duzenlenecekListeId = null;
    const tur = document.querySelector('.container').dataset.tur;
    const uniAraInput = document.getElementById('uni_ara');
    const bolumAraInput = document.getElementById('bolum_ara');
    uniAraInput.addEventListener('keyup', () => {
        dataTableInstance.column(0).search(uniAraInput.value).draw();
    });
    bolumAraInput.addEventListener('keyup', () => {
        dataTableInstance.column(1).search(bolumAraInput.value).draw();
    });
    async function universiteleriYukle() {
        if (tumUniversiteler.length === 0) {
            try {
                const response = await fetch('api/get_yeni_kontenjanlar.php');
                const json = await response.json();
                if (json.error) {
                    alert("Veri Yükleme Hatası: " + json.error);
                    return;
                }
                tumUniversiteler = json.data;
            } catch (e) {
                console.error("API'den veri alınamadı:", e);
                alert("Sunucudan veri alınırken bir hata oluştu.");
                return;
            }
        }
        let filtrelenmisVeri = tumUniversiteler;
        if (tur === 'bilgisayar' || tur === 'yazilim'){
             filtrelenmisVeri = tumUniversiteler.filter(uni => uni.tur === tur);
        }
        if (dataTableInstance) { dataTableInstance.destroy(); }
        dataTableInstance = yeniDataTable.DataTable({
            data: filtrelenmisVeri,
            responsive: true,
            dom: 'lrtip', 
            columns: [
                { data: 'universite_adi', title: 'Üniversite' },
                { data: 'bolum_adi', title: 'Bölüm' },
                { data: 'kontenjan', title: 'Kontenjan' },
                {
                    data: null, title: 'İşlemler',
                    render: (data, type, row) => `<button class="btn btn-primary btn-sm" onclick='terciheEkle(${JSON.stringify(row).replace(/'/g, "&apos;")})'>Ekle</button>`,
                    orderable: false,
                    searchable: false 
                }
            ],
            language: { url: '
        });
    }
    function tercihListesiniGuncelle() {
        tercihSepeti.innerHTML = '';
        if (mevcutTercihler.length === 0) {
            tercihSepeti.innerHTML = '<li class="list-group-item text-center text-muted p-3">Listeniz boş.</li>';
            return;
        }
        mevcutTercihler.forEach((tercih, index) => {
            const listItem = document.createElement('li');
            listItem.className = 'list-group-item d-flex justify-content-between align-items-start list-group-item-action';
            listItem.dataset.rowData = JSON.stringify(tercih);
            listItem.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="bi bi-grip-vertical me-2 handle" style="cursor: grab;"></i>
                    <div>
                        <strong class="me-2">${index + 1}.</strong> ${tercih.universite_adi}
                        <br><small class="text-muted" style="margin-left: 28px;">${tercih.bolum_adi} (Kont: ${tercih.kontenjan})</small>
                    </div>
                </div>
                <button class="btn btn-danger btn-sm py-0 px-2" onclick="tercihtenCikar(${index})">X</button>`;
            tercihSepeti.appendChild(listItem);
        });
    }
    window.terciheEkle = (rowData) => {
        if (mevcutTercihler.length >= 30) { alert('En fazla 30 tercih yapabilirsiniz.'); return; }
        if (mevcutTercihler.some(t => t.universite_adi === rowData.universite_adi && t.bolum_adi === rowData.bolum_adi)) {
            alert('Bu bölüm zaten listenizde.'); return;
        }
        mevcutTercihler.push(rowData);
        tercihListesiniGuncelle();
    };
    window.tercihtenCikar = (index) => {
        mevcutTercihler.splice(index, 1);
        tercihListesiniGuncelle();
    };
    async function duzenlemeModunuBaslat() {
        const liste_id = localStorage.getItem('edit_yeni_liste_id');
        const liste_adi = localStorage.getItem('edit_yeni_liste_adi');
        if (liste_id && liste_adi) {
            tercihListesiBaslik.textContent = `'${liste_adi}' Listesini Düzenle`;
            islemButonlariDiv.innerHTML = `<input type="text" id="liste-adi" class="form-control mb-2" value="${liste_adi}"><div class="d-grid gap-2"><button id="guncelle-btn" class="btn btn-success">Listeyi Güncelle</button><button id="iptal-btn" class="btn btn-secondary">İptal</button></div>`;
            const response = await fetch(`api/get_yeni_liste_detay.php?liste_id=${liste_id}`);
            mevcutTercihler = await response.json();
            tercihListesiniGuncelle();
            document.getElementById('guncelle-btn').addEventListener('click', () => tercihListesiniGuncelleKaydet(parseInt(liste_id)));
            document.getElementById('iptal-btn').addEventListener('click', () => { window.location.href = 'listelerim2.php'; });
        } else {
            islemButonlariDiv.innerHTML = `<input type="text" id="liste-adi" class="form-control mb-2" placeholder="Liste Adı Girin..."><div class="d-grid"><button id="kaydet-btn" class="btn btn-success">Bu Listeyi Kaydet</button></div>`;
            document.getElementById('kaydet-btn').addEventListener('click', tercihListesiniKaydet);
        }
    }
    async function tercihListesiniKaydet() {
        const listeAdi = document.getElementById('liste-adi').value.trim();
        if (!listeAdi) { alert('Lütfen listenize bir ad verin.'); return; }
        if (mevcutTercihler.length === 0) { alert('Kaydetmek için tercih eklemelisiniz.'); return; }
        const formData = new FormData();
        formData.append('liste_adi', listeAdi);
        formData.append('tercihler_json', JSON.stringify(mevcutTercihler));
        const response = await fetch('api/save_yeni_liste.php', { method: 'POST', body: formData });
        const result = await response.json();
        alert(result.message);
        if (result.success) {
            mevcutTercihler = [];
            tercihListesiniGuncelle();
            document.getElementById('liste-adi').value = '';
        }
    }
    async function tercihListesiniGuncelleKaydet(liste_id) {
        const yeniListeAdi = document.getElementById('liste-adi').value.trim();
        if (!yeniListeAdi) { alert('Liste adı boş olamaz.'); return; }
        const formData = new FormData();
        formData.append('liste_id', liste_id);
        formData.append('liste_adi', yeniListeAdi);
        formData.append('tercihler_json', JSON.stringify(mevcutTercihler));
        const response = await fetch('api/update_yeni_liste.php', { method: 'POST', body: formData });
        const result = await response.json();
        alert(result.message);
        if (result.success) { window.location.href = 'listelerim2.php'; }
    }
    new Sortable(tercihSepeti, {
        animation: 150,
        handle: '.handle',
        onEnd: function () {
            const guncellenmisSira = Array.from(tercihSepeti.children).map(li => JSON.parse(li.dataset.rowData));
            mevcutTercihler = guncellenmisSira;
            tercihListesiniGuncelle();
        }
    });
    universiteleriYukle();
    duzenlemeModunuBaslat();
});