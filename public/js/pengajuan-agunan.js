function sesuaikanFormAgunan(item) {
    if (!item) return;
    const jenisSel = item.querySelector('select[name$="[jenis_agunan]"]');
    if (!jenisSel) return;
    const val = jenisSel.value;

    const wrapAlamat = item.querySelector('.wrap-alamat-agunan');
    const wrapNomor = item.querySelector('.wrap-nomor-sertifikat');
    const wrapPemilik = item.querySelector('.wrap-pemilik-agunan');
    const wrapImb = item.querySelector('.wrap-status-imb');
    const wrapLama = item.querySelector('.wrap-lama-kepemilikan');
    const wrapTanah = item.querySelector('.wrap-luas-tanah');
    const wrapBangunan = item.querySelector('.wrap-luas-bangunan');
    const wrapMv = item.querySelector('.wrap-nilai-mv');
    const inputLvPct = item.querySelector('.agunan-lv-pct');

    const setField = (wrap, labelText, placeholderText, isHidden) => {
        if (!wrap) return;
        if (isHidden) {
            wrap.classList.add('hidden');
        } else {
            wrap.classList.remove('hidden');
        }
        const lbl = wrap.querySelector('.lbl-text');
        if (lbl && labelText) lbl.innerText = labelText;
        const inp = wrap.querySelector('input, textarea');
        if (inp && placeholderText !== undefined && placeholderText !== '') inp.placeholder = placeholderText;
    };

    if (val === 'BPKB Mobil' || val.indexOf('BPKB') !== -1 || val.indexOf('Kendaraan') !== -1) {
        // HIDE Luas Tanah, Luas Bangunan, Status IMB
        setField(wrapImb, '', '', true);
        setField(wrapTanah, '', '', true);
        setField(wrapBangunan, '', '', true);

        // Ubah label & placeholder untuk BPKB Kendaraan
        setField(wrapAlamat, 'Merk / Tipe Kendaraan & Plat Nomor', 'Contoh: Toyota Avanza 1.5 Veloz AT 2022 (Plat B 1234 CD)', false);
        setField(wrapNomor, 'Nomor BPKB / No. Rangka Mesin', 'No. BPKB & No. Rangka Mesin', false);
        setField(wrapPemilik, 'Nama Pemilik di BPKB / STNK', 'Sesuai nama tercatat di BPKB/STNK', false);
        setField(wrapLama, 'Tahun Pembuatan Kendaraan', 'Contoh: Th. 2022 / 3 Tahun', false);
        setField(wrapMv, 'Nilai Pasar Kendaraan / MV (Rp)', '0', false);

        if (inputLvPct && (inputLvPct.value === '60' || inputLvPct.value === '100' || !inputLvPct.value)) {
            inputLvPct.value = '50';
            if (typeof hitungCoverageAgunan === 'function') hitungCoverageAgunan();
        }
    } else if (val === 'Bilyet Deposito' || val.indexOf('Deposito') !== -1) {
        // HIDE Luas Tanah, Luas Bangunan, Status IMB
        setField(wrapImb, '', '', true);
        setField(wrapTanah, '', '', true);
        setField(wrapBangunan, '', '', true);

        // Ubah label & placeholder untuk Deposito
        setField(wrapAlamat, 'Bank Penerbit & Kantor Cabang', 'Contoh: Bank BPR SA / Bank Mandiri KC Bandung', false);
        setField(wrapNomor, 'Nomor Bilyet Deposito & No. Rekening', 'No. Seri Bilyet Deposito & No. Rekening', false);
        setField(wrapPemilik, 'Nama Pemilik Rekening Bilyet', 'Sesuai nama di Bilyet Deposito / KTP', false);
        setField(wrapLama, 'Tenor & Tanggal Jatuh Tempo', 'Contoh: 12 Bulan / Jatuh Tempo 15 Nov 2027', false);
        setField(wrapMv, 'Nominal Pokok Deposito (Rp)', '0', false);

        if (inputLvPct && (inputLvPct.value === '60' || inputLvPct.value === '50' || !inputLvPct.value)) {
            inputLvPct.value = '100';
            if (typeof hitungCoverageAgunan === 'function') hitungCoverageAgunan();
        }
    } else if (val === 'SHM Sarusun' || val.indexOf('Sarusun') !== -1 || val.indexOf('Apartemen') !== -1) {
        // HIDE Luas Tanah, TAMPILKAN Luas Bangunan & IMB/SLF
        setField(wrapTanah, '', '', true);
        setField(wrapBangunan, 'Luas Unit Sarusun (mÂ²)', 'Contoh: 45', false);
        setField(wrapImb, 'Status Sertifikat Laik Fungsi (SLF)', '', false);

        setField(wrapAlamat, 'Nama Apartemen, Tower, Unit & Alamat', 'Nama Apartemen, Tower, No. Lantai/Unit, Alamat Lengkap', false);
        setField(wrapNomor, 'Nomor SHM Sarusun', 'No. Sertifikat Hak Milik Sarusun', false);
        setField(wrapPemilik, 'Nama Pemilik di Sertifikat', 'Sesuai nama di sertifikat', false);
        setField(wrapLama, 'Lama Kepemilikan Unit', 'Contoh: 5 Tahun', false);
        setField(wrapMv, 'Nilai Pasar / MV (Rp)', '0', false);
    } else {
        // SHM / SHGB / Default Properti
        setField(wrapTanah, 'Luas Tanah (mÂ²)', 'Contoh: 120', false);
        setField(wrapBangunan, 'Luas Bangunan (mÂ²)', 'Contoh: 85', false);
        setField(wrapImb, 'Status IMB / PBG', '', false);

        setField(wrapAlamat, 'Alamat Lengkap Objek Jaminan', 'Alamat lokasi aset jaminan', false);
        setField(wrapNomor, 'Nomor Sertifikat (SHM / SHGB)', 'No. Sertifikat / BPKB', false);
        setField(wrapPemilik, 'Nama Pemilik Agunan', 'Sesuai nama di sertifikat', false);
        setField(wrapLama, 'Lama Kepemilikan', 'Contoh: 8 Tahun', false);
        setField(wrapMv, 'Nilai Pasar / MV (Rp)', '0', false);
    }
}
window.sesuaikanFormAgunan = sesuaikanFormAgunan;

function inisialisasiSemuaAgunan() {
    document.querySelectorAll('.agunan-item').forEach(function(item) {
        sesuaikanFormAgunan(item);
    });
    document.querySelectorAll('select[name$="[jenis_agunan]"]').forEach(function(sel) {
        sel.onchange = function() {
            sesuaikanFormAgunan(this.closest('.agunan-item'));
        };
    });
}

document.addEventListener('DOMContentLoaded', inisialisasiSemuaAgunan);
setTimeout(inisialisasiSemuaAgunan, 100);
setTimeout(inisialisasiSemuaAgunan, 500);

// Hook ke tambahBarisAgunan jika dipanggil
if (typeof window.tambahBarisAgunan === 'function') {
    var _oldTambah = window.tambahBarisAgunan;
    window.tambahBarisAgunan = function() {
        _oldTambah();
        setTimeout(inisialisasiSemuaAgunan, 50);
    };
}