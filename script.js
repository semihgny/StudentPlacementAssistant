document.addEventListener('DOMContentLoaded', () => {
    const tercihSepeti = document.getElementById('tercih-sepeti');
    const kategoriFiltreButonlari = document.querySelectorAll('#kategori-filtreleri .btn');
    const islemButonlariDiv = document.getElementById('islem-butonlari');
    const tercihListesiBaslik = document.getElementById('tercih-listesi-baslik');
    const noteModalElement = document.getElementById('noteModal');
    const noteModal = new bootstrap.Modal(noteModalElement);
    const universiteDataTable = $('#universiteDataTable');
    let dataTableInstance;
    let tumUniversiteler = [];
    let mevcutTercihler = [];
    let duzenlemeModu = false;
    let duzenlenecekListeId = null;
    const themeToggleBtn = document.getElementById('theme-toggle-btn');
    const htmlElement = document.documentElement;
    themeToggleBtn.innerHTML = htmlElement.getAttribute('data-bs-theme') === 'dark' ? '<i class="bi bi-sun-fill"></i>' : '<i class="bi bi-moon-stars-fill"></i>';
    themeToggleBtn.addEventListener('click', () => {
        const newTheme = htmlElement.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
        htmlElement.setAttribute('data-bs-theme', newTheme);
        themeToggleBtn.innerHTML = newTheme === 'dark' ? '<i class="bi bi-sun-fill"></i>' : '<i class="bi bi-moon-stars-fill"></i>';
        localStorage.setItem('theme', newTheme);
    });
    const minPuanInput = document.getElementById('minPuan');
    const maxPuanInput = document.getElementById('maxPuan');
    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
        const min = parseFloat(minPuanInput.value) || 0;
        const max = parseFloat(maxPuanInput.value) || Infinity;
        const puan = parseFloat(data[2]) || 0;
        return (puan >= min && puan <= max);
    });
    minPuanInput.addEventListener('keyup', () => dataTableInstance.draw());
    maxPuanInput.addEventListener('keyup', () => dataTableInstance.draw());
    async function universiteleriYukle(tur = 'hepsi') {
        if (dataTableInstance) { dataTableInstance.destroy(); }
        $('#universite-tablosu').empty();
        if (tumUniversiteler.length === 0) {
            try {
                const response = await fetch('api/get_universities.php');
                tumUniversiteler = await response.json();
                if (tumUniversiteler.error) {
                    alert("Veri Yükleme Hatası: " + tumUniversiteler.error);
                    return;
                }
            } catch (e) {
                alert("Sunucudan veri alınamadı.");
                return;
            }
        }
        let filtrelenmisVeri = (tur === 'hepsi') ? tumUniversiteler : tumUniversiteler.filter(uni => uni.tur === tur);
        dataTableInstance = universiteDataTable.DataTable({
            data: filtrelenmisVeri,
            responsive: true,
            columns: [
                { data: 'universite_adi', title: 'Üniversite' },
                { data: 'bolum_adi', title: 'Bölüm' },
                { data: 'puan_2024', title: 'Puan' },
                {
                    data: null, title: 'İşlemler',
                    render: (data, type, row) => {
                        const universiteAdiEsc = row.universite_adi.replace(/'/g, "\\'");
                        const bolumAdiEsc = row.bolum_adi.replace(/'/g, "\\'");
                        return `<button class="btn btn-primary btn-sm" onclick="terciheEkle(${row.id}, '${universiteAdiEsc}', '${bolumAdiEsc}', ${row.puan_2024})">Ekle</button> <button class="btn btn-info btn-sm text-white" onclick="openNoteModal(${row.id}, '${universiteAdiEsc}')">Not</button>`;
                    },
                    orderable: false, searchable: false
                }
            ],
            language: {
                "sDecimal": ",", "sEmptyTable": "Tabloda herhangi bir veri mevcut değil",
                "sInfo": "_TOTAL_ kayıttan _START_ - _END_ arasındaki kayıtlar gösteriliyor",
                "sInfoEmpty": "Kayıt yok", "sInfoFiltered": "(_MAX_ kayıt içerisinden bulunan)",
                "sInfoPostFix": "", "sInfoThousands": ".", "sLengthMenu": "Sayfada _MENU_ kayıt göster",
                "sLoadingRecords": "Yükleniyor...", "sProcessing": "İşleniyor...", "sSearch": "Ara:",
                "sZeroRecords": "Eşleşen kayıt bulunamadı",
                "oPaginate": { "sFirst": "İlk", "sLast": "Son", "sNext": "Sonraki", "sPrevious": "Önceki" },
                "oAria": { "sSortAscending": ": artan sütun sıralamasını aktifleştir", "sSortDescending": ": azalan sütun sıralamasını aktifleştir" }
            }
        });
        dataTableInstance.draw();
    }
    const listeYukleInput = document.getElementById('liste-yukle-input');
    listeYukleInput.addEventListener('change', (event) => {
        const file = event.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function(e) {
            const content = e.target.result;
            const lines = content.split('\n').filter(line => /^\d+\./.test(line.trim()));
            if (lines.length === 0) { alert('Dosya formatı geçersiz veya boş.'); return; }
            if (!confirm(`${lines.length} üniversite bulundu. Mevcut listeniz temizlenip bu liste yüklenecektir. Onaylıyor musunuz?`)) { return; }
            let yeniTercihler = [];
            lines.forEach(line => {
                const uniAdiBolum = line.replace(/^\d+\.\s*/, '').trim();
                const [uniAdi, bolumAdi] = uniAdiBolum.split(' - ');
                if (uniAdi && bolumAdi) {
                    const eslesenUni = tumUniversiteler.find(uni => uni.universite_adi.trim() === uniAdi.trim() && uni.bolum_adi.trim() === bolumAdi.trim());
                    if(eslesenUni) { yeniTercihler.push(eslesenUni); }
                }
            });
            mevcutTercihler = yeniTercihler;
            tercihListesiniGuncelle();
            alert('Liste başarıyla içeri aktarıldı.');
        };
        reader.readAsText(file);
        event.target.value = '';
    });
    function tercihListesiniGuncelle() {
        tercihSepeti.innerHTML = '';
        if (mevcutTercihler.length === 0) {
            tercihSepeti.innerHTML = '<li class="list-group-item text-center text-muted p-3">Listeniz boş.<br><small>Soldaki tablodan üniversite ekleyin.</small></li>';
            return;
        }
        mevcutTercihler.forEach((tercih, index) => {
            const listItem = document.createElement('li');
            listItem.className = 'list-group-item d-flex justify-content-between align-items-start list-group-item-action';
            listItem.dataset.id = tercih.id;
            listItem.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="bi bi-grip-vertical me-2 handle" style="cursor: grab; font-size: 1.2rem;"></i>
                    <div>
                        <strong class="me-2">${index + 1}.</strong> ${tercih.universite_adi}
                        <br>
                        <small class="text-muted" style="margin-left: 28px;">${tercih.bolum_adi} - <strong>${tercih.puan_2024}</strong></small>
                    </div>
                </div>
                <button class="btn btn-danger btn-sm py-0 px-2" onclick="tercihtenCikar(${index})">X</button>
            `;
            tercihSepeti.appendChild(listItem);
        });
    }
    window.terciheEkle = (id, universite_adi, bolum_adi, puan_2024) => {
        if (mevcutTercihler.length >= 30) { alert('Tercih listeniz dolu! En fazla 30 tercih yapabilirsiniz.'); return; }
        if (mevcutTercihler.some(t => t.id === id)) { alert('Bu bölüm zaten tercih listenizde.'); return; }
        mevcutTercihler.push({ id, universite_adi, bolum_adi, puan_2024 });
        tercihListesiniGuncelle();
    };
    window.tercihtenCikar = (index) => {
        mevcutTercihler.splice(index, 1);
        tercihListesiniGuncelle();
    };
    window.openNoteModal = async function(universite_id, universite_adi) {
        document.getElementById('noteModalUniversityName').textContent = universite_adi;
        const noteTextarea = document.getElementById('noteTextarea');
        noteTextarea.value = 'Yükleniyor...';
        try {
            const response = await fetch(`api/get_note_for_university.php?universite_id=${universite_id}`);
            const data = await response.json();
            noteTextarea.value = data.not_metni || '';
        } catch (error) { noteTextarea.value = ''; }
        document.getElementById('saveNoteBtn').onclick = () => saveNote(universite_id);
        noteModal.show();
    }
    async function saveNote(universite_id) {
        const notMetni = document.getElementById('noteTextarea').value;
        const formData = new FormData();
        formData.append('universite_id', universite_id);
        formData.append('not_metni', notMetni);
        try {
            const response = await fetch('api/save_not.php', { method: 'POST', body: formData });
            const result = await response.json();
            if (result.success) {
                alert(result.message);
                noteModal.hide();
            } else {
                alert('Hata: ' + result.message);
            }
        } catch (error) { alert('Not kaydedilirken bir sunucu hatası oluştu.'); }
    }
    async function duzenlemeModunuBaslat() {
        const liste_id = localStorage.getItem('edit_liste_id');
        const liste_adi = localStorage.getItem('edit_liste_adi');
        if (liste_id && liste_adi) {
            tercihListesiBaslik.textContent = `'${liste_adi}' Listesini Düzenle`;
            islemButonlariDiv.innerHTML = `<div class="mb-2"><input type="text" id="liste-adi" class="form-control" value="${liste_adi}"></div><div class="d-grid gap-2"><button id="guncelle-btn" class="btn btn-success">Listeyi Güncelle</button><button id="iptal-btn" class="btn btn-secondary">İptal</button></div>`;
            const response = await fetch(`api/get_liste_detay.php?liste_id=${liste_id}`);
            const detaylar = await response.json();
            mevcutTercihler = detaylar.map(d => ({id: parseInt(d.id), universite_adi: d.universite_adi, bolum_adi: d.bolum_adi, puan_2024: d.puan_2024}));
            tercihListesiniGuncelle();
            document.getElementById('guncelle-btn').addEventListener('click', () => tercihListesiniGuncelleKaydet(parseInt(liste_id)));
            document.getElementById('iptal-btn').addEventListener('click', () => { window.location.href = 'tercih-listeleri.php'; });
        } else {
            document.getElementById('kaydet-btn').addEventListener('click', tercihListesiniKaydet);
            document.getElementById('indir-btn').addEventListener('click', tercihListesiniIndir);
            tercihListesiniGuncelle();
        }
    }
    async function tercihListesiniKaydet() {
        const listeAdi = document.getElementById('liste-adi').value.trim();
        if (!listeAdi) { alert('Lütfen listenize bir ad verin.'); return; }
        if (mevcutTercihler.length === 0) { alert('Kaydetmek için tercih eklemelisiniz.'); return; }
        const tercihIDleri = mevcutTercihler.map(t => t.id);
        const formData = new FormData();
        formData.append('liste_adi', listeAdi);
        formData.append('tercihler', JSON.stringify(tercihIDleri));
        const response = await fetch('api/save_tercih_listesi.php', { method: 'POST', body: formData });
        const result = await response.json();
        if (result.success) {
            alert(result.message);
            window.location.href = 'tercih-listeleri.php';
        } else {
            alert('Hata: ' + result.message);
        }
    }
    async function tercihListesiniGuncelleKaydet(liste_id) {
        const yeniListeAdi = document.getElementById('liste-adi').value.trim();
        if (!yeniListeAdi) { alert('Liste adı boş olamaz.'); return; }
        const tercihIDleri = mevcutTercihler.map(t => t.id);
        const formData = new FormData();
        formData.append('liste_id', liste_id);
        formData.append('liste_adi', yeniListeAdi);
        formData.append('tercihler', JSON.stringify(tercihIDleri));
        const response = await fetch('api/update_list.php', { method: 'POST', body: formData });
        const result = await response.json();
        if (result.success) {
            alert(result.message);
            window.location.href = 'tercih-listeleri.php';
        } else {
            alert('Hata: ' + result.message);
        }
    }
    function tercihListesiniIndir() {
        if (mevcutTercihler.length === 0) { alert('İndirmek için önce tercih eklemelisiniz.'); return; }
        let dosyaIcerigi = "DGS Tercih Listem\n===================\n\n";
        mevcutTercihler.forEach((tercih, index) => {
            dosyaIcerigi += `${index + 1}. ${tercih.universite_adi} - ${tercih.bolum_adi}\n`;
        });
        const blob = new Blob([dosyaIcerigi], { type: 'text/plain;charset=utf-8' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'tercih_listem.txt';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
    function setActiveLink(activeLink) {
        kategoriFiltreButonlari.forEach(link => link.classList.remove('active'));
        activeLink.classList.add('active');
    }
    kategoriFiltreButonlari.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const tur = e.target.id.replace('-btn', '');
            setActiveLink(e.target);
            universiteleriYukle(tur);
        });
    });
    new Sortable(tercihSepeti, {
        animation: 150,
        handle: '.handle',
        onEnd: function () {
            const guncellenmisSira = Array.from(tercihSepeti.children).map(li => parseInt(li.dataset.id));
            mevcutTercihler.sort((a, b) => guncellenmisSira.indexOf(a.id) - guncellenmisSira.indexOf(b.id));
            tercihListesiniGuncelle();
        }
    });
    universiteleriYukle('hepsi');
    duzenlemeModunuBaslat();
});