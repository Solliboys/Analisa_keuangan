// ========================================================
// KOMPRESI GAMBAR CLIENT-SIDE (HTML5 Canvas)
// ========================================================
async function kompresBerkasJikaGambar(file, maxWidth = 1600, quality = 0.75) {
    if (!file) return null;
    const isImage = (file.type && file.type.startsWith('image/')) || /\.(jpe?g|png|webp|bmp)$/i.test(file.name);
    if (!isImage) return file; // Non-gambar (PDF, TXT) dikembalikan apa adanya

    return new Promise((resolve) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = new Image();
            img.onload = function() {
                let w = img.width;
                let h = img.height;
                if (w > maxWidth || h > maxWidth) {
                    if (w > h) {
                        h = Math.round((h * maxWidth) / w);
                        w = maxWidth;
                    } else {
                        w = Math.round((w * maxWidth) / h);
                        h = maxWidth;
                    }
                }
                const canvas = document.createElement('canvas');
                canvas.width = w;
                canvas.height = h;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, w, h);
                canvas.toBlob(function(blob) {
                    if (blob && blob.size < file.size) {
                        const newName = file.name.replace(/\.[^/.]+$/, ".jpg");
                        const compressedFile = new File([blob], newName, {
                            type: 'image/jpeg',
                            lastModified: Date.now()
                        });
                        resolve(compressedFile);
                    } else {
                        resolve(file);
                    }
                }, 'image/jpeg', quality);
            };
            img.onerror = function() { resolve(file); };
            img.src = e.target.result;
        };
        reader.onerror = function() { resolve(file); };
        reader.readAsDataURL(file);
    });
}
window.kompresBerkasJikaGambar = kompresBerkasJikaGambar;

function formatRupiahInput(angka) {
    if (!angka) return '0';
    let number_string = angka.toString().replace(/[^,\d]/g, '');
    let split = number_string.split(',');
    let sisa = split[0].length % 3;
    let rupiah = split[0].substr(0, sisa);
    let ribuan = split[0].substr(sisa).match(/\d{3}/gi);
    if (ribuan) {
        let separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }
    return split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
}
window.formatRupiahInput = formatRupiahInput;

window.CAN_EDIT_SLIK = window.CAN_EDIT_SLIK ?? true;
window.PETUGAS_SLIK_AKTIF = window.PETUGAS_SLIK_AKTIF ?? 0;
window.slikDokumenUploaded = window.slikDokumenUploaded || {};
window.daftarPermohonanSlikArray = window.daftarPermohonanSlikArray || [];

/**
 * Dialog Pop-up Konfirmasi Kirim Pengajuan SLIK ke Petugas SLIK
 */
async function konfirmasiKirimPengajuanKePetugasSlik(idxTarget = null) {
    const elNama1 = document.getElementById("nama_debitur");
    const elNama2 = document.getElementById("nama_debitur_tab2");
    const namaDebitur = (elNama1?.value || elNama2?.value || window.NAMA_DEBITUR_VAL || "Debitur").trim().toUpperCase();

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Kirim Pengajuan SLIK ke Petugas SLIK?',
            html: '<div class="text-left text-xs text-slate-600 space-y-3 pt-1">' +
                  '  <div class="p-3 bg-blue-50 border border-blue-200 rounded-xl text-blue-900 leading-relaxed">' +
                  '    Permohonan pengecekan riwayat fasilitas kredit debitur <b>' + namaDebitur + '</b> akan dikirimkan langsung ke antrean kerja <b>Petugas Verifikator SLIK OJK</b>.' +
                  '  </div>' +
                  '  <div class="text-[11.5px] text-slate-600 leading-relaxed space-y-1.5 pl-1">' +
                  '    <p>• <b>Petugas SLIK</b> akan melakukan penarikan data iDeb resmi dan mengunggah berkas hasil verifikasi ke sistem.</p>' +
                  '    <p>• Sebagai Account Officer (Pengusul), Anda <b>dapat langsung melanjutkan pengisian formulir ke tahap berikutnya</b> tanpa terhambat.</p>' +
                  '  </div>' +
                  '</div>',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Kirim ke Petugas SLIK',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then(async (result) => {
            if (result.isConfirmed) {
                await eksekusiKirimKePetugasSlik(idxTarget);
            }
        });
    } else {
        if (confirm(`Kirim permohonan pengecekan SLIK untuk ${namaDebitur} ke Petugas SLIK OJK?`)) {
            await eksekusiKirimKePetugasSlik(idxTarget);
        }
    }
}
window.konfirmasiKirimPengajuanKePetugasSlik = konfirmasiKirimPengajuanKePetugasSlik;

/**
 * Eksekusi AJAX pengiriman permohonan SLIK ke Petugas SLIK
 */
async function eksekusiKirimKePetugasSlik(idxTarget = null) {
    const kid = document.getElementById('kredit_id')?.value || (window.KREDIT_ID_VAL || '');
    const baseUrl = (typeof getBaseUrl === 'function') ? getBaseUrl() : (window.BASE_URL || '');
    
    // Tampilkan loading modal
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Mengirim Pengajuan...',
            text: 'Meneruskan permohonan ke antrean Petugas SLIK OJK',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });
    }

    try {
        const formData = new FormData();
        formData.append('kredit_id', kid);
        formData.append('daftar_permohonan_slik', JSON.stringify(window.daftarPermohonanSlikArray || []));

        const res = await fetch(baseUrl + '/pengajuan/kirim-ke-petugas-slik', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        });

        const data = await res.json();
        if (data.status) {
            // Update input status slik di form
            const inpStatusSlik = document.getElementById('inputStatusSlik');
            if (inpStatusSlik) inpStatusSlik.value = 'Menunggu Pengecekan';
            const hiddenStatusSlik = document.getElementById('hidden_status_slik');
            if (hiddenStatusSlik) hiddenStatusSlik.value = 'Menunggu Pengecekan';

            // Update badge di banner
            const badgeAlert = document.getElementById('badgeAlertSlik');
            if (badgeAlert) {
                badgeAlert.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-100 text-blue-800';
                badgeAlert.innerText = 'Antrean Petugas SLIK';
            }
            const descAlert = document.getElementById('deskripsiAlertSlik');
            if (descAlert) {
                descAlert.innerHTML = 'Permohonan SLIK debitur telah berhasil dikirimkan ke <strong>Petugas Verifikator SLIK OJK</strong>. Anda dapat langsung melanjutkan pengisian formulir ke tahap berikutnya tanpa terhambat.';
            }
            const btnBanner = document.getElementById('btnAksiBannerSlik');
            if (btnBanner) btnBanner.innerText = 'Kirim Ulang ke Petugas';

            const textBtnHeader = document.getElementById('textBtnKirimSlikHeader');
            if (textBtnHeader) textBtnHeader.innerText = 'Antrean Petugas SLIK';

            // Update badge status di setiap baris tabel
            document.querySelectorAll('#bodySlikIdeb [id^="status-badge-cell-"]').forEach(cell => {
                cell.innerHTML = '<span class="inline-block bg-[#0284c7] text-white font-bold text-[10px] px-2 py-0.5 rounded shadow-2xs">Menunggu Pengecekan</span>';
            });

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Pengajuan SLIK Terkirim!',
                    text: data.message || 'Permohonan pemeriksaan SLIK berhasil masuk antrean Petugas SLIK OJK.',
                    confirmButtonColor: '#2563eb',
                    confirmButtonText: 'Lanjutkan Pengisian Form'
                });
            }
        } else {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Mengirim',
                    text: data.message || 'Terjadi kesalahan sistem.'
                });
            }
        }
    } catch (err) {
        console.error('Error kirim permohonan ke petugas slik:', err);
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Gangguan Jaringan',
                text: 'Gagal menghubungi server untuk mengirim permohonan.'
            });
        }
    }
}
window.eksekusiKirimKePetugasSlik = eksekusiKirimKePetugasSlik;

function pesanPetugasSlikAktif() {
    konfirmasiKirimPengajuanKePetugasSlik();
}
window.pesanPetugasSlikAktif = pesanPetugasSlikAktif;

// ========================================================
// SINKRONISASI OTOMATIS DATA DEBITUR DARI TAB SEBELUMNYA
// ========================================================
function sinkronisasiDataDebiturKeSlik() {
    const elNama1 = document.getElementById("nama_debitur");
    const elNama2 = document.getElementById("nama_debitur_tab2");
    let namaVal = (elNama1?.value || elNama2?.value || "").trim().toUpperCase();

    const elNik = document.getElementById("npwp_pribadi");
    let nikVal = (elNik?.value || "").trim();

    const elJd = document.getElementById("jenis_debitur");
    const jdVal = (elJd?.value || "Individu").toLowerCase();
    const entitas = (jdVal.includes("badan") || jdVal.includes("usaha") || jdVal.includes("pt") || jdVal.includes("cv")) ? "B" : "I";

    const cellNama = document.getElementById("cell-nama-0");
    if (cellNama && namaVal) {
        cellNama.textContent = namaVal;
    }

    const cellNoid = document.getElementById("cell-noid-0");
    if (cellNoid && nikVal) {
        cellNoid.textContent = nikVal;
    }

    const cellReff = document.getElementById("cell-reff-0");
    const kid = document.getElementById("kredit_id")?.value || (window.KREDIT_ID_VAL || '1');
    const tahun = (window.TGL_TAHUN_VAL || String(new Date().getFullYear()));
    const baseReff = kid + "/BPR-SA/IDEB/" + tahun;
    if (cellReff) {
        cellReff.textContent = `${baseReff}|01|${entitas}|${nikVal || "-"}`;
    }

    const hiddenNik = document.getElementById("hidden_slik_nik");
    if (hiddenNik && nikVal) {
        hiddenNik.value = nikVal;
    }
}
window.sinkronisasiDataDebiturKeSlik = sinkronisasiDataDebiturKeSlik;

function pasangListenerSinkronisasiSlik() {
    sinkronisasiDataDebiturKeSlik();
    ["nama_debitur", "nama_debitur_tab2", "npwp_pribadi", "jenis_debitur"].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener("input", sinkronisasiDataDebiturKeSlik);
            el.addEventListener("change", sinkronisasiDataDebiturKeSlik);
        }
    });
}

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", pasangListenerSinkronisasiSlik);
} else {
    pasangListenerSinkronisasiSlik();
}
setTimeout(sinkronisasiDataDebiturKeSlik, 500);

let currentActiveSlikIndex = 0;
let permohonanCount = (Array.isArray(window.daftarPermohonanSlikArray) && window.daftarPermohonanSlikArray.length > 0)
    ? Math.max(...window.daftarPermohonanSlikArray.map(r => parseInt(r.index) || 0)) + 1
    : 0;

// Filter Pencarian Tabel SLIK
function filterTabelSlik(query) {
    const q = (query || '').toLowerCase().trim();
    const rows = document.querySelectorAll('#bodySlikIdeb tr.slik-row-item');
    rows.forEach(tr => {
        const text = tr.innerText.toLowerCase();
        tr.style.display = text.includes(q) ? '' : 'none';
    });
}
window.filterTabelSlik = filterTabelSlik;

// Download Berkas TXT di Browser
function downloadTxtContent(filename, text) {
    const blob = new Blob([text], { type: 'text/plain;charset=utf-8' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    setTimeout(() => {
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }, 200);
}
window.downloadTxtContent = downloadTxtContent;

// Toggle Checkbox Pilih Semua
function toggleSelectAllSlik(masterCheckbox) {
    const isChecked = masterCheckbox.checked;
    document.querySelectorAll('.slik-select-item').forEach(cb => {
        cb.checked = isChecked;
    });
}
window.toggleSelectAllSlik = toggleSelectAllSlik;

// Handler Checkbox "Nama Sama dengan Pemohon"
function toggleNamaSamaPemohon(checkbox) {
    const elNama = document.getElementById('inputSlikNama');
    const elNoId = document.getElementById('inputSlikNoId');
    const elKeterkaitan = document.getElementById('inputSlikKeterkaitan');
    if (checkbox.checked) {
        const namaPemohon = (document.getElementById('nama_debitur')?.value || document.getElementById('nama_debitur_tab2')?.value || document.getElementById('cell-nama-0')?.innerText || '').trim().toUpperCase();
        const noIdPemohon = (document.getElementById('npwp_pribadi')?.value || document.getElementById('cell-noid-0')?.innerText || '').trim();
        if (elNama && namaPemohon) elNama.value = namaPemohon;
        if (elNoId && noIdPemohon && noIdPemohon !== '-') elNoId.value = noIdPemohon;
        if (elKeterkaitan) elKeterkaitan.value = 'Pemohon';
    } else {
        if (elNama) elNama.value = '';
        if (elNoId) elNoId.value = '';
        if (elKeterkaitan) elKeterkaitan.value = '';
    }
}
window.toggleNamaSamaPemohon = toggleNamaSamaPemohon;

// Handler Upload File di Modal Tambah SLIK
function handleModalFileSlik(input, type) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const sizeKb = (file.size / 1024).toFixed(1) + ' KB';
        if (type === 'identitas') {
            const label = document.getElementById('labelFileIdentitas');
            const sub = document.getElementById('subFileIdentitas');
            if (label) {
                label.innerText = file.name;
                label.title = file.name;
                label.style.maxWidth = '100%';
                label.style.overflow = 'hidden';
                label.style.textOverflow = 'ellipsis';
                label.style.whiteSpace = 'nowrap';
            }
            if (sub) sub.innerText = sizeKb + ' • Siap diunggah';
        } else if (type === 'permohonan') {
            const label = document.getElementById('labelFilePermohonan');
            const sub = document.getElementById('subFilePermohonan');
            if (label) {
                label.innerText = file.name;
                label.title = file.name;
                label.style.maxWidth = '100%';
                label.style.overflow = 'hidden';
                label.style.textOverflow = 'ellipsis';
                label.style.whiteSpace = 'nowrap';
            }
            if (sub) sub.innerText = sizeKb + ' • Siap diunggah';
        }
    }
}
window.handleModalFileSlik = handleModalFileSlik;

// Modal Tambah Permohonan SLIK
function bukaModalTambahPermohonanSlik() {
    const modal = document.getElementById('modalTambahPermohonanSlik');
    if (modal) {
        const elNama = document.getElementById('inputSlikNama');
        const elNoId = document.getElementById('inputSlikNoId');
        const elCheck = document.getElementById('checkNamaSamaPemohon');
        const elKeterkaitan = document.getElementById('inputSlikKeterkaitan');
        const labelId = document.getElementById('labelFileIdentitas');
        const subId = document.getElementById('subFileIdentitas');
        const labelPerm = document.getElementById('labelFilePermohonan');
        const subPerm = document.getElementById('subFilePermohonan');

        if (elNama) elNama.value = '';
        if (elNoId) elNoId.value = '';
        if (elCheck) elCheck.checked = false;
        if (elKeterkaitan) elKeterkaitan.value = '';
        if (labelId) labelId.innerText = 'Drag and drop a file here or click';
        if (subId) subId.innerText = 'Format PDF / Gambar';
        if (labelPerm) labelPerm.innerText = 'Drag and drop a file here or click';
        if (subPerm) subPerm.innerText = 'Format PDF / TXT';

        const inpId = document.getElementById('inputSlikFileIdentitas');
        const inpPerm = document.getElementById('inputSlikFilePermohonan');
        if (inpId) inpId.value = '';
        if (inpPerm) inpPerm.value = '';

        modal.classList.remove('hidden');
    }
}
window.bukaModalTambahPermohonanSlik = bukaModalTambahPermohonanSlik;

function tutupModalTambahPermohonanSlik() {
    const modal = document.getElementById('modalTambahPermohonanSlik');
    if (modal) modal.classList.add('hidden');
}
window.tutupModalTambahPermohonanSlik = tutupModalTambahPermohonanSlik;

// ========================================================
// SIMPAN PERMOHONAN SLIK (Auto-Upload & Auto-Save ke DB)
// ========================================================
async function simpanTambahPermohonanSlik() {
    const nama = (document.getElementById('inputSlikNama')?.value || '').trim().toUpperCase();
    let noId = (document.getElementById('inputSlikNoId')?.value || '').trim().replace(/\D/g, '');
    const keterkaitan = document.getElementById('inputSlikKeterkaitan')?.value || 'Pasangan';
    const entitas = (keterkaitan.toLowerCase().includes('badan') || keterkaitan.toLowerCase().includes('perusahaan') || keterkaitan.toLowerCase().includes('pt')) ? 'B' : 'I';

    if (!nama) {
        alert('Harap isi Nama subjek permohonan SLIK.');
        return;
    }
    if (!noId) {
        alert('Harap isi Nomor Identitas (NIK/NPWP).');
        return;
    }
    if (!keterkaitan) {
        alert('Harap pilih Keterkaitan subjek.');
        return;
    }

    // ---- EDIT MODE: update existing entry ----
    if (window.slikEditingIdx !== undefined && window.slikEditingIdx !== null) {
        const editIdx = parseInt(window.slikEditingIdx);
        window.slikEditingIdx = null;

        // Upload new files if any
        const inpFileIdE = document.getElementById('inputSlikFileIdentitas');
        const inpFilePermE = document.getElementById('inputSlikFilePermohonan');
        const fileIdE = inpFileIdE?.files?.[0] || null;
        const filePermE = inpFilePermE?.files?.[0] || null;
        let uploadedDocsE = { identitas: null, permohonan: null };

        if (fileIdE || filePermE) {
            const fData = new FormData();
            const compFileIdE = fileIdE ? await kompresBerkasJikaGambar(fileIdE) : null;
            const compFilePermE = filePermE ? await kompresBerkasJikaGambar(filePermE) : null;
            if (compFileIdE) fData.append('file_identitas', compFileIdE);
            if (compFilePermE) fData.append('file_permohonan', compFilePermE);
            const baseUrlE = (typeof getBaseUrl === 'function') ? getBaseUrl() : (window.BASE_URL || '');
            try {
                const resE = await fetch(baseUrlE + '/pengajuan/upload-dokumen-slik', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: fData
                });
                const resDataE = await resE.json();
                if (resDataE.status && resDataE.files) {
                    if (resDataE.files.identitas) uploadedDocsE.identitas = resDataE.files.identitas;
                    if (resDataE.files.permohonan) uploadedDocsE.permohonan = resDataE.files.permohonan;
                }
            } catch(e2) { console.warn('Upload edit slik gagal:', e2); }
        }

        // Update in array
        const existing = window.daftarPermohonanSlikArray.find(r => parseInt(r.index) === editIdx);
        if (existing) {
            existing.nama = nama;
            existing.no_id = noId;
            existing.keterkaitan = keterkaitan;
            if (uploadedDocsE.identitas) existing.file_identitas = uploadedDocsE.identitas;
            if (uploadedDocsE.permohonan) existing.file_permohonan = uploadedDocsE.permohonan;
        }

        // Update slikDokumenUploaded
        if (!window.slikDokumenUploaded) window.slikDokumenUploaded = {};
        if (!window.slikDokumenUploaded[editIdx]) window.slikDokumenUploaded[editIdx] = { nama: nama };
        else window.slikDokumenUploaded[editIdx].nama = nama;
        if (uploadedDocsE.identitas) window.slikDokumenUploaded[editIdx].identitas = uploadedDocsE.identitas;
        if (uploadedDocsE.permohonan) window.slikDokumenUploaded[editIdx].permohonan = uploadedDocsE.permohonan;

        // Update DOM cells
        const namaCell = document.getElementById('cell-nama-' + editIdx);
        const noIdCell = document.getElementById('cell-noid-' + editIdx);
        if (namaCell) namaCell.innerHTML = '<div class="flex items-center gap-1.5"><span>' + nama + '</span><span class="text-[9px] font-extrabold px-1.5 py-0.5 rounded bg-indigo-50 text-indigo-700 border border-indigo-200/60 uppercase">' + keterkaitan + '</span></div>';
        if (noIdCell) noIdCell.textContent = noId;

        // Update hidden input & save
        const hiddenInputE = document.getElementById('hidden_daftar_permohonan_slik');
        if (hiddenInputE) hiddenInputE.value = JSON.stringify(window.daftarPermohonanSlikArray);

        tutupModalTambahPermohonanSlik();

        if (typeof simpanForm === 'function') simpanForm(false, false);
        if (typeof Swal !== 'undefined') {
            Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Data permohonan SLIK berhasil diperbarui!', showConfirmButton: false, timer: 2000 });
        }
        return;
    }
    // ---- END EDIT MODE ----

    const btnSimpan = document.querySelector('#modalTambahPermohonanSlik button[onclick="simpanTambahPermohonanSlik()"]');
    const originalText = btnSimpan ? btnSimpan.innerHTML : 'Simpan';
    if (btnSimpan) {
        btnSimpan.disabled = true;
        btnSimpan.innerHTML = '<span class="inline-block animate-spin mr-1">&#9696;</span> Menyimpan...';
    }

    const inpFileId = document.getElementById('inputSlikFileIdentitas');
    const inpFilePerm = document.getElementById('inputSlikFilePermohonan');
    const fileId = inpFileId?.files?.[0] || null;
    const filePerm = inpFilePerm?.files?.[0] || null;

        // Tutup modal langsung seketika agar tidak menunggu proses upload
    tutupModalTambahPermohonanSlik();

    let uploadedDocs = { identitas: null, permohonan: null };

    // Upload files ke backend agar tersimpan permanen di server (dengan kompresi)
    if (fileId || filePerm) {
        const formData = new FormData();
        const compFileId = fileId ? await kompresBerkasJikaGambar(fileId) : null;
        const compFilePerm = filePerm ? await kompresBerkasJikaGambar(filePerm) : null;
        if (compFileId) formData.append('file_identitas', compFileId);
        if (compFilePerm) formData.append('file_permohonan', compFilePerm);

        const baseUrl = (typeof getBaseUrl === 'function') ? getBaseUrl() : (window.BASE_URL || '');
        try {
            const res = await fetch(baseUrl + '/pengajuan/upload-dokumen-slik', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });
            const resData = await res.json();
            if (resData.status && resData.files) {
                if (resData.files.identitas) uploadedDocs.identitas = resData.files.identitas;
                if (resData.files.permohonan) uploadedDocs.permohonan = resData.files.permohonan;
            }
        } catch (e) {
            console.warn('Upload berkas slik gagal:', e);
            if (fileId) {
                uploadedDocs.identitas = {
                    name: fileId.name,
                    size: (fileId.size / 1024).toFixed(1) + ' KB',
                    url: URL.createObjectURL(fileId)
                };
            }
            if (filePerm) {
                uploadedDocs.permohonan = {
                    name: filePerm.name,
                    size: (filePerm.size / 1024).toFixed(1) + ' KB',
                    url: URL.createObjectURL(filePerm)
                };
            }
        }
    }

    permohonanCount++;
    const newIdx = permohonanCount - 1;

    const kodeUrut = String(permohonanCount).padStart(2, '0');
    const kid = document.getElementById('kredit_id')?.value || (window.KREDIT_ID_VAL || '1');
    const tahun = (window.TGL_TAHUN_VAL || String(new Date().getFullYear()));
    const noReff = `${kid}/BPR-SA/IDEB/${tahun}|${kodeUrut}|${entitas}|${noId}`;

    const bulanList = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    const d = new Date();
    const tglSekarang = `${d.getDate()} ${bulanList[d.getMonth()]} ${d.getFullYear()}`;
    const pengusulNama = document.getElementById('cell-pengusul-0')?.innerText.trim() || 'Staff Pengusul Kredit';

    const rowData = {
        index: newIdx,
        nama: nama,
        no_id: noId,
        keterkaitan: keterkaitan,
        no_reff: noReff,
        tanggal: tglSekarang,
        status: 'Belum Terbit',
        hasil: 'Menunggu',
        pengusul: pengusulNama,
        file_identitas: uploadedDocs.identitas,
        file_permohonan: uploadedDocs.permohonan
    };

    window.daftarPermohonanSlikArray.push(rowData);
    window.slikDokumenUploaded[newIdx] = {
        nama: nama,
        identitas: uploadedDocs.identitas,
        permohonan: uploadedDocs.permohonan
    };

    // Update hidden input agar masuk form submission
    const hiddenInput = document.getElementById('hidden_daftar_permohonan_slik');
    if (hiddenInput) {
        hiddenInput.value = JSON.stringify(window.daftarPermohonanSlikArray);
    }

    const tbody = document.getElementById('bodySlikIdeb');
    if (tbody) {
        const rowHtml = `
            <tr class="hover:bg-slate-50/70 transition slik-row-item" data-index="${newIdx}" id="slik-row-${newIdx}">
                <td class="py-2.5 px-2 text-center whitespace-nowrap">
                    <div class="flex items-center justify-center">
                        <input type="checkbox" class="slik-select-item w-3.5 h-3.5 rounded text-blue-600 border-slate-300 focus:ring-blue-500 cursor-pointer" data-index="${newIdx}" value="${newIdx}" checked>
                    </div>
                </td>
                <td class="py-2.5 px-2 whitespace-nowrap" id="status-badge-cell-${newIdx}">
                    <span class="inline-block bg-[#f59e0b] text-white font-bold text-[10px] px-2 py-0.5 rounded shadow-2xs">
                        Belum Terbit
                    </span>
                </td>
                <td class="py-2.5 px-2 text-[11px] font-semibold text-slate-700 max-w-[130px] truncate" id="cell-reff-${newIdx}" title="${noReff}">
                    ${noReff}
                </td>
                <td class="py-2.5 px-2 whitespace-nowrap text-[11px] text-slate-600" id="cell-tgl-${newIdx}">
                    ${tglSekarang}
                </td>
                <td class="py-2.5 px-2 whitespace-nowrap text-[11px] font-semibold text-slate-800" id="cell-noid-${newIdx}">
                    ${noId}
                </td>
                <td class="py-2.5 px-2 whitespace-nowrap text-[11px] font-bold text-slate-900" id="cell-nama-${newIdx}">
                    <div class="flex items-center gap-1">
                        <span>${nama}</span>
                        <span class="text-[9px] font-bold px-1 py-0.2 rounded bg-indigo-50 text-indigo-700 border border-indigo-200/60 uppercase">${keterkaitan}</span>
                    </div>
                </td>
                <td class="py-2.5 px-2 whitespace-nowrap text-center" id="hasil-badge-cell-${newIdx}">
                    <span class="inline-block px-2 py-0.5 rounded-full border border-slate-300 text-slate-500 text-[10px] font-bold bg-slate-50/50">
                        Menunggu
                    </span>
                </td>
                <td class="py-2.5 px-2 text-center whitespace-nowrap">
                    <button type="button" onclick="bukaModalRingkasanSlik(${newIdx})" 
                            class="inline-flex items-center gap-1 px-2.5 py-1 bg-white border border-[#4834d4] hover:bg-indigo-50 text-[#4834d4] rounded-md text-[11px] font-semibold transition cursor-pointer shadow-2xs">
                        <svg class="w-3.5 h-3.5 text-[#4834d4]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        <span>Lihat</span>
                    </button>
                </td>
                <td class="py-2.5 px-2 text-center whitespace-nowrap">
                    <button type="button" onclick="bukaModalPdfSlik(${newIdx})" 
                            class="inline-flex items-center gap-1 px-2.5 py-1 bg-white border border-[#4834d4] hover:bg-indigo-50 text-[#4834d4] rounded-md text-[11px] font-semibold transition cursor-pointer shadow-2xs">
                        <svg class="w-3.5 h-3.5 text-[#4834d4]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        <span>Lihat</span>
                    </button>
                </td>
                <td class="py-2.5 px-2 text-[11px] text-slate-600 max-w-[100px] truncate" id="cell-pengusul-${newIdx}" title="${pengusulNama}">
                    ${pengusulNama}
                </td>
                <td class="py-2.5 px-2 text-center whitespace-nowrap">
                    <div class="inline-flex items-center gap-1 justify-center">
                        <button type="button" onclick="bukaModalLihatDokumenSlik(${newIdx})" 
                                class="p-1 text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 rounded-md transition cursor-pointer border border-blue-200 shadow-2xs" 
                                title="Lihat Dokumen yang Diunggah (Identitas & Permohonan)">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </button>
                        <button type="button" onclick="toggleDropdownProsesSlik(event,${newIdx})"
                                class="inline-flex items-center gap-1 px-2.5 py-1 bg-[#0a0082] hover:bg-[#07005e] text-white rounded-md text-[11px] font-bold transition shadow-xs cursor-pointer"
                                data-proses-idx="${newIdx}">
                            <span>Proses</span>
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <button type="button" onclick="hapusBarisPermohonanSlik(${newIdx})" 
                                class="p-1 text-rose-500 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 rounded-md transition cursor-pointer border border-rose-200" title="Hapus permohonan ini">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                </td>
            </tr>
        `;

        const emptyStateSlik = document.getElementById('emptyStateSlik');
        if (emptyStateSlik) emptyStateSlik.remove();

        tbody.insertAdjacentHTML('beforeend', rowHtml);
    }

    tutupModalTambahPermohonanSlik();

    if (btnSimpan) {
        btnSimpan.disabled = false;
        btnSimpan.innerHTML = originalText;
    }

    // Auto Simpan ke Database
    if (typeof simpanForm === 'function') {
        simpanForm(false, false);
    }

    const isPetugasSlikWajib = (window.PETUGAS_SLIK_AKTIF === 1 && !window.CAN_EDIT_SLIK);

    if (isPetugasSlikWajib) {
        // Ketika fitur slik harus petugas slik yang upload, langsung munculkan pop-up kirim pengajuan ke petugas slik
        setTimeout(() => {
            konfirmasiKirimPengajuanKePetugasSlik(newIdx);
        }, 350);
    } else {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: `Permohonan SLIK (${nama}) berhasil disimpan!`,
                showConfirmButton: false,
                timer: 2000
            });
        }
    }
}
window.simpanTambahPermohonanSlik = simpanTambahPermohonanSlik;

// ========================================================
// SPLIT BUTTON DROPDOWN — PROSES / EDIT DATA UPLOAD
// ========================================================

var _slikDropdownEl = null;
var _slikDropdownIdx = null;

function _closeSlikDropdown() {
    if (_slikDropdownEl && _slikDropdownEl.parentNode) {
        _slikDropdownEl.parentNode.removeChild(_slikDropdownEl);
    }
    _slikDropdownEl = null;
    _slikDropdownIdx = null;
}

function tutupDropdownProsesSlik(idx) {
    _closeSlikDropdown();
}
window.tutupDropdownProsesSlik = tutupDropdownProsesSlik;

function toggleDropdownProsesSlik(e, idx) {
    if (e) {
        if (e.stopPropagation) e.stopPropagation();
        if (e.preventDefault) e.preventDefault();
    }

    // Jika sudah terbuka untuk idx yang sama, tutup
    if (_slikDropdownIdx === idx && _slikDropdownEl) {
        _closeSlikDropdown();
        return;
    }

    _closeSlikDropdown();

    var btn = (e && (e.currentTarget || e.target)) ? (e.currentTarget || e.target).closest('button') : null;
    if (!btn) {
        btn = document.querySelector('[data-proses-idx="' + idx + '"]');
    }
    var rect = btn ? btn.getBoundingClientRect() : { bottom: 200, left: 200 };

    var dd = document.createElement('div');
    dd.id = 'slikBodyDropdown';
    dd.style.cssText = [
        'position:fixed',
        'top:' + (rect.bottom + 4) + 'px',
        'left:' + rect.left + 'px',
        'z-index:999999',
        'background:#ffffff',
        'border:1px solid #cbd5e1',
        'border-radius:10px',
        'box-shadow:0 12px 30px rgba(0,0,0,0.2)',
        'min-width:200px',
        'overflow:hidden',
        'display:block'
    ].join(';');

    var isPetugasAktif = (window.PETUGAS_SLIK_AKTIF === 1);
    var canEdit = (window.CAN_EDIT_SLIK === true);
    var isUserPengusul = (!window.USER_ROLE || window.USER_ROLE.toLowerCase() === 'pengusul');

    var btnProses = document.createElement('button');
    btnProses.type = 'button';
    btnProses.style.cssText = 'display:flex;align-items:center;gap:10px;width:100%;text-align:left;padding:11px 14px;font-size:12px;font-weight:600;background:#ffffff;border:none;cursor:pointer;border-bottom:1px solid #f1f5f9;';
    
    if (isPetugasAktif && isUserPengusul && !canEdit) {
        // Mode Petugas SLIK Aktif: Pengusul kirim permohonan ke Petugas SLIK
        btnProses.style.color = '#1e40af';
        btnProses.innerHTML = '<svg width="15" height="15" fill="none" stroke="#2563eb" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg><span>Kirim Pengajuan ke Petugas SLIK</span>';
        btnProses.onmouseenter = function() { this.style.background = '#eff6ff'; };
        btnProses.onmouseleave = function() { this.style.background = '#ffffff'; };
        btnProses.onclick = function(ev) {
            if (ev && ev.stopPropagation) ev.stopPropagation();
            _closeSlikDropdown();
            konfirmasiKirimPengajuanKePetugasSlik(idx);
        };
    } else {
        // Mode Fleksibel (Single AO) atau Petugas SLIK / Superadmin
        btnProses.style.color = '#1e293b';
        btnProses.innerHTML = '<svg width="15" height="15" fill="none" stroke="#2563eb" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg><span>Proses (Upload Hasil)</span>';
        btnProses.onmouseenter = function() { this.style.background = '#eff6ff'; };
        btnProses.onmouseleave = function() { this.style.background = '#ffffff'; };
        btnProses.onclick = function(ev) {
            if (ev && ev.stopPropagation) ev.stopPropagation();
            _closeSlikDropdown();
            bukaModalUploadHasilSlik(idx);
        };
    }

    var btnEdit = document.createElement('button');
    btnEdit.type = 'button';
    btnEdit.style.cssText = 'display:flex;align-items:center;gap:10px;width:100%;text-align:left;padding:11px 14px;font-size:12px;font-weight:600;color:#1e293b;background:#ffffff;border:none;cursor:pointer;';
    btnEdit.innerHTML = '<svg width="15" height="15" fill="none" stroke="#d97706" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg><span>Edit Data Upload</span>';
    btnEdit.onmouseenter = function() { this.style.background = '#fffbeb'; };
    btnEdit.onmouseleave = function() { this.style.background = '#ffffff'; };
    btnEdit.onclick = function(ev) {
        if (ev && ev.stopPropagation) ev.stopPropagation();
        _closeSlikDropdown();
        bukaModalEditPermohonanSlik(idx);
    };

    dd.appendChild(btnProses);
    dd.appendChild(btnEdit);
    document.body.appendChild(dd);
    _slikDropdownEl = dd;
    _slikDropdownIdx = idx;
}
window.toggleDropdownProsesSlik = toggleDropdownProsesSlik;

// Tutup dropdown jika klik di luar dropdown dan bukan di tombol Proses
document.addEventListener('click', function(e) {
    if (!_slikDropdownEl) return;
    var target = e.target;
    if (target.closest && (target.closest('#slikBodyDropdown') || target.closest('[onclick*="toggleDropdownProsesSlik"]') || target.closest('[data-proses-idx]'))) {
        return;
    }
    _closeSlikDropdown();
});

window.addEventListener('scroll', function() { _closeSlikDropdown(); }, true);
window.addEventListener('resize', function() { _closeSlikDropdown(); });

function bukaModalEditPermohonanSlik(idx) {
    var existing = null;
    for (var i = 0; i < window.daftarPermohonanSlikArray.length; i++) {
        if (parseInt(window.daftarPermohonanSlikArray[i].index) === parseInt(idx)) {
            existing = window.daftarPermohonanSlikArray[i];
            break;
        }
    }
    if (!existing) {
        alert('Data permohonan tidak ditemukan.');
        return;
    }

    window.slikEditingIdx = idx;
    bukaModalTambahPermohonanSlik();

    var elNama = document.getElementById('inputSlikNama');
    var elNoId = document.getElementById('inputSlikNoId');
    var elKeterkaitan = document.getElementById('inputSlikKeterkaitan');
    if (elNama) elNama.value = existing.nama || '';
    if (elNoId) elNoId.value = existing.no_id || '';
    if (elKeterkaitan) elKeterkaitan.value = existing.keterkaitan || '';

    var dokData = (window.slikDokumenUploaded && window.slikDokumenUploaded[idx]) ? window.slikDokumenUploaded[idx] : null;
    var fileId = (dokData && dokData.identitas) ? dokData.identitas : (existing.file_identitas || null);
    var filePerm = (dokData && dokData.permohonan) ? dokData.permohonan : (existing.file_permohonan || null);

    if (fileId) {
        var lblId = document.getElementById('labelFileIdentitas');
        var subId = document.getElementById('subFileIdentitas');
        if (lblId) {
            lblId.innerText = fileId.name || 'File sudah ada';
            lblId.title = lblId.innerText;
            lblId.style.maxWidth = '100%';
            lblId.style.overflow = 'hidden';
            lblId.style.textOverflow = 'ellipsis';
            lblId.style.whiteSpace = 'nowrap';
        }
        if (subId) subId.innerText = (fileId.size || '') + ' • File ada di server';
    }
    if (filePerm) {
        var lblPerm = document.getElementById('labelFilePermohonan');
        var subPerm = document.getElementById('subFilePermohonan');
        if (lblPerm) {
            lblPerm.innerText = filePerm.name || 'File sudah ada';
            lblPerm.title = lblPerm.innerText;
            lblPerm.style.maxWidth = '100%';
            lblPerm.style.overflow = 'hidden';
            lblPerm.style.textOverflow = 'ellipsis';
            lblPerm.style.whiteSpace = 'nowrap';
        }
        if (subPerm) subPerm.innerText = (filePerm.size || '') + ' • File ada di server';
    }
}
window.bukaModalEditPermohonanSlik = bukaModalEditPermohonanSlik;


// Hapus Baris Permohonan SLIK & Auto Simpan
function hapusBarisPermohonanSlik(idx) {
    if (confirm('Apakah Anda yakin ingin menghapus permohonan SLIK ini?')) {
        const row = document.getElementById('slik-row-' + idx);
        if (row) row.remove();

        window.daftarPermohonanSlikArray = window.daftarPermohonanSlikArray.filter(r => parseInt(r.index) !== parseInt(idx));
        if (window.slikDokumenUploaded) {
            delete window.slikDokumenUploaded[idx];
        }

        const hiddenInput = document.getElementById('hidden_daftar_permohonan_slik');
        if (hiddenInput) {
            hiddenInput.value = JSON.stringify(window.daftarPermohonanSlikArray);
        }

        const tbody = document.getElementById('bodySlikIdeb');
        if (tbody && tbody.querySelectorAll('.slik-row-item').length === 0) {
            tbody.innerHTML = `
                <tr id="emptyStateSlik">
                    <td colspan="11" class="py-10 text-center text-xs text-slate-400 font-medium">
                        Belum ada permohonan SLIK. Klik <strong>Tambah Pengajuan SLIK</strong> untuk menambah.
                    </td>
                </tr>
            `;
        }

        // Auto Simpan ke Database
        if (typeof simpanForm === 'function') {
            simpanForm(false, false);
        }

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'info',
                title: 'Permohonan SLIK dihapus & perubahan tersimpan',
                showConfirmButton: false,
                timer: 2000
            });
        }
    }
}
window.hapusBarisPermohonanSlik = hapusBarisPermohonanSlik;

// ========================================================
// MODAL LIHAT DOKUMEN PENGAJUAN SLIK
// ========================================================
window.activePreviewDocIdx = null;

function bukaModalLihatDokumenSlik(idx) {
    const modal = document.getElementById('modalLihatDokumenSlik');
    if (!modal) return;

    window.activePreviewDocIdx = idx;
    const data = (window.slikDokumenUploaded && window.slikDokumenUploaded[idx]) ? window.slikDokumenUploaded[idx] : null;
    const subTitle = document.getElementById('modalLihatDokumenSub');
    if (subTitle) {
        subTitle.textContent = data ? `Subjek: ${data.nama || 'Debitur / Pihak Terkait'}` : 'Berkas identitas dan permohonan';
    }

    // Identitas
    const namaId = document.getElementById('lihatDocNamaIdentitas');
    const btnId = document.getElementById('lihatDocBtnIdentitas');
    if (data && data.identitas) {
        if (namaId) namaId.textContent = `${data.identitas.name} (${data.identitas.size})`;
        if (btnId) {
            btnId.innerHTML = `<button type="button" onclick="previewFilePengajuanSlik('identitas')" class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-md transition shadow-2xs inline-flex items-center gap-1 cursor-pointer">
                <span>Buka</span> &rarr;
            </button>`;
        }
    } else {
        if (namaId) namaId.textContent = 'Belum ada dokumen identitas yang diunggah';
        if (btnId) btnId.innerHTML = `<span class="text-xs text-slate-400 italic">Tidak ada berkas</span>`;
    }

    // Permohonan
    const namaPerm = document.getElementById('lihatDocNamaPermohonan');
    const btnPerm = document.getElementById('lihatDocBtnPermohonan');
    if (data && data.permohonan) {
        if (namaPerm) namaPerm.textContent = `${data.permohonan.name} (${data.permohonan.size})`;
        if (btnPerm) {
            btnPerm.innerHTML = `<button type="button" onclick="previewFilePengajuanSlik('permohonan')" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-md transition shadow-2xs inline-flex items-center gap-1 cursor-pointer">
                <span>Buka</span> &rarr;
            </button>`;
        }
    } else {
        if (namaPerm) namaPerm.textContent = 'Belum ada dokumen permohonan yang diunggah';
        if (btnPerm) btnPerm.innerHTML = `<span class="text-xs text-slate-400 italic">Tidak ada berkas</span>`;
    }

    modal.classList.remove('hidden');
}
window.bukaModalLihatDokumenSlik = bukaModalLihatDokumenSlik;

function tutupModalLihatDokumenSlik() {
    const modal = document.getElementById('modalLihatDokumenSlik');
    if (modal) modal.classList.add('hidden');
}
window.tutupModalLihatDokumenSlik = tutupModalLihatDokumenSlik;

// Pratinjau Dokumen SLIK di dalam Popup (Tanpa Pindah URL)
function previewFilePengajuanSlik(docType) {
    const activeIdx = window.activePreviewDocIdx;
    const data = (window.slikDokumenUploaded && window.slikDokumenUploaded[activeIdx]) ? window.slikDokumenUploaded[activeIdx] : null;
    if (!data || !data[docType]) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Berkas Tidak Ditemukan',
                text: 'Dokumen belum diunggah untuk subjek ini.',
                confirmButtonColor: '#2563eb'
            });
        } else {
            alert('Berkas tidak ditemukan.');
        }
        return;
    }

    const doc = data[docType];
    const previewModal = document.getElementById('modalPreviewDocSlik');
    const previewTitle = document.getElementById('previewDocTitle');
    const previewSub = document.getElementById('previewDocSub');
    const container = document.getElementById('previewDocContainer');

    if (!previewModal || !container) return;

    const labelTipe = docType === 'identitas' ? 'Pratinjau Dokumen Identitas (KTP/NPWP)' : 'Pratinjau Dokumen Permohonan SLIK';
    if (previewTitle) previewTitle.textContent = labelTipe;
    if (previewSub) previewSub.textContent = `${doc.name} (${doc.size})`;

    const ext = (doc.name || '').split('.').pop().toLowerCase();

    if (['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'].includes(ext)) {
        container.innerHTML = `<div class="p-2 flex items-center justify-center w-full"><img src="${doc.url}" alt="${doc.name}" class="max-h-[70vh] max-w-full object-contain rounded-lg shadow-sm"></div>`;
    } else if (ext === 'pdf') {
        container.innerHTML = `<iframe src="${doc.url}" class="w-full h-[70vh] border border-slate-200 rounded-lg shadow-inner"></iframe>`;
    } else if (['txt', 'csv', 'log'].includes(ext)) {
        if (doc.file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const escaped = (e.target.result || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                container.innerHTML = `<pre class="w-full h-[70vh] p-4 bg-slate-900 text-slate-100 rounded-lg text-xs overflow-auto font-mono whitespace-pre-wrap leading-relaxed">${escaped}</pre>`;
            };
            reader.readAsText(doc.file);
        } else {
            container.innerHTML = `<iframe src="${doc.url}" class="w-full h-[70vh] border border-slate-200 rounded-lg"></iframe>`;
        }
    } else {
        container.innerHTML = `<iframe src="${doc.url}" class="w-full h-[70vh] border border-slate-200 rounded-lg"></iframe>`;
    }

    previewModal.classList.remove('hidden');
}
window.previewFilePengajuanSlik = previewFilePengajuanSlik;

function tutupPreviewDocSlik() {
    const modal = document.getElementById('modalPreviewDocSlik');
    if (modal) {
        modal.classList.add('hidden');
        const container = document.getElementById('previewDocContainer');
        if (container) container.innerHTML = '';
    }
}
window.tutupPreviewDocSlik = tutupPreviewDocSlik;

// ========================================================
// FUNGSI EXPORT TXT PERMOHONAN SLIK
// ========================================================
function exportTxtPermohonanSlik() {
    if (typeof sinkronisasiDataDebiturKeSlik === 'function') {
        sinkronisasiDataDebiturKeSlik();
    }

    let checkedItems = Array.from(document.querySelectorAll('.slik-select-item:checked'));
    if (checkedItems.length === 0) {
        const allItems = Array.from(document.querySelectorAll('.slik-select-item'));
        if (allItems.length > 0) {
            checkedItems = allItems;
        } else {
            alert('Tidak ada data permohonan SLIK yang dapat diekspor.');
            return;
        }
    }

    let fileContent = '';
    checkedItems.forEach(cb => {
        const idx = cb.getAttribute('data-index');
        const cellReff = document.getElementById('cell-reff-' + idx);
        if (cellReff && cellReff.innerText.trim()) {
            fileContent += cellReff.innerText.trim() + '\r\n';
        }
    });

    if (!fileContent.trim()) {
        const cellReff0 = document.getElementById('cell-reff-0');
        if (cellReff0 && cellReff0.innerText.trim()) {
            fileContent = cellReff0.innerText.trim() + '\r\n';
        } else {
            alert('Data No Reff SLIK kosong.');
            return;
        }
    }

    const kid = document.getElementById('kredit_id')?.value || (window.KREDIT_ID_VAL || '1');
    const filename = `Permohonan_SLIK_Kredit_${kid}.txt`;
    downloadTxtContent(filename, fileContent);

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: `Berkas TXT (${checkedItems.length} permohonan) berhasil didownload!`,
            showConfirmButton: false,
            timer: 2500
        });
    }
}
window.exportTxtPermohonanSlik = exportTxtPermohonanSlik;

// ========================================================
// MODAL UPLOAD HASIL SLIK (PDF & TXT)
// ========================================================
function bukaModalUploadHasilSlik(idx) {
    if (!window.CAN_EDIT_SLIK && window.PETUGAS_SLIK_AKTIF === 1) {
        pesanPetugasSlikAktif();
        return;
    }

    currentActiveSlikIndex = idx;
    const modal = document.getElementById('modalUploadHasilSlik');
    if (modal) {
        const inpTxt = document.getElementById('inputTxtFile') || document.getElementById('inputHasilTxt');
        const inpPdf = document.getElementById('inputPdfFile') || document.getElementById('inputHasilPdf');
        if (inpTxt) inpTxt.value = '';
        if (inpPdf) inpPdf.value = '';

        const labelTxt = document.getElementById('labelFileNameTxt');
        const labelPdf = document.getElementById('labelFileNamePdf');
        if (labelTxt) {
            labelTxt.innerText = 'Drag and drop a file here or click';
            labelTxt.title = '';
        }
        if (labelPdf) {
            labelPdf.innerText = 'Drag and drop a file here or click';
            labelPdf.title = '';
        }

        const subTxt = document.getElementById('labelFileSubTxt');
        const subPdf = document.getElementById('labelFileSubPdf');
        if (subTxt) subTxt.innerText = 'Format TXT';
        if (subPdf) subPdf.innerText = 'Format PDF';

        modal.classList.remove('hidden');
    }
}
window.bukaModalUploadHasilSlik = bukaModalUploadHasilSlik;

function tutupModalUploadHasilSlik() {
    const modal = document.getElementById('modalUploadHasilSlik');
    if (modal) modal.classList.add('hidden');
}
window.tutupModalUploadHasilSlik = tutupModalUploadHasilSlik;

function handleFileSelected(input, type) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const sizeKb = (file.size / 1024).toFixed(1) + ' KB';
        if (type === 'txt') {
            const label = document.getElementById('labelFileNameTxt');
            const sub = document.getElementById('labelFileSubTxt');
            if (label) {
                label.innerText = file.name;
                label.title = file.name;
                label.style.maxWidth = '100%';
                label.style.overflow = 'hidden';
                label.style.textOverflow = 'ellipsis';
                label.style.whiteSpace = 'nowrap';
            }
            if (sub) sub.innerText = sizeKb + ' • Siap diunggah';
        } else if (type === 'pdf') {
            const label = document.getElementById('labelFileNamePdf');
            const sub = document.getElementById('labelFileSubPdf');
            if (label) {
                label.innerText = file.name;
                label.title = file.name;
                label.style.maxWidth = '100%';
                label.style.overflow = 'hidden';
                label.style.textOverflow = 'ellipsis';
                label.style.whiteSpace = 'nowrap';
            }
            if (sub) sub.innerText = sizeKb + ' • Siap diunggah';
        }
    }
}
window.handleFileSelected = handleFileSelected;

// ========================================================
// MODAL UPLOAD HASIL SLIK (Mode Fleksibel / Petugas SLIK)
// ========================================================
function bukaModalUploadHasilSlik(idx) {
    currentActiveSlikIndex = idx;
    const modal = document.getElementById('modalUploadHasilSlik');
    if (!modal) return;

    let rowItem = window.daftarPermohonanSlikArray.find(r => parseInt(r.index) === parseInt(idx));
    if (!rowItem && window.daftarPermohonanSlikArray[idx]) {
        rowItem = window.daftarPermohonanSlikArray[idx];
    }

    const selectHasil = document.getElementById('modalHasilSlikSelect');
    if (selectHasil && rowItem && rowItem.hasil && rowItem.hasil !== 'Menunggu') {
        selectHasil.value = rowItem.hasil;
    }

    const labelTxt = document.getElementById('labelFileNameTxt');
    const labelPdf = document.getElementById('labelFileNamePdf');
    const subTxt = document.getElementById('labelFileSubTxt');
    const subPdf = document.getElementById('labelFileSubPdf');

    if (labelTxt) {
        labelTxt.innerText = (rowItem && rowItem.berkas_txt) ? rowItem.berkas_txt.split('/').pop() : 'Drag and drop a file here or click';
        labelTxt.title = labelTxt.innerText;
    }
    if (subTxt) {
        subTxt.innerText = (rowItem && rowItem.berkas_txt) ? 'File ada di server • Klik untuk ganti' : 'Format .TXT';
    }

    if (labelPdf) {
        labelPdf.innerText = (rowItem && rowItem.berkas_pdf) ? rowItem.berkas_pdf.split('/').pop() : 'Drag and drop a file here or click';
        labelPdf.title = labelPdf.innerText;
    }
    if (subPdf) {
        subPdf.innerText = (rowItem && rowItem.berkas_pdf) ? 'File ada di server • Klik untuk ganti' : 'Format .PDF';
    }

    // Reset file input agar bersih
    const inpTxt = document.getElementById('inputTxtFile');
    const inpPdf = document.getElementById('inputPdfFile');
    if (inpTxt) inpTxt.value = '';
    if (inpPdf) inpPdf.value = '';

    modal.classList.remove('hidden');
    modal.style.display = 'flex';
}
window.bukaModalUploadHasilSlik = bukaModalUploadHasilSlik;

function tutupModalUploadHasilSlik() {
    const modal = document.getElementById('modalUploadHasilSlik');
    if (modal) {
        modal.classList.add('hidden');
        modal.style.display = 'none';
    }
}
window.tutupModalUploadHasilSlik = tutupModalUploadHasilSlik;

// Submit Upload Hasil SLIK
async function submitFormUploadHasilSlik(e) {
    if (e) {
        try { e.preventDefault(); } catch(err){}
    }

    // 1. Langsung tutup modal seketika saat tombol Simpan ditekan agar user tidak menunggu
    tutupModalUploadHasilSlik();

    const selectHasil = document.getElementById('modalHasilSlikSelect');
    const inputTxt = document.getElementById('inputTxtFile');
    const inputPdf = document.getElementById('inputPdfFile');

    const hasilVal = selectHasil ? selectHasil.value : 'Lancar';
    const fileTxt = inputTxt?.files?.[0];
    const filePdf = inputPdf?.files?.[0];

    const kid = document.getElementById('kredit_id')?.value || (window.KREDIT_ID_VAL || '');
    const baseUrl = (typeof getBaseUrl === 'function') ? getBaseUrl() : (window.BASE_URL || '');

    // 2. Update status & hasil langsung di baris tabel di UI
    const badgeCell = document.getElementById('status-badge-cell-' + currentActiveSlikIndex);
    if (badgeCell) {
        badgeCell.innerHTML = `<span class="inline-block bg-[#00a859] text-white font-bold text-[11px] px-3 py-1 rounded shadow-2xs">Telah Terbit</span>`;
    }

    const hasilCell = document.getElementById('hasil-badge-cell-' + currentActiveSlikIndex);
    if (hasilCell) {
        let colorClass = 'border-slate-300 text-slate-500 bg-slate-50/50';
        if (hasilVal.toLowerCase() === 'lancar') {
            colorClass = 'border-[#00a859] text-[#00a859] bg-emerald-50/30';
        } else {
            colorClass = 'border-amber-500 text-amber-600 bg-amber-50/30';
        }
        hasilCell.innerHTML = `<span class="inline-block px-3 py-0.5 rounded-full border ${colorClass} text-xs font-semibold">${hasilVal}</span>`;
    }

    const hiddenStatus = document.getElementById('hidden_status_slik');
    if (hiddenStatus) hiddenStatus.value = 'Telah Terbit';

    const hiddenHasil = document.getElementById('hidden_hasil_slik');
    if (hiddenHasil) hiddenHasil.value = hasilVal;

    // Update item di array memory
    let rowItem = window.daftarPermohonanSlikArray.find(r => parseInt(r.index) === parseInt(currentActiveSlikIndex));
    if (!rowItem && window.daftarPermohonanSlikArray[currentActiveSlikIndex]) {
        rowItem = window.daftarPermohonanSlikArray[currentActiveSlikIndex];
    }
    if (rowItem) {
        rowItem.status = 'Telah Terbit';
        rowItem.hasil = hasilVal;
    }

    const hiddenInput = document.getElementById('hidden_daftar_permohonan_slik');
    if (hiddenInput) {
        hiddenInput.value = JSON.stringify(window.daftarPermohonanSlikArray);
    }

    // Tampilkan notifikasi toast langsung
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Hasil SLIK berhasil disimpan & diterbitkan!',
            showConfirmButton: false,
            timer: 2000
        });
    }

    // 3. Kirim upload dan simpan otomatis di background
    try {
        const formData = new FormData();
        formData.append('hasil_slik', hasilVal);
        formData.append('active_index', currentActiveSlikIndex);
        if (kid) formData.append('kredit_id', kid);
        if (filePdf) formData.append('file_pdf', filePdf);
        if (fileTxt) formData.append('file_txt', fileTxt);
        formData.append('daftar_permohonan_slik', JSON.stringify(window.daftarPermohonanSlikArray));

        const res = await fetch(baseUrl + '/pengajuan/upload-hasil-slik', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        });
        const data = await res.json();

        if (data.status) {
            if (data.pdf_path) {
                const hiddenPdf = document.getElementById('hidden_berkas_slik_pdf');
                if (hiddenPdf) hiddenPdf.value = data.pdf_path;
                if (rowItem) rowItem.berkas_pdf = data.pdf_path;
                window.BERKAS_SLIK_PDF_VAL = data.pdf_path;
            }
            if (data.txt_path) {
                const hiddenTxt = document.getElementById('hidden_berkas_slik_txt');
                if (hiddenTxt) hiddenTxt.value = data.txt_path;
                if (rowItem) rowItem.berkas_txt = data.txt_path;
                window.BERKAS_SLIK_TXT_VAL = data.txt_path;
            }
            if (data.daftar_permohonan_slik) {
                try {
                    window.daftarPermohonanSlikArray = JSON.parse(data.daftar_permohonan_slik);
                } catch(e){}
            }
            if (hiddenInput) {
                hiddenInput.value = JSON.stringify(window.daftarPermohonanSlikArray);
            }
            if (data.facilities && Array.isArray(data.facilities) && data.facilities.length > 0) {
                window.slikFasilitasArray = data.facilities;
                if (typeof renderTabelFasilitasSlik === 'function') {
                    renderTabelFasilitasSlik(window.slikFasilitasArray);
                }
                if (typeof hitungMetrikRingkasanSlik === 'function') {
                    hitungMetrikRingkasanSlik();
                }
                if (typeof sinkronkanHiddenFasilitasSlik === 'function') {
                    sinkronkanHiddenFasilitasSlik();
                }
            }
        }

        // Panggil simpanForm agar seluruh data form tersimpan permanen di database
        if (typeof simpanForm === 'function') {
            simpanForm(false, false);
        }
    } catch (err) {
        console.error('Error saat menyimpan hasil SLIK:', err);
    }
}
window.submitFormUploadHasilSlik = submitFormUploadHasilSlik;

// ========================================================
// MODAL RINGKASAN SLIK & EXPORT
// ========================================================
function formatRupiahDisplay(num) {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(num || 0));
}

function bukaModalRingkasanSlik(idx) {
    currentActiveSlikIndex = idx;
    const modal = document.getElementById('modalRingkasanSlik');
    if (!modal) return;

    modal.classList.remove('hidden');
    modal.style.display = 'flex';

    // 1. Tampilkan identitas subjek aktif di banner atas
    let rowItem = window.daftarPermohonanSlikArray.find(r => parseInt(r.index) === parseInt(idx));
    if (!rowItem && window.daftarPermohonanSlikArray[idx]) {
        rowItem = window.daftarPermohonanSlikArray[idx];
    }

    const debNama = document.getElementById('ringkasanDebiturNama');
    const debKeterkaitan = document.getElementById('ringkasanDebiturKeterkaitan');
    const debNoId = document.getElementById('ringkasanDebiturNoId');
    const debNoReff = document.getElementById('ringkasanDebiturNoReff');
    const debBadge = document.getElementById('ringkasanDebiturHasilBadge');

    if (rowItem) {
        if (debNama) debNama.textContent = rowItem.nama || window.NAMA_DEBITUR_VAL || 'Debitur';
        if (debKeterkaitan) {
            debKeterkaitan.textContent = rowItem.is_utama ? 'PEMOHON' : (rowItem.keterkaitan || 'PENJAMIN');
        }
        if (debNoId) debNoId.textContent = rowItem.no_id || window.NO_ID_VAL || '-';
        if (debNoReff) debNoReff.textContent = rowItem.no_reff || '-';
        if (debBadge) {
            debBadge.textContent = rowItem.hasil || 'Lancar';
            if ((rowItem.hasil || '').toLowerCase() === 'lancar') {
                debBadge.className = 'px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300';
            } else {
                debBadge.className = 'px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300';
            }
        }
    }

    // 2. Render jika data fasilitas sudah ada di memory
    if (window.slikFasilitasArray && window.slikFasilitasArray.length > 0) {
        renderTabelFasilitasSlik(window.slikFasilitasArray);
        hitungMetrikRingkasanSlik();
    }

    // 3. Otomatis muat atau ambil dari hasil SLIK yang diunggah
    muatRingkasanSlikDariServer(idx);
}
window.bukaModalRingkasanSlik = bukaModalRingkasanSlik;

async function muatRingkasanSlikDariServer(idx) {
    const loadingEl = document.getElementById('loadingRingkasanSlik');
    if (loadingEl) {
        loadingEl.classList.remove('hidden');
        loadingEl.classList.add('flex');
    }

    const tbody = document.getElementById('bodyRincianFasilitasSlik');
    if (tbody && (!window.slikFasilitasArray || window.slikFasilitasArray.length === 0)) {
        tbody.innerHTML = `
            <tr>
                <td colspan="14" class="py-12 text-center text-xs text-blue-600 font-semibold bg-white">
                    <div class="flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 animate-spin text-blue-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span>Memuat ringkasan data SLIK dari berkas...</span>
                    </div>
                </td>
            </tr>
        `;
    }

    const kid = document.getElementById('kredit_id')?.value || (window.KREDIT_ID_VAL || '');
    const baseUrl = (typeof getBaseUrl === 'function') ? getBaseUrl() : (window.BASE_URL || '');

    let rowItem = window.daftarPermohonanSlikArray.find(r => parseInt(r.index) === parseInt(idx));
    if (!rowItem && window.daftarPermohonanSlikArray[idx]) rowItem = window.daftarPermohonanSlikArray[idx];

    let pdfPath = (rowItem && rowItem.berkas_pdf) ? rowItem.berkas_pdf : (document.getElementById('hidden_berkas_slik_pdf')?.value || '');

    try {
        const formData = new FormData();
        if (kid) formData.append('kredit_id', kid);
        formData.append('active_index', idx);
        if (pdfPath) formData.append('pdf_path', pdfPath);

        const res = await fetch(baseUrl + '/pengajuan/ambil-ringkasan-slik', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        });
        const data = await res.json();

        if (loadingEl) {
            loadingEl.classList.add('hidden');
            loadingEl.classList.remove('flex');
        }

        if (data.status && Array.isArray(data.facilities) && data.facilities.length > 0) {
            window.slikFasilitasArray = data.facilities;
            renderTabelFasilitasSlik(window.slikFasilitasArray);
            hitungMetrikRingkasanSlik();
            sinkronkanHiddenFasilitasSlik();
        } else {
            if (!window.slikFasilitasArray || window.slikFasilitasArray.length === 0) {
                if (tbody) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="14" class="py-10 text-center text-xs text-slate-500 bg-white">
                                <div class="flex flex-col items-center justify-center gap-1.5 max-w-md mx-auto">
                                    <span class="text-rose-600 font-bold text-sm">Gagal memuat data SLIK</span>
                                    <span class="text-slate-400 font-normal text-xs leading-relaxed text-center">${data.message || 'Berkas hasil SLIK belum diunggah atau tidak ditemukan catatan fasilitas.'}</span>
                                    <div class="mt-4 flex items-center gap-2">
                                        <button type="button" onclick="muatRingkasanSlikDariServer(${idx})" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition cursor-pointer">
                                            Coba Muat Ulang
                                        </button>
                                        <button type="button" onclick="tutupModalRingkasanSlik(); bukaModalUploadHasilSlik(${idx});" class="px-3.5 py-1.5 bg-[#0a0082] hover:bg-[#07005e] text-white text-xs font-semibold rounded-lg transition cursor-pointer shadow-2xs">
                                            Unggah Berkas Hasil SLIK
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    `;
                }
            }
        }
    } catch (err) {
        if (loadingEl) {
            loadingEl.classList.add('hidden');
            loadingEl.classList.remove('flex');
        }
        console.error('Error saat memuat ringkasan SLIK:', err);
        if (tbody && (!window.slikFasilitasArray || window.slikFasilitasArray.length === 0)) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="14" class="py-10 text-center text-xs text-slate-500 bg-white">
                        <div class="flex flex-col items-center justify-center gap-1.5">
                            <span class="text-rose-600 font-bold text-sm">Gagal memuat data SLIK</span>
                            <span class="text-slate-400 font-normal text-xs">Terjadi gangguan jaringan saat memuat data.</span>
                            <button type="button" onclick="muatRingkasanSlikDariServer(${idx})" class="mt-3 px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition cursor-pointer">
                                Coba Muat Ulang
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }
    }
}
window.muatRingkasanSlikDariServer = muatRingkasanSlikDariServer;

function renderTabelFasilitasSlik(list) {
    const tbody = document.getElementById('bodyRincianFasilitasSlik');
    if (!tbody) return;

    if (!list || list.length === 0) {
        tbody.innerHTML = `
            <tr id="emptyFasilitasRow">
                <td colspan="14" class="py-8 text-center text-xs text-slate-400 italic bg-white">
                    Tidak ada catatan fasilitas pinjaman aktif.
                </td>
            </tr>
        `;
        return;
    }

    let html = '';
    list.forEach((row, i) => {
        const debName = row.nama_debitur || window.NAMA_DEBITUR_VAL || '-';
        const bankName = row.bank_lembaga || '-';
        const jenisPenggunaan = row.jenis_penggunaan || row.keterangan || 'Modal Kerja';
        const jenisKredit = row.jenis_kredit || row.jenis_fasilitas || 'Kredit Usaha';
        const plafon = parseFloat(row.plafon || 0);
        const outstanding = parseFloat(row.outstanding || 0);
        const sukuBunga = row.suku_bunga || '-';
        const tglAkad = row.tgl_akad || '-';
        const jatuhTempo = row.jatuh_tempo || '-';
        const jangkaWaktu = row.jangka_waktu || '-';
        const tunggakan = row.tunggakan || '0';
        const kelancaran = row.kelancaran || 'Lancar';
        const isLancar = kelancaran.toLowerCase() === 'lancar';
        const badgeColor = isLancar ? 'bg-emerald-50 text-emerald-700 border-emerald-300' : 'bg-rose-50 text-rose-700 border-rose-300';

        html += `
            <tr class="hover:bg-slate-50/80 transition" id="row-fasilitas-${i}">
                <td class="py-2.5 px-3 border-r border-slate-100 text-center font-bold">${i + 1}</td>
                <td class="py-2.5 px-3 border-r border-slate-100 font-bold">${debName}</td>
                <td class="py-2.5 px-3 border-r border-slate-100">${bankName}</td>
                <td class="py-2.5 px-3 border-r border-slate-100">${jenisPenggunaan}</td>
                <td class="py-2.5 px-3 border-r border-slate-100">${jenisKredit}</td>
                <td class="py-2.5 px-3 border-r border-slate-100 font-semibold">${formatRupiahDisplay(plafon)}</td>
                <td class="py-2.5 px-3 border-r border-slate-100 font-bold text-blue-700">${formatRupiahDisplay(outstanding)}</td>
                <td class="py-2.5 px-3 border-r border-slate-100">${sukuBunga}</td>
                <td class="py-2.5 px-3 border-r border-slate-100">${tglAkad}</td>
                <td class="py-2.5 px-3 border-r border-slate-100">${jatuhTempo}</td>
                <td class="py-2.5 px-3 border-r border-slate-100">${jangkaWaktu}</td>
                <td class="py-2.5 px-3 border-r border-slate-100">${tunggakan}</td>
                <td class="py-2.5 px-3 text-center border-r border-slate-100">
                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-semibold border ${badgeColor}">
                        ${kelancaran}
                    </span>
                </td>
                <td class="py-2.5 px-2 text-center">
                    <button type="button" onclick="hapusFasilitasSlik(${i})" class="text-rose-500 hover:text-rose-700 p-1 cursor-pointer" title="Hapus Fasilitas">✕</button>
                </td>
            </tr>
        `;
    });

    tbody.innerHTML = html;
}
window.renderTabelFasilitasSlik = renderTabelFasilitasSlik;

function hitungMetrikRingkasanSlik() {
    const list = window.slikFasilitasArray || [];
    let totalPlafon = 0;
    let totalBakiDebet = 0;
    let countBankUmum = 0;
    let countBpr = 0;
    let countLembaga = 0;
    let countLainnya = 0;
    let rankKol = 1;

    const rankMap = {
        'lancar': 1,
        'dpk': 2,
        'dpd 1-30': 2,
        'kurang lancar': 3,
        'dpd 60-90': 3,
        'diragukan': 4,
        'dpd 90-180': 4,
        'macet': 5,
        'dpd 180+': 5,
        'hapus buku': 5
    };
    const reverseRankMap = {
        1: 'Lancar',
        2: 'DPK',
        3: 'Kurang Lancar',
        4: 'Diragukan',
        5: 'Macet'
    };

    list.forEach(r => {
        const p = parseFloat(r.plafon || 0);
        const b = parseFloat(r.outstanding || 0);
        totalPlafon += p;
        totalBakiDebet += b;

        const bankName = (r.bank_lembaga || '').toLowerCase();
        const jenisKredit = (r.jenis_kredit || r.jenis_fasilitas || '').toLowerCase();

        if (bankName.includes('bpr') || bankName.includes('bprs')) {
            countBpr++;
        } else if (bankName.includes('leasing') || bankName.includes('finance') || bankName.includes('pembiayaan') || jenisKredit.includes('pembiayaan')) {
            countLembaga++;
        } else if (bankName.includes('bank')) {
            countBankUmum++;
        } else {
            countLainnya++;
        }

        const k = (r.kelancaran || 'Lancar').toLowerCase().trim();
        const rk = rankMap[k] || 1;
        if (rk > rankKol) rankKol = rk;
    });

    const elPlafon = document.getElementById('ringkasanMetricPlafon');
    const elBakiDebet = document.getElementById('ringkasanMetricBakiDebet');
    const elKol = document.getElementById('ringkasanMetricKol');
    const elBankUmum = document.getElementById('ringkasanMetricBankUmum');
    const elBpr = document.getElementById('ringkasanMetricBpr');
    const elLembaga = document.getElementById('ringkasanMetricLembaga');
    const elLainnya = document.getElementById('ringkasanMetricLainnya');

    if (elPlafon) elPlafon.textContent = formatRupiahDisplay(totalPlafon);
    if (elBakiDebet) elBakiDebet.textContent = formatRupiahDisplay(totalBakiDebet);
    if (elKol) elKol.textContent = list.length > 0 ? (reverseRankMap[rankKol] || 'Lancar') : '-';
    if (elBankUmum) elBankUmum.textContent = countBankUmum;
    if (elBpr) elBpr.textContent = countBpr;
    if (elLembaga) elLembaga.textContent = countLembaga;
    if (elLainnya) elLainnya.textContent = countLainnya;
}
window.hitungMetrikRingkasanSlik = hitungMetrikRingkasanSlik;

function sinkronkanHiddenFasilitasSlik() {
    const container = document.getElementById('hiddenFasilitasSlikContainer');
    if (!container) return;

    const list = window.slikFasilitasArray || [];
    let html = '';
    list.forEach((r, i) => {
        html += `
            <input type="hidden" name="slik[${i}][nama_debitur]" value="${r.nama_debitur || ''}">
            <input type="hidden" name="slik[${i}][bank_lembaga]" value="${r.bank_lembaga || ''}">
            <input type="hidden" name="slik[${i}][keterangan]" value="${r.keterangan || r.jenis_penggunaan || 'Modal Kerja'}">
            <input type="hidden" name="slik[${i}][jenis_fasilitas]" value="${r.jenis_fasilitas || r.jenis_kredit || 'Kredit Usaha'}">
            <input type="hidden" name="slik[${i}][plafon]" value="${r.plafon || 0}">
            <input type="hidden" name="slik[${i}][outstanding]" value="${r.outstanding || 0}">
            <input type="hidden" name="slik[${i}][kelancaran]" value="${r.kelancaran || 'Lancar'}">
            <input type="hidden" name="slik[${i}][suku_bunga]" value="${r.suku_bunga || '-'}">
            <input type="hidden" name="slik[${i}][tgl_akad]" value="${r.tgl_akad || '-'}">
            <input type="hidden" name="slik[${i}][jatuh_tempo]" value="${r.jatuh_tempo || '-'}">
            <input type="hidden" name="slik[${i}][jangka_waktu]" value="${r.jangka_waktu || '-'}">
            <input type="hidden" name="slik[${i}][tunggakan]" value="${r.tunggakan || '0'}">
        `;
    });
    container.innerHTML = html;
}
window.sinkronkanHiddenFasilitasSlik = sinkronkanHiddenFasilitasSlik;

function bukaFormTambahFasilitasSlik() {
    const form = document.getElementById('formTambahFasilitasSlikContainer');
    if (form) form.classList.remove('hidden');
}
window.bukaFormTambahFasilitasSlik = bukaFormTambahFasilitasSlik;

function tutupFormTambahFasilitasSlik() {
    const form = document.getElementById('formTambahFasilitasSlikContainer');
    if (form) form.classList.add('hidden');
}
window.tutupFormTambahFasilitasSlik = tutupFormTambahFasilitasSlik;

function simpanFasilitasSlikBaru() {
    const bank = document.getElementById('inputFasBank')?.value.trim();
    const jenis = document.getElementById('inputFasJenis')?.value.trim() || 'Modal Kerja';
    const plafonRaw = document.getElementById('inputFasPlafon')?.value || '0';
    const outRaw = document.getElementById('inputFasOutstanding')?.value || '0';
    const kelancaran = document.getElementById('inputFasKelancaran')?.value || 'Lancar';
    const bunga = document.getElementById('inputFasBunga')?.value.trim() || '-';

    if (!bank) {
        alert('Harap isi Nama Bank / Lembaga.');
        return;
    }

    const plafon = parseFloat(plafonRaw.replace(/[^0-9]/g, '')) || 0;
    const outstanding = parseFloat(outRaw.replace(/[^0-9]/g, '')) || 0;

    let rowItem = window.daftarPermohonanSlikArray.find(r => parseInt(r.index) === parseInt(currentActiveSlikIndex));
    if (!rowItem && window.daftarPermohonanSlikArray[currentActiveSlikIndex]) {
        rowItem = window.daftarPermohonanSlikArray[currentActiveSlikIndex];
    }
    const debName = (rowItem && rowItem.nama) ? rowItem.nama : (window.NAMA_DEBITUR_VAL || 'Debitur');

    if (!window.slikFasilitasArray) window.slikFasilitasArray = [];
    window.slikFasilitasArray.push({
        nama_debitur: debName,
        bank_lembaga: bank,
        jenis_penggunaan: jenis,
        jenis_kredit: jenis,
        keterangan: jenis,
        jenis_fasilitas: jenis,
        plafon: plafon,
        outstanding: outstanding,
        suku_bunga: bunga,
        tgl_akad: '-',
        jatuh_tempo: '-',
        jangka_waktu: '-',
        tunggakan: '0',
        kelancaran: kelancaran,
        kondisi: 'Fasilitas Aktif'
    });

    renderTabelFasilitasSlik(window.slikFasilitasArray);
    hitungMetrikRingkasanSlik();
    sinkronkanHiddenFasilitasSlik();

    // Reset form
    document.getElementById('inputFasBank').value = '';
    document.getElementById('inputFasJenis').value = '';
    document.getElementById('inputFasPlafon').value = '';
    document.getElementById('inputFasOutstanding').value = '';
    document.getElementById('inputFasBunga').value = '';
    tutupFormTambahFasilitasSlik();
}
window.simpanFasilitasSlikBaru = simpanFasilitasSlikBaru;

function hapusFasilitasSlik(idx) {
    if (!window.slikFasilitasArray || !window.slikFasilitasArray[idx]) return;
    if (confirm('Hapus rincian fasilitas pinjaman ini?')) {
        window.slikFasilitasArray.splice(idx, 1);
        renderTabelFasilitasSlik(window.slikFasilitasArray);
        hitungMetrikRingkasanSlik();
        sinkronkanHiddenFasilitasSlik();
    }
}
window.hapusFasilitasSlik = hapusFasilitasSlik;

function tutupModalRingkasanSlik() {
    const modal = document.getElementById('modalRingkasanSlik');
    if (modal) {
        modal.classList.add('hidden');
        modal.style.display = 'none';
    }
}
window.tutupModalRingkasanSlik = tutupModalRingkasanSlik;

function exportPdfRingkasanSlik() {
    const kid = document.getElementById('kredit_id')?.value || (window.KREDIT_ID_VAL || '1');
    const baseUrl = (typeof getBaseUrl === 'function') ? getBaseUrl() : (window.BASE_URL || '');
    window.open(baseUrl + '/pengajuan/cetak/' + kid + '?print=1', '_blank');
}
window.exportPdfRingkasanSlik = exportPdfRingkasanSlik;

function exportExcelRingkasanSlik() {
    const table = document.getElementById('tabelRincianFasilitasSlik');
    if (!table) {
        alert('Tabel fasilitas SLIK tidak ditemukan.');
        return;
    }
    let csv = '';
    const rows = table.querySelectorAll('tr');
    rows.forEach(tr => {
        const cols = tr.querySelectorAll('th, td');
        const rowData = [];
        cols.forEach(col => rowData.push('"' + col.innerText.replace(/"/g, '""').trim() + '"'));
        csv += rowData.join(',') + '\r\n';
    });

    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'Ringkasan_Fasilitas_SLIK_' + (document.getElementById('kredit_id')?.value || '1') + '.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}
window.exportExcelRingkasanSlik = exportExcelRingkasanSlik;

// ========================================================
// MODAL VIEWER PDF HASIL SLIK
// ========================================================
function bukaModalPdfSlik(idx) {
    currentActiveSlikIndex = idx;
    const modal = document.getElementById('modalPdfSlik');
    if (!modal) return;

    const iframe = document.getElementById('iframePdfSlik');
    const pdfWrapper = document.getElementById('pdfViewerWrapper');
    const emptyWrapper = document.getElementById('pdfEmptyWrapper');
    const fileNameLabel = document.getElementById('pdfFileNameLabel');
    const btnBukaTab = document.getElementById('btnBukaPdfTab');

    let rowItem = window.daftarPermohonanSlikArray.find(r => parseInt(r.index) === parseInt(idx));
    if (!rowItem && window.daftarPermohonanSlikArray[idx]) {
        rowItem = window.daftarPermohonanSlikArray[idx];
    }

    const hiddenPdf = document.getElementById('hidden_berkas_slik_pdf');
    const baseUrl = (typeof getBaseUrl === 'function') ? getBaseUrl() : (window.BASE_URL || '');
    
    let path = '';
    if (rowItem && rowItem.berkas_pdf) {
        path = rowItem.berkas_pdf;
    } else if (hiddenPdf && hiddenPdf.value) {
        path = hiddenPdf.value;
    } else if (window.BERKAS_SLIK_PDF_VAL) {
        path = window.BERKAS_SLIK_PDF_VAL;
    } else if (rowItem && rowItem.file_permohonan && rowItem.file_permohonan.path && rowItem.file_permohonan.path.toLowerCase().endsWith('.pdf')) {
        path = rowItem.file_permohonan.path;
    }

    if (path) {
        if (hiddenPdf && !hiddenPdf.value) hiddenPdf.value = path;
        if (rowItem && !rowItem.berkas_pdf) rowItem.berkas_pdf = path;

        const cleanBase = baseUrl.replace(/\/+$/, '');
        const cleanPath = path.replace(/^\/+/, '');
        const fullUrl = path.startsWith('http') ? path : (cleanBase + '/' + cleanPath);

        if (iframe) iframe.src = fullUrl;
        if (fileNameLabel) fileNameLabel.innerText = path.split('/').pop();
        if (btnBukaTab) btnBukaTab.href = fullUrl;
        if (pdfWrapper) pdfWrapper.classList.remove('hidden');
        if (emptyWrapper) emptyWrapper.classList.add('hidden');
    } else {
        if (iframe) iframe.src = '';
        if (pdfWrapper) pdfWrapper.classList.add('hidden');
        if (emptyWrapper) emptyWrapper.classList.remove('hidden');
    }

    modal.classList.remove('hidden');
    modal.style.display = 'flex';
}
window.bukaModalPdfSlik = bukaModalPdfSlik;

function tutupModalPdfSlik() {
    const modal = document.getElementById('modalPdfSlik');
    if (modal) {
        modal.classList.add('hidden');
        modal.style.display = 'none';
    }
    const iframe = document.getElementById('iframePdfSlik');
    if (iframe) {
        iframe.src = '';
    }
}
window.tutupModalPdfSlik = tutupModalPdfSlik;

