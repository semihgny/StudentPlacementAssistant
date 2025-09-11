document.addEventListener('DOMContentLoaded', () => {
    const notlarListesiDiv = document.getElementById('notlar-listesi');
    window.notlariYukle = async function() {
        notlarListesiDiv.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Yükleniyor...</span></div></div>';
        try {
            const response = await fetch('api/get_notlar.php');
            const notlar = await response.json();
            notlarListesiDiv.innerHTML = '';
            if (notlar.length > 0) {
                notlar.forEach(not => {
                    const notMetniHtml = not.not_metni.replace(/\n/g, '<br>');
                    const notKarti = `
                        <div class="card shadow-sm mb-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">${not.universite_adi}</h5>
                                <small class="text-muted">${new Date(not.kayit_tarihi).toLocaleString('tr-TR')}</small>
                            </div>
                            <div class="card-body">
                                <p class="card-text" id="not-metin-${not.not_id}">${notMetniHtml}</p>
                            </div>
                            <div class="card-footer text-end">
                                <button class="btn btn-secondary btn-sm" onclick="notDuzenle(${not.not_id})">Düzenle</button>
                                <button class="btn btn-danger btn-sm" onclick="notSil(${not.not_id})">Sil</button>
                            </div>
                        </div>
                    `;
                    notlarListesiDiv.innerHTML += notKarti;
                });
            } else {
                notlarListesiDiv.innerHTML = '<div class="alert alert-info">Henüz kaydedilmiş bir notunuz yok.</div>';
            }
        } catch (error) {
            console.error('Notlar yüklenirken hata:', error);
            notlarListesiDiv.innerHTML = '<div class="alert alert-danger">Notlar yüklenirken bir hata oluştu.</div>';
        }
    }
    window.notSil = async function(not_id) {
        if (confirm('Bu notu silmek istediğinizden emin misiniz?')) {
            const formData = new FormData();
            formData.append('not_id', not_id);
            const response = await fetch('api/delete_note.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            if(result.success) {
                alert('Not başarıyla silindi.');
                notlariYukle(); 
            } else {
                alert('Hata: ' + result.message);
            }
        }
    };
    window.notDuzenle = async function(not_id) {
        const notMetinElementi = document.getElementById(`not-metin-${not_id}`);
        const mevcutMetin = notMetinElementi.innerHTML.replace(/<br\s*[\/]?>/gi, "\n");
        const yeniMetin = prompt("Notu düzenleyin:", mevcutMetin);
        if (yeniMetin !== null && yeniMetin.trim() !== mevcutMetin.trim()) {
            const formData = new FormData();
            formData.append('not_id', not_id);
            formData.append('not_metni', yeniMetin);
            const response = await fetch('api/update_note.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            if(result.success) {
                alert('Not başarıyla güncellendi.');
                notlariYukle(); 
            } else {
                alert('Hata: ' + result.message);
            }
        }
    };
    notlariYukle();
});