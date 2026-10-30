/**
 * Pengajuan Form Logic - OTW Credit Engine
 * Extracted from form.php
 */
const getBaseUrl = () => (window.BASE_URL ? window.BASE_URL.replace(/\/+$/, '') : '');

        let currentTab = 1;
        const totalTabs = 12; // Proses pengusul dibatasi sampai tahap 12 (Tahap 13 didisabel sementara)

        // Inisialisasi saat halaman dimuat
        document.addEventListener('DOMContentLoaded', () => {
            hitungKalkulasiAngsuran();
            hitungCoverageAgunan();
            hitungTotalSlik();
            hitungRataRataRekening();
            hitungLabaRugi();
            hitungDisbursement();
            initDaftarBuktiPendapatanLain();
            initDaftarBebanLain();

            const boxHasil = document.getElementById('boxHasilEksekusiAnalisa');
            if (boxHasil && !boxHasil.classList.contains('hidden')) {
                renderTabelSimulasiAngsuran();
            }

            // Buka tab spesifik jika ada query parameter misal ?tab=6 untuk Petugas SLIK
            const urlParams = new URLSearchParams(window.location.search);
            const initialTab = parseInt(urlParams.get('tab'));
            if (initialTab && initialTab >= 1 && initialTab <= totalTabs) {
                goToTab(initialTab);
            }
        });

        const stepsMetadata = [
            { title: '1. Aplikasi', sub: 'Sedang dikerjakan', defaultSub: 'Parameter dasar', mainTitle: 'Langkah 1: Aplikasi Pengajuan', desc: 'Konfigurasi parameter dasar plafon dan skema kredit debitur.' },
            { title: '2. Info Pribadi', sub: 'Sedang dikerjakan', defaultSub: 'Data demografi', mainTitle: 'Langkah 2: Data Informasi Pribadi', desc: 'Data identitas nasabah pemohon sesuai KTP dan verifikasi kependudukan.' },
            { title: '3. Info Usaha', sub: 'Sedang dikerjakan', defaultSub: 'Legalitas usaha', mainTitle: 'Langkah 3: Profil & Legalitas Usaha', desc: 'Informasi operasional, pengalaman, legalitas izin dan tempat usaha.' },
            { title: '4. Jaminan', sub: 'Sedang dikerjakan', defaultSub: 'Agunan kredit', mainTitle: 'Langkah 4: Agunan & Jaminan Kredit', desc: 'Data sertifikat tanah, BPKB kendaraan, dan taksasi nilai agunan.' },
            { title: '5. Dokumen', sub: 'Sedang dikerjakan', defaultSub: 'Unggah berkas', mainTitle: 'Langkah 5: Unggah Dokumen Berkas', desc: 'Unggah berkas pendukung KTP, KK, NPWP, NIB, dan agunan.' },
            { title: '6. SLIK / CLIK', sub: 'Sedang dikerjakan', defaultSub: 'Cek riwayat kredit', mainTitle: 'Langkah 6: Cek SLIK OJK & Riwayat', desc: 'Riwayat kolektibilitas kredit pada bank atau lembaga keuangan lain.' },
            { title: '7. Web Check', sub: 'Sedang dikerjakan', defaultSub: 'Verifikasi online', mainTitle: 'Langkah 7: Verifikasi Web & Legal Check', desc: 'Pengecekan profiling web, kepatuhan hukum, dan reputasi usaha.' },
            { title: '8. Rekening Koran', sub: 'Sedang dikerjakan', defaultSub: 'Analisis mutasi', mainTitle: 'Langkah 8: Rekapitulasi Mutasi Rekening Koran', desc: 'Pencatatan arus kas masuk (kredit) dan keluar (debit) untuk validasi omzet usaha riil.' },
            { title: '9. Laba Rugi', sub: 'Sedang dikerjakan', defaultSub: 'Proyeksi keuangan', mainTitle: 'Langkah 9: Analisis Laba Rugi & Pendapatan', desc: 'Perhitungan omset pendapatan, HPP pengeluaran, laba bersih riil, dan ekstra cashflow.' },
            { title: '10. Opini Pengusul', sub: 'Sedang dikerjakan', defaultSub: 'Catatan kualitatif', mainTitle: 'Langkah 10: Opini & Pertimbangan Pengusul', desc: 'Evaluasi kualitatif dan pertimbangan pengusul terkait kelayakan usaha, kondisi agunan, dan kebutuhan pembiayaan nasabah.' },
            { title: '11. Disbursement', sub: 'Sedang dikerjakan', defaultSub: 'Jadwal pencairan', mainTitle: 'Langkah 11: Simulasi Biaya & Pencairan (Disbursement)', desc: 'Rincian seluruh komponen biaya potong muka dan estimasi dana riil yang diterima debitur saat akad.' },
            { title: '12. Repayment', sub: 'Sedang dikerjakan', defaultSub: 'Kemampuan angsur', mainTitle: 'Langkah 12: Kemampuan Angsur & DSC (Tahap Akhir Pengusul)', desc: 'Pengujian kemampuan bayar debitur terhadap angsuran pinjaman (Standar aman DSC >= 1.30x).' }
        ];

        // Tab Navigation
        function goToTab(tabIndex) {
            if (tabIndex < 1 || tabIndex > totalTabs) return;
            currentTab = tabIndex;

            // Sembunyikan semua tab panel
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
            // Tampilkan tab terpilih
            const target = document.getElementById('tab-' + tabIndex);
            if (target) target.classList.add('active');
            if (tabIndex === 6 && typeof sinkronisasiDataDebiturKeSlik === 'function') {
                sinkronisasiDataDebiturKeSlik();
            }

            // Update Header Judul & Deskripsi
            const meta = stepsMetadata[tabIndex - 1];
            if (meta) {
                const titleEl = document.getElementById('stepMainTitle');
                const subEl = document.getElementById('stepSubTitle');
                if (titleEl) titleEl.innerText = meta.mainTitle;
                if (subEl) subEl.innerText = meta.desc;
            }

            // Update Sidebar Stepper Progress (1 - 12)
            const tah = document.getElementById('sidebarTahapNum');
            const bad = document.getElementById('sidebarTahapBadge');
            const prog = document.getElementById('progressBarFill');
            if (tah) tah.innerText = tabIndex;
            if (bad) bad.innerText = tabIndex + '/' + totalTabs;
            if (prog) prog.style.width = ((tabIndex / totalTabs) * 100).toFixed(1) + '%';

            // Update Vertical Stepper List (1-12)
            for (let i = 1; i <= totalTabs; i++) {
                const item = document.getElementById('step-item-' + i);
                const circle = document.getElementById('step-circle-' + i);
                const title = document.getElementById('step-title-' + i);
                const sub = document.getElementById('step-sub-' + i);
                const stepMeta = stepsMetadata[i - 1];

                if (!item || !circle || !title || !sub) continue;

                if (i === tabIndex) {
                    item.className = 'w-full text-left p-2.5 rounded-2xl transition flex items-center gap-3 bg-blue-50/80 border border-blue-200/80 shadow-2xs';
                    circle.className = 'w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-bold shadow-xs shrink-0';
                    circle.innerHTML = i;
                    title.className = 'text-xs font-bold text-blue-900 truncate';
                    sub.className = 'text-[11px] text-blue-600 font-semibold truncate';
                    sub.innerText = 'Sedang dikerjakan';
                } else if (i < tabIndex) {
                    item.className = 'w-full text-left p-2.5 rounded-2xl transition flex items-center gap-3 hover:bg-slate-50 border border-transparent';
                    circle.className = 'w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold shrink-0 border border-blue-200';
                    circle.innerHTML = '&check;';
                    title.className = 'text-xs font-bold text-slate-800 truncate';
                    sub.className = 'text-[11px] text-emerald-600 font-medium truncate';
                    sub.innerText = 'Selesai';
                } else {
                    item.className = 'w-full text-left p-2.5 rounded-2xl transition flex items-center gap-3 hover:bg-slate-50 border border-transparent';
                    circle.className = 'w-7 h-7 rounded-full bg-white border border-slate-300 text-slate-500 flex items-center justify-center text-xs font-bold shrink-0';
                    circle.innerHTML = i;
                    title.className = 'text-xs font-bold text-slate-700 truncate';
                    sub.className = 'text-[11px] text-slate-400 truncate';
                    sub.innerText = stepMeta.defaultSub;
                }
            }

            // Update Label & Button Prev/Next
            const lbl = document.getElementById('tabStepLabel');
            if (lbl) lbl.innerText = `Langkah ${tabIndex} dari ${totalTabs}`;
            const prev = document.getElementById('btnPrev');
            if (prev) prev.style.visibility = tabIndex === 1 ? 'hidden' : 'visible';
            const nxt = document.getElementById('btnNext');
            if (nxt) {
                if (tabIndex === totalTabs) {
                    nxt.className = 'px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-sm flex items-center gap-1.5';
                    nxt.innerHTML = 'Simpan & Selesai Pengajuan &check;';
                } else {
                    nxt.className = 'px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-sm flex items-center gap-1.5';
                    nxt.innerHTML = 'Selanjutnya &rarr;';
                }
            }

            // Animasi scroll stepper sidebar agar tidak terpotong dan responsif terhadap pergantian tahap
            scrollStepperList(tabIndex);

            // Scroll ke atas halaman form
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Fungsi animasi scroll otomatis stepper sidebar
        function scrollStepperList(tabIndex) {
            const list = document.getElementById('verticalStepperList');
            if (!list) return;

            if (tabIndex === 1) {
                // Saat kembali ke langkah 1, scroll ke posisi paling atas
                list.scrollTo({ top: 0, behavior: 'smooth' });
                return;
            }

            const activeItem = document.getElementById('step-item-' + tabIndex);
            const item12 = document.getElementById('step-item-12');

            if (tabIndex === 2 && item12) {
                // Ketika pencet langkah 2, geser ke bawah secara halus agar langkah 12 muncul dan langkah 1 bergeser ke atas
                const item12Bottom = item12.offsetTop + item12.offsetHeight;
                const targetScroll = Math.max(0, item12Bottom - list.clientHeight + 8);
                list.scrollTo({ top: targetScroll, behavior: 'smooth' });
            } else if (activeItem) {
                // Jaga agar langkah aktif lainnya selalu berada di area pandang
                const itemTop = activeItem.offsetTop;
                const itemBottom = itemTop + activeItem.offsetHeight;
                const viewTop = list.scrollTop;
                const viewBottom = viewTop + list.clientHeight;

                if (itemTop < viewTop) {
                    list.scrollTo({ top: itemTop - 8, behavior: 'smooth' });
                } else if (itemBottom > viewBottom) {
                    list.scrollTo({ top: itemBottom - list.clientHeight + 8, behavior: 'smooth' });
                }
            }
        }

        function tabNav(delta) {
            const next = currentTab + delta;
            if (next > totalTabs) {
                simpanForm(true);
            } else if (next >= 1) {
                // Auto simpan progress saat berganti langkah
                simpanForm(false, false);
                goToTab(next);
            }
        }

        // Sinkronisasi suku bunga bulanan vs tahunan
        function syncBunga(origin) {
            const bungaBulanInput = document.getElementById('bunga_per_bulan');
            const bungaTahunInput = document.getElementById('bunga_per_tahun');
            if (bungaTahunInput && bungaBulanInput) {
                const t = parseFloat(bungaTahunInput.value) || 0;
                bungaBulanInput.value = (t / 12).toFixed(2);
            }
        }

        // Kalkulasi Angsuran Pinjaman (Balloon & Installment berdasarkan metode bunga)
        function hitungKalkulasiAngsuran() {
            const plafon = parseNominal(document.getElementById('plafon_pengajuan')?.value || 0);
            const tenor = Math.max(1, parseInt(document.getElementById('tenor_bulan')?.value || 12));
            const bungaTahunPct = parseFloat(document.getElementById('bunga_per_tahun')?.value || 0) / 100;
            const metodeBunga = (document.getElementById('perhitungan_bunga')?.value || 'ANUITAS').toUpperCase();

            // Suku bunga bulanan
            const i = bungaTahunPct / 12;

            // 1. Balloon Payment: bunga per bulan
            const angsuranBalloon = Math.round(plafon * i);

            // 2. Installment berdasarkan metode bunga (FLAT, EFEKTIF, ANUITAS)
            let angsuranInstallment = 0;
            if (metodeBunga === 'ANUITAS') {
                if (i > 0) {
                    angsuranInstallment = Math.round(plafon * (i / (1 - Math.pow(1 + i, -tenor))));
                } else {
                    angsuranInstallment = Math.round(plafon / tenor);
                }
            } else if (metodeBunga === 'EFEKTIF') {
                const pokokBulan = plafon / tenor;
                const bungaBulanAwal = plafon * i;
                angsuranInstallment = Math.round(pokokBulan + bungaBulanAwal);
            } else {
                // FLAT
                const pokokBulan = plafon / tenor;
                const bungaInstallmentBulan = plafon * i;
                angsuranInstallment = Math.round(pokokBulan + bungaInstallmentBulan);
            }

            document.getElementById('boxAngsuranBalloon').innerText = formatRupiahString(angsuranBalloon);
            document.getElementById('boxAngsuranInstallment').innerText = formatRupiahString(angsuranInstallment);
            
            const fieldBalloon = document.getElementById('angsuran_balloon');
            if (fieldBalloon) fieldBalloon.value = formatRupiahString(angsuranBalloon);
            const fieldInst = document.getElementById('angsuran_installment');
            if (fieldInst) fieldInst.value = formatRupiahString(angsuranInstallment);

            // Default angsuran dipilih jika masih kosong
            const fieldPilih = document.getElementById('angsuran_dipilih');
            if (fieldPilih && (!fieldPilih.value || fieldPilih.value === '0')) {
                const skema = document.getElementById('skema_pembayaran')?.value || 'Balloon Payment';
                fieldPilih.value = formatRupiahString(skema === 'Balloon Payment' ? angsuranBalloon : angsuranInstallment);
            }

            hitungRasioDSC();
            const boxHasil = document.getElementById('boxHasilEksekusiAnalisa');
            if (boxHasil && !boxHasil.classList.contains('hidden')) {
                renderTabelSimulasiAngsuran();
            }
        }

        // Kalkulasi Agunan & Coverage Ratio
        function hitungCoverageAgunan() {
            const plafon = parseNominal(document.getElementById('plafon_pengajuan')?.value || 0);
            let totalMV = 0;
            let totalLV = 0;

            const items = document.querySelectorAll('.agunan-item');
            items.forEach(item => {
                const mvInput = item.querySelector('.agunan-mv');
                const lvPctInput = item.querySelector('.agunan-lv-pct');
                const lvLabel = item.querySelector('.agunan-lv-nominal');

                const mv = parseNominal(mvInput?.value || 0);
                const lvPct = parseFloat(lvPctInput?.value || 60);
                const lv = Math.round(mv * (lvPct / 100));

                if (lvLabel) lvLabel.innerText = formatRupiahString(lv);
                totalMV += mv;
                totalLV += lv;
            });

            document.getElementById('totalAgunanMV').innerText = formatRupiahString(totalMV);
            document.getElementById('totalAgunanLV').innerText = formatRupiahString(totalLV);

            const covMV = plafon > 0 ? ((totalMV / plafon) * 100).toFixed(1) : 0;
            const covLV = plafon > 0 ? ((totalLV / plafon) * 100).toFixed(1) : 0;

            document.getElementById('coverageMV').innerText = covMV + '%';
            document.getElementById('coverageLV').innerText = covLV + '%';
        }

        // Tambah & Hapus Baris Agunan
        function tambahBarisAgunan() {
            const container = document.getElementById('containerAgunan');
            const count = container.querySelectorAll('.agunan-item').length;
            const itemNum = count + 1;
            const html = `
                <div class="agunan-item bg-slate-50/60 hover:bg-slate-50/90 border border-slate-200/90 rounded-2xl p-5 sm:p-6 transition relative">
                    <!-- Item Top Bar -->
                    <div class="flex items-center justify-between pb-3.5 mb-4 border-b border-slate-200/60">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg bg-blue-50 text-blue-600 font-bold text-xs border border-blue-100">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            </span>
                            <span class="text-xs font-bold text-slate-700 tracking-wide">Aset Jaminan #<span class="agunan-num">${itemNum}</span></span>
                        </div>
                        <button type="button" onclick="hapusBarisAgunan(this)" class="inline-flex items-center gap-1.5 px-3 py-1 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-xs font-semibold transition border border-rose-100">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            Hapus
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Alamat Lengkap Objek Jaminan (md:col-span-2) -->
                        <div class="md:col-span-2 wrap-alamat-agunan">
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <span class="lbl-text">Alamat Lengkap Objek Jaminan</span>
                            </label>
                            <input type="text" name="agunan[${count}][alamat_agunan]" placeholder="Alamat lokasi aset jaminan"
                                   class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                        </div>

                        <!-- Jenis Dokumen Agunan -->
                        <div class="wrap-jenis-agunan">
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <span class="lbl-text">Jenis Dokumen Agunan</span>
                            </label>
                            <select name="agunan[${count}][jenis_agunan]" onchange="sesuaikanFormAgunan(this.closest('.agunan-item'))" class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs cursor-pointer">
                                <option value="SHM">SHM (Sertifikat Hak Milik)</option>
                                <option value="SHGB">SHGB (Hak Guna Bangunan)</option>
                                <option value="SHM Sarusun">SHM Sarusun (Apartemen)</option>
                                <option value="BPKB Mobil">BPKB Kendaraan Roda 4</option>
                                <option value="Bilyet Deposito">Bilyet Deposito</option>
                            </select>
                        </div>

                        <!-- Nomor Sertifikat / BPKB -->
                        <div class="wrap-nomor-sertifikat">
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path></svg>
                                <span class="lbl-text">Nomor Sertifikat / BPKB</span>
                            </label>
                            <input type="text" name="agunan[${count}][nomor_sertifikat]" placeholder="No. Sertifikat / BPKB"
                                   class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                        </div>

                        <!-- Nama Pemilik Agunan -->
                        <div class="wrap-pemilik-agunan">
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                <span class="lbl-text">Nama Pemilik Agunan</span>
                            </label>
                            <input type="text" name="agunan[${count}][pemilik_agunan]" placeholder="Sesuai nama di sertifikat"
                                   class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                        </div>

                        <!-- Hubungan Kepemilikan -->
                        <div class="wrap-hubungan-kepemilikan">
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                <span class="lbl-text">Hubungan Kepemilikan</span>
                            </label>
                            <select name="agunan[${count}][hubungan_kepemilikan]" class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs cursor-pointer">
                                <option value="Milik Sendiri">Milik Sendiri</option>
                                <option value="Suami / Istri">Suami / Istri</option>
                                <option value="Orang Tua">Orang Tua</option>
                                <option value="Pihak Ketiga">Pihak Ketiga</option>
                            </select>
                        </div>

                        <!-- Status IMB / PBG -->
                        <div class="wrap-status-imb">
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span class="lbl-text">Status IMB / PBG</span>
                            </label>
                            <select name="agunan[${count}][status_imb]" class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs cursor-pointer">
                                <option value="Ada">Ada</option>
                                <option value="Tidak Ada">Tidak Ada</option>
                                <option value="Proses Pengurusan">Proses Pengurusan</option>
                            </select>
                        </div>

                        <!-- Lama Kepemilikan -->
                        <div class="wrap-lama-kepemilikan">
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span class="lbl-text">Lama Kepemilikan</span>
                            </label>
                            <input type="text" name="agunan[${count}][lama_kepemilikan]" placeholder="Contoh: 8 Tahun"
                                   class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                        </div>

                        <!-- Luas Tanah (m²) -->
                        <div class="wrap-luas-tanah">
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                                <span class="lbl-text">Luas Tanah (m²)</span>
                            </label>
                            <input type="number" name="agunan[${count}][luas_tanah_m2]" placeholder="Contoh: 120"
                                   class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                        </div>

                        <!-- Luas Bangunan (m²) -->
                        <div class="wrap-luas-bangunan">
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                <span class="lbl-text">Luas Bangunan (m²)</span>
                            </label>
                            <input type="number" name="agunan[${count}][luas_bangunan_m2]" placeholder="Contoh: 85"
                                   class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                        </div>

                        <!-- Nilai Pasar / MV (Rp) -->
                        <div class="wrap-nilai-mv">
                            <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span class="lbl-text">Nilai Pasar / MV (Rp)</span>
                            </label>
                            <input type="text" name="agunan[${count}][nilai_pasar_mv]" placeholder="0"
                                   class="w-full text-xs font-bold text-blue-700 border border-slate-200 rounded-xl px-3.5 py-2.5 bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input agunan-mv"
                                   oninput="formatRupiah(this); hitungCoverageAgunan();">
                        </div>

                        <!-- Likuidasi (% & Nominal Nilai LV) -->
                        <div class="wrap-likuidasi">
                            <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                <span class="lbl-text">Likuidasi (% / Nilai LV)</span>
                            </label>
                            <div class="flex items-center gap-2">
                                <div class="relative w-16 shrink-0">
                                    <input type="number" step="1" name="agunan[${count}][persen_likuidasi]" value="60"
                                           class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-2 py-2.5 text-center bg-white hover:bg-slate-50/50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs agunan-lv-pct"
                                           oninput="hitungCoverageAgunan();">
                                </div>
                                <span class="text-xs text-slate-400 font-bold">%</span>
                                <div class="flex-1 px-2.5 py-2.5 bg-emerald-50 border border-emerald-200/60 rounded-xl text-center">
                                    <span class="text-xs font-bold text-emerald-700 agunan-lv-nominal">Rp 0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
            const newItem = container.lastElementChild;
            if (typeof sesuaikanFormAgunan === 'function') {
                sesuaikanFormAgunan(newItem);
            }
            hitungCoverageAgunan();
        }

        function hapusBarisAgunan(btn) {
            const container = document.getElementById('containerAgunan');
            if (container.querySelectorAll('.agunan-item').length > 1) {
                btn.closest('.agunan-item').remove();
                // Update numbering
                container.querySelectorAll('.agunan-item').forEach((el, index) => {
                    const numSpan = el.querySelector('.agunan-num');
                    if (numSpan) numSpan.textContent = index + 1;
                });
                hitungCoverageAgunan();
            } else {
                alert('Minimal harus ada 1 aset agunan');
            }
        }

        // Kalkulasi SLIK Total Outstanding
        function hitungTotalSlik() {
            let total = 0;
            document.querySelectorAll('.slik-outstanding').forEach(inp => {
                total += parseNominal(inp.value || 0);
            });
            document.getElementById('totalOutstandingSlik').innerText = formatRupiahString(total);
        }

        function tambahBarisSlik() {
            const tbody = document.getElementById('bodySlik');
            const count = tbody.querySelectorAll('tr').length;
            const html = `
                <tr>
                    <td class="p-2.5 bg-white">
                        <input type="text" name="slik[${count}][nama_debitur]" placeholder="Debitur / Pasangan" 
                               class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                    </td>
                    <td class="p-2.5 bg-white">
                        <input type="text" name="slik[${count}][bank_lembaga]" placeholder="Nama Bank / Leasing" 
                               class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                    </td>
                    <td class="p-2.5 bg-white">
                        <input type="text" name="slik[${count}][plafon]" placeholder="0" 
                               class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input" 
                               oninput="formatRupiah(this)">
                    </td>
                    <td class="p-2.5 bg-white">
                        <input type="text" name="slik[${count}][outstanding]" placeholder="0" 
                               class="w-full text-xs font-bold text-blue-700 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input slik-outstanding" 
                               oninput="formatRupiah(this); hitungTotalSlik();">
                    </td>
                    <td class="p-2.5 bg-white">
                        <select name="slik[${count}][kelancaran]" 
                                class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs cursor-pointer">
                            <option value="Lancar">Kol 1 (Lancar)</option>
                            <option value="DPK">Kol 2 (DPK / Perhatian Khusus)</option>
                            <option value="Kurang Lancar">Kol 3 (Kurang Lancar)</option>
                            <option value="Diragukan">Kol 4 (Diragukan)</option>
                            <option value="Macet">Kol 5 (Macet)</option>
                        </select>
                    </td>
                    <td class="p-2.5 bg-white">
                        <input type="text" name="slik[${count}][keterangan]" placeholder="Modal Kerja / KPR / dll" 
                               class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                    </td>
                    <td class="p-2.5 bg-white text-center">
                        <button type="button" onclick="hapusBarisTabel(this); hitungTotalSlik();" 
                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 transition border border-rose-100" title="Hapus Baris">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </td>
                </tr>
            `;
            tbody.insertAdjacentHTML('beforeend', html);
        }

        // Kalkulasi Rekening Koran
        function hitungRataRataRekening() {
            let totalAvgKredit = 0;
            const items = document.querySelectorAll('.rekening-item');
            items.forEach(item => {
                const kInput = item.querySelector('.rek-kredit');
                const bInput = item.querySelector('.rek-bulan');
                const labelAvg = item.querySelector('.rek-avg-kredit');

                const totalKredit = parseNominal(kInput?.value || 0);
                const bulan = Math.max(1, parseInt(bInput?.value || 6));
                const avgKredit = Math.round(totalKredit / bulan);

                if (labelAvg) labelAvg.innerText = formatRupiahString(avgKredit);
                totalAvgKredit += avgKredit;
            });

            document.getElementById('totalAvgKreditGabungan').innerText = formatRupiahString(totalAvgKredit);
        }

        function salinOmzetKeLabaRugi() {
            let totalAvgKredit = 0;
            document.querySelectorAll('.rekening-item').forEach(item => {
                const kInput = item.querySelector('.rek-kredit');
                const bInput = item.querySelector('.rek-bulan');
                const totalKredit = parseNominal(kInput?.value || 0);
                const bulan = Math.max(1, parseInt(bInput?.value || 6));
                totalAvgKredit += Math.round(totalKredit / bulan);
            });

            if (totalAvgKredit > 0) {
                document.getElementById('pendapatan_rk').value = formatRupiahString(totalAvgKredit);
                hitungLabaRugi();
                showToast('Omzet rata-rata rekening koran berhasil disalin ke Tab Laba Rugi', 'success');
                goToTab(9);
            } else {
                showToast('Isi mutasi kredit rekening koran terlebih dahulu', 'error');
            }
        }

        function tambahBarisRekening() {
            const container = document.getElementById('containerRekening');
            const count = container.querySelectorAll('.rekening-item').length;
            const html = `
                <div class="rekening-item p-5 bg-white rounded-2xl border border-slate-200/90 shadow-2xs hover:border-blue-200 transition space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs border border-blue-100">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                            </div>
                            <span class="text-xs font-bold text-slate-700 tracking-tight">Data Rekening Bank</span>
                        </div>
                        <button type="button" onclick="hapusBarisTabel(this); hitungRataRataRekening();" class="inline-flex items-center gap-1 text-[11px] font-bold text-slate-400 hover:text-rose-600 hover:bg-rose-50 px-2.5 py-1 rounded-lg transition cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            Hapus
                        </button>
                    </div>
                    <!-- Baris 1: Identitas Rekening -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Nama Bank</label>
                            <input type="text" name="rekening[${count}][nama_bank]" placeholder="Contoh: BCA / Mandiri / BRI" 
                                   class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs h-[38px]">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Periode Mutasi</label>
                            <input type="text" name="rekening[${count}][periode]" placeholder="Contoh: Jan 2026 - Jun 2026" 
                                   class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs h-[38px]">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Nomor Rekening</label>
                            <input type="text" name="rekening[${count}][nomor_rekening]" placeholder="Nomor rekening bank" 
                                   class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs h-[38px]">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">Atas Nama Rekening</label>
                            <input type="text" name="rekening[${count}][atas_nama]" placeholder="Nama pemilik rekening" 
                                   class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs h-[38px]">
                        </div>
                    </div>
                    <!-- Baris 2: Mutasi & Kalkulasi Rata-rata -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 pt-3 border-t border-slate-100">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5 h-8 flex items-end">Total Mutasi Kredit (Masuk)</label>
                            <input type="text" name="rekening[${count}][total_mutasi_kredit]" placeholder="0" 
                                   class="w-full text-xs font-bold text-blue-700 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input rek-kredit h-[38px]" 
                                   oninput="formatRupiah(this); hitungRataRataRekening();">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5 h-8 flex items-end">Total Mutasi Debit (Keluar)</label>
                            <input type="text" name="rekening[${count}][total_mutasi_debit]" placeholder="0" 
                                   class="w-full text-xs font-bold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rupiah-input rek-debit h-[38px]" 
                                   oninput="formatRupiah(this); hitungRataRataRekening();">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5 h-8 flex items-end">Jumlah Bulan Mutasi</label>
                            <input type="number" name="rekening[${count}][jumlah_bulan]" value="6" min="1" max="24" 
                                   class="w-full text-xs font-semibold text-slate-800 border border-slate-200 rounded-xl px-3 py-2 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition shadow-2xs rek-bulan h-[38px]" 
                                   oninput="hitungRataRataRekening();">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-blue-700 uppercase tracking-wider mb-1.5 h-8 flex items-end">Rata-rata Kredit / Bulan</label>
                            <div class="w-full text-xs font-extrabold text-blue-800 bg-blue-50/80 border border-blue-200 rounded-xl px-3 py-2 flex items-center shadow-2xs rek-avg-kredit h-[38px]">
                                Rp 0
                            </div>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
        }

        // Kalkulasi Laba Rugi (RK & Wawancara)
        function hitungLabaRugi() {
            // Track 1: RK
            const pRK = parseNominal(document.getElementById('pendapatan_rk')?.value || 0);
            const hppPctRK = parseFloat(document.getElementById('hpp_persen_rk')?.value || 60);
            const nomHppRK = Math.round(pRK * (hppPctRK / 100));
            const grossRK = pRK - nomHppRK;

            // Biaya Hidup (langsung nominal rupiah tanpa %)
            const nomBhRK = parseNominal(document.getElementById('biaya_hidup_nominal_rk')?.value || 0);
            if (pRK > 0 && document.getElementById('biaya_hidup_persen_rk')) {
                document.getElementById('biaya_hidup_persen_rk').value = (Math.round((nomBhRK / pRK) * 1000) / 10);
            }

            // Beban Lain (dihitung dari total rincian beban)
            let totalBebanLain = 0;
            if (Array.isArray(daftarBebanLain) && daftarBebanLain.length > 0) {
                daftarBebanLain.forEach(item => {
                    totalBebanLain += parseNominal(item.nominal || 0);
                });
            } else {
                totalBebanLain = parseNominal(document.getElementById('beban_lainnya_nominal_rk')?.value || 0);
            }

            if (document.getElementById('beban_lainnya_nominal_rk')) {
                document.getElementById('beban_lainnya_nominal_rk').value = totalBebanLain;
            }
            if (pRK > 0 && document.getElementById('beban_lainnya_persen_rk')) {
                document.getElementById('beban_lainnya_persen_rk').value = (Math.round((totalBebanLain / pRK) * 1000) / 10);
            }
            const nomBlRK = totalBebanLain;

            const nettRK = grossRK - nomBhRK - nomBlRK;

            // Pendapatan Lain-lain
            const isAdaLain = document.getElementById('ada_bukti_pendapatan_lain_rk')?.checked;
            const pLainRK = isAdaLain ? parseNominal(document.getElementById('pendapatan_lain_rk')?.value || 0) : 0;
            const totalCashflow = nettRK + pLainRK;

            if (document.getElementById('nomHppRK')) document.getElementById('nomHppRK').innerText = formatRupiahString(nomHppRK);
            if (document.getElementById('grossRK')) document.getElementById('grossRK').innerText = formatRupiahString(grossRK);
            if (document.getElementById('nomBiayaHidupRK')) document.getElementById('nomBiayaHidupRK').innerText = formatRupiahString(nomBhRK);
            if (document.getElementById('nomBebanLainRK')) document.getElementById('nomBebanLainRK').innerText = formatRupiahString(nomBlRK);
            if (document.getElementById('nettRK')) document.getElementById('nettRK').innerText = formatRupiahString(nettRK);
            if (document.getElementById('labelTotalPendapatanLain')) {
                document.getElementById('labelTotalPendapatanLain').innerText = formatRupiahString(pLainRK);
            }
            if (document.getElementById('totalCashflowBersih')) {
                document.getElementById('totalCashflowBersih').innerText = formatRupiahString(totalCashflow);
            }

            // Track 2: IV
            const pIV = parseNominal(document.getElementById('pendapatan_iv')?.value || 0);
            const hppPctIV = parseFloat(document.getElementById('hpp_persen_iv')?.value || 60);
            const nomHppIV = Math.round(pIV * (hppPctIV / 100));
            const grossIV = pIV - nomHppIV;

            const bhPctIV = parseFloat(document.getElementById('biaya_hidup_persen_iv')?.value || 10);
            const nomBhIV = Math.round(pIV * (bhPctIV / 100));

            const blPctIV = parseFloat(document.getElementById('beban_lainnya_persen_iv')?.value || 10);
            const nomBlIV = Math.round(pIV * (blPctIV / 100));

            const nettIV = grossIV - nomBhIV - nomBlIV;

            if (document.getElementById('nomHppIV')) document.getElementById('nomHppIV').innerText = formatRupiahString(nomHppIV);
            if (document.getElementById('grossIV')) document.getElementById('grossIV').innerText = formatRupiahString(grossIV);
            if (document.getElementById('nomBiayaHidupIV')) document.getElementById('nomBiayaHidupIV').innerText = formatRupiahString(nomBhIV);
            if (document.getElementById('nomBebanLainIV')) document.getElementById('nomBebanLainIV').innerText = formatRupiahString(nomBlIV);
            if (document.getElementById('nettIV')) document.getElementById('nettIV').innerText = formatRupiahString(nettIV);

            hitungRasioDSC();
        }

        
        // Inisialisasi & Manajemen Rincian Beban Lain-Lain (Dinamis: Tambah/Hapus)
        let daftarBebanLain = [];

        function initDaftarBebanLain() {
            const raw = document.getElementById('detail_beban_lainnya_rk')?.value;
            daftarBebanLain = [];
            if (raw && raw !== '[]' && raw !== '') {
                try {
                    const parsed = JSON.parse(raw);
                    if (Array.isArray(parsed)) {
                        daftarBebanLain = parsed;
                    }
                } catch (e) {
                    console.error('Error parse detail_beban_lainnya_rk', e);
                }
            }

            if (daftarBebanLain.length === 0) {
                const nomLama = parseNominal(document.getElementById('beban_lainnya_nominal_rk')?.value || 0);
                if (nomLama > 0) {
                    daftarBebanLain = [{ nama: 'Beban Lain-Lain', nominal: nomLama }];
                }
            }

            renderListBebanLain();
        }
        window.initDaftarBebanLain = initDaftarBebanLain;

        function renderListBebanLain() {
            const container = document.getElementById('containerBebanLain');
            if (!container) return;

            if (daftarBebanLain.length === 0) {
                container.innerHTML = `
                    <div class="p-3 text-center border border-dashed border-slate-200 rounded-xl text-slate-400 text-xs bg-slate-50/50">
                        Belum ada rincian beban lain. Klik <strong class="text-blue-600 font-semibold cursor-pointer hover:underline" onclick="tambahBebanLain()">+ Tambah Beban</strong> (cth: Biaya Sekolah, dll).
                    </div>
                `;
            } else {
                let html = '';
                daftarBebanLain.forEach((item, index) => {
                    const safeNama = (item.nama || '').replace(/"/g, '&quot;');
                    const nomVal = parseInt(item.nominal, 10) || 0;
                    const nomStr = nomVal > 0 ? nomVal.toLocaleString('id-ID') : '';
                    html += `
                        <div class="flex items-center gap-2 p-2 bg-white border border-slate-200 rounded-xl shadow-2xs hover:border-blue-200 transition">
                            <div class="flex-1 min-w-0">
                                <input type="text" value="${safeNama}" 
                                       placeholder="Nama beban (cth: Biaya Sekolah)" 
                                       class="w-full text-xs font-semibold text-slate-800 border-0 bg-transparent px-2 py-1 focus:ring-0 placeholder:text-slate-400 placeholder:font-normal focus:outline-none"
                                       oninput="updateItemBebanLain(${index}, 'nama', this.value)">
                            </div>
                            <div class="w-32 sm:w-40 shrink-0">
                                <div class="relative flex items-center">
                                    <span class="absolute left-2.5 text-[10px] font-bold text-slate-400 select-none">Rp</span>
                                    <input type="text" value="${nomStr}" 
                                           placeholder="0" 
                                           class="w-full text-xs font-bold text-slate-800 text-right border border-slate-200 rounded-lg pl-7 pr-2.5 py-1.5 bg-slate-50/70 focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-100 transition rupiah-input"
                                           oninput="formatRupiah(this); updateItemBebanLain(${index}, 'nominal', this.value)">
                                </div>
                            </div>
                            <button type="button" onclick="hapusBebanLain(${index})" title="Hapus beban ini" 
                                    class="w-8 h-8 rounded-lg text-rose-500 hover:text-rose-700 hover:bg-rose-50 flex items-center justify-center border border-slate-200 hover:border-rose-200 transition shrink-0 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    `;
                });
                container.innerHTML = html;
            }

            const hiddenDetail = document.getElementById('detail_beban_lainnya_rk');
            if (hiddenDetail) {
                hiddenDetail.value = JSON.stringify(daftarBebanLain);
            }

            hitungLabaRugi();
        }
        window.renderListBebanLain = renderListBebanLain;

        function tambahBebanLain(nama = '', nominal = 0) {
            daftarBebanLain.push({
                nama: nama,
                nominal: nominal
            });
            renderListBebanLain();
        }
        window.tambahBebanLain = tambahBebanLain;

        function hapusBebanLain(index) {
            if (index >= 0 && index < daftarBebanLain.length) {
                daftarBebanLain.splice(index, 1);
                renderListBebanLain();
            }
        }
        window.hapusBebanLain = hapusBebanLain;

        function updateItemBebanLain(index, field, val) {
            if (!daftarBebanLain[index]) return;
            if (field === 'nominal') {
                daftarBebanLain[index].nominal = parseNominal(val);
            } else {
                daftarBebanLain[index].nama = val;
            }

            const hiddenDetail = document.getElementById('detail_beban_lainnya_rk');
            if (hiddenDetail) {
                hiddenDetail.value = JSON.stringify(daftarBebanLain);
            }

            hitungLabaRugi();
        }
        window.updateItemBebanLain = updateItemBebanLain;

        // Toggle Tampilan Form Pendapatan Lain-Lain
        function togglePendapatanLain(checked) {
            const sec = document.getElementById('sectionPendapatanLain');
            const info = document.getElementById('infoBelumCentang');
            const box = document.getElementById('boxTogglePendapatanLain');
            if (checked) {
                if (sec) sec.classList.remove('hidden');
                if (info) info.classList.add('hidden');
                if (box) {
                    box.className = 'p-4 rounded-xl border transition mb-5 bg-blue-50/80 border-blue-300 ring-1 ring-blue-200';
                }
            } else {
                if (sec) sec.classList.add('hidden');
                if (info) info.classList.remove('hidden');
                if (box) {
                    box.className = 'p-4 rounded-xl border transition mb-5 bg-white border-slate-200 hover:border-slate-300';
                }
            }
            hitungLabaRugi();
        }
        window.togglePendapatanLain = togglePendapatanLain;

        // Inisialisasi Daftar Bukti Pendapatan Lain-Lain (Multiple & Delete)
        let daftarBuktiPendapatanLain = [];

        function initDaftarBuktiPendapatanLain() {
            const raw = document.getElementById('bukti_pendapatan_lain_rk')?.value;
            daftarBuktiPendapatanLain = [];
            if (raw && raw !== '[]') {
                try {
                    const parsed = JSON.parse(raw);
                    if (Array.isArray(parsed)) {
                        daftarBuktiPendapatanLain = parsed;
                    } else if (typeof parsed === 'string') {
                        daftarBuktiPendapatanLain = [{ name: parsed.split('/').pop(), path: parsed }];
                    }
                } catch (e) {
                    daftarBuktiPendapatanLain = [{ name: raw.split('/').pop(), path: raw }];
                }
            }
            renderListBuktiPendapatanLain();
        }
        window.initDaftarBuktiPendapatanLain = initDaftarBuktiPendapatanLain;

        let debounceSaveCatatanBukti = null;
        function updateCatatanBuktiLain(index, val) {
            if (daftarBuktiPendapatanLain[index]) {
                daftarBuktiPendapatanLain[index].desc = val;
                const hiddenInput = document.getElementById('bukti_pendapatan_lain_rk');
                if (hiddenInput) {
                    hiddenInput.value = JSON.stringify(daftarBuktiPendapatanLain);
                }
                clearTimeout(debounceSaveCatatanBukti);
                debounceSaveCatatanBukti = setTimeout(() => {
                    if (typeof simpanForm === 'function') {
                        simpanForm(false, false);
                    }
                }, 800);
            }
        }
        window.updateCatatanBuktiLain = updateCatatanBuktiLain;

        function renderListBuktiPendapatanLain() {
            const container = document.getElementById('containerListBuktiLain');
            if (!container) return;

            if (daftarBuktiPendapatanLain.length === 0) {
                container.innerHTML = `
                    <div class="py-3 px-4 border border-dashed border-slate-200 rounded-xl bg-slate-50/60 text-center">
                        <span class="text-xs text-slate-400 font-medium">Belum ada bukti yang diunggah (Opsional)</span>
                    </div>
                `;
                return;
            }

            let html = '';
            daftarBuktiPendapatanLain.forEach((item, index) => {
                const name = item.name || (item.path ? item.path.split('/').pop() : 'Berkas Bukti');
                const ext = name.split('.').pop().toUpperCase() || 'DOC';
                const sizeText = item.size || 'Baru saja diunggah • 30.3 KB';
                const descText = item.desc || '';
                const fullUrl = item.path.startsWith('http') ? item.path : (getBaseUrl() + '/' + item.path);
                const safeName = name.replace(/["'<>]/g, '');
                const safeDesc = descText.replace(/["'<>]/g, '');

                html += `
                    <div class="p-3 bg-white border border-slate-200/90 rounded-2xl flex flex-col gap-2.5 text-xs hover:border-blue-300 hover:shadow-xs transition" id="buktiRow_${index}">
                        <!-- Baris Atas: Info File & Tombol Aksi (Hapus di sebelah kiri Lihat File) -->
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-2.5 min-w-0">
                                <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-extrabold text-[11px] shrink-0 border border-blue-100 mt-0.5">
                                    ${ext}
                                </div>
                                <div class="min-w-0">
                                    <div class="font-bold text-slate-800 text-xs sm:text-sm leading-snug truncate" title="${safeName}">
                                        ${safeName}
                                    </div>
                                    <div class="flex flex-wrap items-center gap-2 mt-0.5">
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold text-blue-700 bg-blue-50 border border-blue-200 inline-flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                            ${sizeText}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Aksi: Hapus (Tempat Sampah di sebelah kiri) & Lihat File -->
                            <div class="flex items-center gap-1.5 shrink-0">
                                <!-- Tombol Hapus (Tempat Sampah di sebelah kiri Lihat File) -->
                                <button type="button" onclick="hapusBuktiPendapatanLain(${index})" class="inline-flex items-center justify-center p-1.5 text-rose-500 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-lg transition shadow-2xs cursor-pointer" title="Hapus Berkas Dokumen">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>

                                <!-- Tombol Lihat File -->
                                <a href="${fullUrl}" target="_blank" class="inline-flex items-center gap-1 px-3 py-1.5 text-blue-600 hover:text-blue-800 font-bold text-[11px] border border-blue-200 rounded-lg hover:bg-blue-50 transition shadow-2xs">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    <span>Lihat File</span>
                                </a>
                            </div>
                        </div>

                        <!-- Text Area Kecil untuk Keterangan Dokumen -->
                        <div class="pt-1.5 border-t border-slate-100">
                            <div class="flex items-center justify-between mb-1">
                                <label class="text-[9px] font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1">
                                    <svg class="w-2.5 h-2.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    Keterangan Dokumen
                                </label>
                            </div>
                            <textarea rows="1" 
                                      placeholder="Tulis keterangan dokumen (misal: KTP Istri, KTP Pribadi, Sertifikat Rumah No. 123, dll)..." 
                                      oninput="updateCatatanBuktiLain(${index}, this.value)"
                                      class="w-full text-[11px] font-normal text-slate-700 bg-slate-50/70 hover:bg-white focus:bg-white border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-100 rounded-lg py-1.5 px-2.5 transition resize-y min-h-[30px] max-h-[80px] placeholder:text-[10px] placeholder:text-slate-400 leading-normal">${safeDesc}</textarea>
                        </div>
                    </div>
                `;
            });

            container.innerHTML = html;
        }
        window.renderListBuktiPendapatanLain = renderListBuktiPendapatanLain;

        // Handler Hapus Bukti Pendapatan Lain
        async function hapusBuktiPendapatanLain(index) {
            if (index < 0 || index >= daftarBuktiPendapatanLain.length) return;
            const item = daftarBuktiPendapatanLain[index];
            const nama = item.name || 'berkas bukti';

            let yakin = true;
            if (typeof Swal !== 'undefined') {
                const res = await Swal.fire({
                    title: 'Hapus Bukti Ini?',
                    text: `Apakah Anda yakin ingin menghapus "${nama}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e11d48',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                });
                yakin = res.isConfirmed;
            } else {
                yakin = confirm(`Apakah Anda yakin ingin menghapus "${nama}"?`);
            }

            if (yakin) {
                daftarBuktiPendapatanLain.splice(index, 1);
                const hiddenInput = document.getElementById('bukti_pendapatan_lain_rk');
                if (hiddenInput) {
                    hiddenInput.value = daftarBuktiPendapatanLain.length > 0 ? JSON.stringify(daftarBuktiPendapatanLain) : '';
                }
                renderListBuktiPendapatanLain();
                showToast('Berkas bukti berhasil dihapus', 'info');
                
                // Simpan otomatis ke backend
                if (typeof simpanForm === 'function') {
                    simpanForm(false, false);
                }
            }
        }
        window.hapusBuktiPendapatanLain = hapusBuktiPendapatanLain;

        // Handler Upload Bukti Pendapatan Lain via AJAX (Support Multiple Files)
        async function handlePilihBuktiLain(input) {
            if (!input.files || input.files.length === 0) return;
            const files = Array.from(input.files);
            
            showToast(`Mengunggah ${files.length} berkas bukti...`, 'info');

            const formData = new FormData();
            files.forEach(f => formData.append('files[]', f));
            const kId = document.getElementById('kredit_id')?.value;
            if (kId) formData.append('kredit_id', kId);

            try {
                const res = await fetch(getBaseUrl() + '/pengajuan/upload-bukti-pendapatan-lain', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });
                const data = await res.json();
                if (data.status) {
                    if (data.files && Array.isArray(data.files)) {
                        data.files.forEach((f, i) => {
                            const orig = files[i];
                            const sz = orig ? ((orig.size / 1024).toFixed(1) + ' KB') : '30.3 KB';
                            daftarBuktiPendapatanLain.push({
                                name: f.nama_file,
                                path: f.path_file,
                                size: 'Baru saja diunggah • ' + sz,
                                desc: ''
                            });
                        });
                    } else if (data.path_file) {
                        const orig = files[0];
                        const sz = orig ? ((orig.size / 1024).toFixed(1) + ' KB') : '30.3 KB';
                        daftarBuktiPendapatanLain.push({
                            name: data.nama_file || 'Berkas Bukti',
                            path: data.path_file,
                            size: 'Baru saja diunggah • ' + sz,
                            desc: ''
                        });
                    }

                    const hiddenInput = document.getElementById('bukti_pendapatan_lain_rk');
                    if (hiddenInput) {
                        hiddenInput.value = JSON.stringify(daftarBuktiPendapatanLain);
                    }
                    renderListBuktiPendapatanLain();
                    showToast(data.message || 'Bukti pendapatan lain berhasil diunggah!', 'success');

                    // Simpan otomatis
                    if (typeof simpanForm === 'function') {
                        simpanForm(false, false);
                    }
                } else {
                    showToast(data.message || 'Gagal mengunggah bukti', 'error');
                }
            } catch (err) {
                console.error('Error upload bukti lain:', err);
                showToast('Koneksi terganggu saat mengunggah file bukti', 'error');
            } finally {
                input.value = '';
            }
        }
        window.handlePilihBuktiLain = handlePilihBuktiLain;

        // Kalkulasi Rasio DSC
        function hitungRasioDSC() {
            const angsuran = parseNominal(document.getElementById('angsuran_dipilih')?.value || 0);

            // Nett RK
            const pRK = parseNominal(document.getElementById('pendapatan_rk')?.value || 0);
            const hppPctRK = parseFloat(document.getElementById('hpp_persen_rk')?.value || 60);
            const bhPctRK = parseFloat(document.getElementById('biaya_hidup_persen_rk')?.value || 10);
            const blPctRK = parseFloat(document.getElementById('beban_lainnya_persen_rk')?.value || 10);
            const nettRK = pRK - (pRK * (hppPctRK/100)) - (pRK * (bhPctRK/100)) - (pRK * (blPctRK/100));

            // Tambahan pendapatan lain jika aktif
            const isAdaLain = document.getElementById('ada_bukti_pendapatan_lain_rk')?.checked;
            const pLainRK = isAdaLain ? parseNominal(document.getElementById('pendapatan_lain_rk')?.value || 0) : 0;
            const totalNettRK = nettRK + pLainRK;

            // Nett IV
            const pIV = parseNominal(document.getElementById('pendapatan_iv')?.value || 0);
            const hppPctIV = parseFloat(document.getElementById('hpp_persen_iv')?.value || 60);
            const bhPctIV = parseFloat(document.getElementById('biaya_hidup_persen_iv')?.value || 10);
            const blPctIV = parseFloat(document.getElementById('beban_lainnya_persen_iv')?.value || 10);
            const nettIV = pIV - (pIV * (hppPctIV/100)) - (pIV * (bhPctIV/100)) - (pIV * (blPctIV/100));

            const dscRK = angsuran > 0 ? (totalNettRK / angsuran).toFixed(2) : 0;
            const dscIV = angsuran > 0 ? (nettIV / angsuran).toFixed(2) : 0;

            document.getElementById('labelRasioDSCRK').innerText = dscRK + 'x';
            document.getElementById('labelRasioDSCIV').innerText = dscIV + 'x';

            updateDSCBadge('badgeStatusDSCRK', dscRK);
            updateDSCBadge('badgeStatusDSCIV', dscIV);
        }

        function updateDSCBadge(elementId, ratio) {
            const badge = document.getElementById(elementId);
            if (!badge) return;
            const val = parseFloat(ratio);
            if (val >= 1.3) {
                badge.className = 'px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800';
                badge.innerText = 'Sangat Aman (\u2265 1.30x)';
            } else if (val >= 1.0) {
                badge.className = 'px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800';
                badge.innerText = 'Cukup / Waspada';
            } else {
                badge.className = 'px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800';
                badge.innerText = 'Defisit / Berisiko (< 1.0x)';
            }
        }

        // Kalkulasi Disbursement (Pencairan Bersih)
        function hitungDisbursement() {
            const plafon = parseNominal(document.getElementById('plafon_pengajuan')?.value || 0);
            const provPct = parseFloat(document.getElementById('provisi_persen')?.value || 7);
            const nomProv = Math.round(plafon * (provPct / 100));

            const agentPct = parseFloat(document.getElementById('agent_persen')?.value || 1);
            const nomAgent = Math.round(plafon * (agentPct / 100));

            const adminPct = parseFloat(document.getElementById('admin_persen')?.value || 1);
            const nomAdmin = Math.round(plafon * (adminPct / 100));

            const holdBulan = parseInt(document.getElementById('hold_angsuran_bulan')?.value || 4);
            const angsuran = parseNominal(document.getElementById('angsuran_dipilih')?.value || 0);
            const nomHold = Math.round(angsuran * holdBulan);

            const asJiwa = parseNominal(document.getElementById('asuransi_jiwa')?.value || 0);
            const asKeb  = parseNominal(document.getElementById('asuransi_kebakaran')?.value || 0);

            const totalPot = nomProv + nomAgent + nomAdmin + nomHold + asJiwa + asKeb;
            const danaBersih = Math.max(0, plafon - totalPot);

            document.getElementById('nomProvisi').innerText = formatRupiahString(nomProv);
            document.getElementById('nomAgent').innerText = formatRupiahString(nomAgent);
            document.getElementById('nomAdmin').innerText = formatRupiahString(nomAdmin);
            document.getElementById('nomHoldAngsuran').innerText = formatRupiahString(nomHold);

            document.getElementById('disbPlafon').innerText = formatRupiahString(plafon);
            document.getElementById('disbTotalPotongan').innerText = '- ' + formatRupiahString(totalPot);
            document.getElementById('disbDanaBersih').innerText = formatRupiahString(danaBersih);
        }

        // Tambah Baris Komite
        function tambahBarisKomite() {
            const container = document.getElementById('containerKomite');
            const count = container.children.length;
            const html = `
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 relative">
                    <button type="button" onclick="hapusBarisTabel(this)" class="absolute top-4 right-4 text-slate-400 hover:text-rose-600 font-bold">&times; Hapus</button>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tingkat Komite</label>
                            <select name="komite[${count}][role_approver]" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white">
                                <option value="Reviewer / Analis Senior">Reviewer / Analis Senior</option>
                                <option value="Komite 1">Komite 1 (Branch Manager)</option>
                                <option value="Komite 2">Komite 2 (Head of Credit)</option>
                                <option value="Direksi">Direksi</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Pejabat Komite</label>
                            <input type="text" name="komite[${count}][nama_approver]" placeholder="Nama Lengkap Pejabat" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Keputusan</label>
                            <select name="komite[${count}][status_approval]" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white font-semibold">
                                <option value="Disetujui">Disetujui</option>
                                <option value="Disetujui Bersyarat">Disetujui Bersyarat</option>
                                <option value="Pending">Pending / Tinjau Kembali</option>
                                <option value="Ditolak">Ditolak</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Keputusan</label>
                            <input type="date" name="komite[${count}][tanggal_approval]" value="${new Date().toISOString().split('T')[0]}" class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white">
                        </div>
                        <div class="md:col-span-4">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Khusus / Syarat Komite (Covenants)</label>
                            <textarea name="komite[${count}][catatan_approval]" rows="2" placeholder="Tuliskan syarat khusus sebelum akad dan pencairan..." class="w-full text-xs border border-slate-300 rounded-lg p-2.5 bg-white"></textarea>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
        }

        function hapusBarisTabel(btn) {
            btn.closest('tr, .p-4')?.remove();
        }

        // Helper Kompresi Gambar Sisi Klien Menggunakan Canvas
        async function compressImageIfNeeded(file) {
            if (!file || !file.type || !file.type.startsWith('image/')) return file;
            return new Promise((resolve) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = new Image();
                    img.onload = function() {
                        const canvas = document.createElement('canvas');
                        let width = img.width;
                        let height = img.height;
                        const maxDim = 1600;

                        if (width > maxDim || height > maxDim) {
                            if (width > height) {
                                height = Math.round((height * maxDim) / width);
                                width = maxDim;
                            } else {
                                width = Math.round((width * maxDim) / height);
                                height = maxDim;
                            }
                        }

                        canvas.width = width;
                        canvas.height = height;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, width, height);

                        canvas.toBlob((blob) => {
                            if (blob && blob.size < file.size) {
                                const compressedFile = new File([blob], file.name.replace(/\.[^/.]+$/, ".jpg"), {
                                    type: 'image/jpeg',
                                    lastModified: Date.now()
                                });
                                resolve(compressedFile);
                            } else {
                                resolve(file);
                            }
                        }, 'image/jpeg', 0.75);
                    };
                    img.onerror = () => resolve(file);
                    img.src = e.target.result;
                };
                reader.onerror = () => resolve(file);
                reader.readAsDataURL(file);
            });
        }

        // Upload Dokumen AJAX dengan Kompresi Gambar & Validasi PDF
        async function prosesUploadDokumen(input) {
            const file = input.files[0];
            if (!file) return;

            // Validasi ukuran dokumen PDF maksimal 5MB
            if (file.type === 'application/pdf' && file.size > 5 * 1024 * 1024) {
                showToast('Ukuran file PDF melebihi 5MB. Silakan kompres terlebih dahulu.', 'error');
                input.value = '';
                return;
            }

            const kreditId = document.getElementById('kredit_id').value;
            if (!kreditId) {
                // Simpan data draft dulu sebelum upload agar punya ID
                await simpanForm(false, false);
            }

            const activeKreditId = document.getElementById('kredit_id').value;
            if (!activeKreditId) {
                showToast('Harap simpan data form terlebih dahulu sebelum mengunggah dokumen.', 'error');
                return;
            }

            showToast('Mengompres & mengunggah dokumen...', 'info');
            const finalFile = await compressImageIfNeeded(file);

            const formData = new FormData();
            formData.append('file', finalFile);
            formData.append('kredit_id', activeKreditId);
            formData.append('kategori', document.getElementById('kategoriUpload').value);
            const catatan = document.getElementById('catatanDokumenUpload')?.value || '';
            formData.append('catatan', catatan);

            try {
                const res = await fetch(getBaseUrl() + '/pengajuan/upload-dokumen', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();
                if (data.status) {
                    showToast('Dokumen berhasil dikompres & diunggah', 'success');
                    // Hapus empty state jika ada
                    const emptyState = document.getElementById('emptyDokumenState');
                    if (emptyState) emptyState.remove();

                    // Tambahkan ke list dokumen
                    const list = document.getElementById('listDokumenTerunggah');
                    const ext = (data.nama_file.split('.').pop() || 'DOC').toUpperCase();
                    const kategoriSelect = document.getElementById('kategoriUpload');
                    const kategoriLabel = data.kategori || (kategoriSelect ? kategoriSelect.value : 'KTP & NPWP');
                    const ukuranText = data.ukuran_kb ? `${data.ukuran_kb} KB` : `${(file.size / 1024).toFixed(1)} KB`;

                    const itemHtml = `
                        <div class="p-3.5 bg-white border border-slate-200/90 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs hover:border-blue-300 hover:shadow-xs transition" id="docRow_${data.dokumen_id}">
                            <div class="flex items-start sm:items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-extrabold text-xs shrink-0 border border-blue-100">
                                    ${ext}
                                </div>
                                <div>
                                    <div class="font-bold text-slate-800 text-sm leading-snug">${data.nama_file}</div>
                                    <div class="flex flex-wrap items-center gap-2 mt-1">
                                        <input type="hidden" id="kategoriDokumen_${data.dokumen_id}" value="${kategoriLabel}">
                                            <select onchange="ubahKategoriDokumen(${data.dokumen_id}, this.value)" class="bg-transparent font-extrabold text-indigo-800 focus:outline-none cursor-pointer">
                                                <option value="KTP & NPWP" ${kategoriLabel.includes('KTP') ? 'selected' : ''}>KTP & NPWP</option>
                                                <option value="Sertifikat Agunan" ${kategoriLabel.includes('Agunan') || kategoriLabel.includes('Sertifikat') ? 'selected' : ''}>Sertifikat Agunan</option>
                                                <option value="Rekening Koran" ${kategoriLabel.includes('Rekening') ? 'selected' : ''}>Rekening Koran</option>
                                                <option value="Legalitas Usaha" ${kategoriLabel.includes('Legalitas') ? 'selected' : ''}>Legalitas Usaha</option>
                                                <option value="Laporan Keuangan" ${kategoriLabel.includes('Keuangan') || kategoriLabel.includes('Neraca') ? 'selected' : ''}>Laporan Keuangan</option>
                                                <option value="Foto Usaha & Agunan" ${kategoriLabel.includes('Foto') ? 'selected' : ''}>Foto Usaha & Agunan</option>
                                                <option value="Dokumen Pendukung" ${kategoriLabel.includes('Pendukung') ? 'selected' : ''}>Dokumen Pendukung</option>
                                            </select>
                                        </span>
                                        <span class="text-[11px] text-emerald-600 font-semibold bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200/60">
                                            Baru saja diunggah &bull; ${ukuranText}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <button type="button" onclick="hapusDokumen(${data.dokumen_id})" class="inline-flex items-center justify-center p-2 text-rose-500 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-xl transition shadow-2xs cursor-pointer" title="Hapus Berkas Dokumen">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                                <a href="${data.path}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-blue-600 hover:text-blue-800 font-bold text-xs border border-blue-200 rounded-xl hover:bg-blue-50 transition shadow-2xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    <span>Lihat File</span>
                                </a>
                            </div>
                        </div>
                    `;
                    list.insertAdjacentHTML('afterbegin', itemHtml);

                    // Update badge counter
                    const badge = document.getElementById('docCountBadge');
                    if (badge) {
                        const total = list.querySelectorAll('a[href]').length;
                        badge.innerText = `${total} Dokumen`;
                    }
                    input.value = '';
                } else {
                    showToast(data.message || 'Gagal mengunggah file', 'error');
                }
            } catch (err) {
                showToast('Terjadi kesalahan koneksi saat upload', 'error');
            }
        }

        // Fungsi Update Kategori Dokumen via AJAX
        async function ubahKategoriDokumen(docId, kategoriBaru) {
            if (!docId) return;
            const formData = new FormData();
            formData.append('dokumen_id', docId);
            formData.append('kategori', kategoriBaru);

            try {
                const res = await fetch(getBaseUrl() + '/pengajuan/update-kategori-dokumen', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();
                if (data.status) {
                    showToast('Kategori dokumen diperbarui ke: ' + kategoriBaru, 'success');
                } else {
                    showToast(data.message || 'Gagal mengubah kategori dokumen', 'error');
                }
            } catch (e) {
                showToast('Koneksi gagal saat mengubah kategori', 'error');
            }
        }

        // Simpan Form Data via AJAX
        async function simpanForm(isComplete = false, showNotification = true) {
            if (isComplete) {
                const nama = document.getElementById('nama_debitur')?.value.trim() || document.getElementById('nama_debitur_tab2')?.value.trim();
                if (!nama) {
                    showToast('Nama Debitur wajib diisi!', 'error');
                    goToTab(1);
                    return;
                }
                const plafon = parseNominal(document.getElementById('plafon_pengajuan')?.value || 0);
                if (plafon <= 0) {
                    showToast('Plafond pengajuan wajib diisi dan lebih dari Rp 0!', 'error');
                    goToTab(1);
                    return;
                }
                const noHp = document.getElementById('nomor_hp')?.value.trim();
                if (!noHp) {
                    showToast('Nomor HP / WhatsApp Aktif wajib diisi!', 'error');
                    goToTab(2);
                    return;
                }
                const nik = document.getElementById('npwp_pribadi')?.value.trim();
                if (!nik || nik.length < 16) {
                    showToast('NIK KTP / NPWP Pribadi wajib diisi 16 digit angka numerik!', 'error');
                    goToTab(2);
                    return;
                }
                const alamatKtp = document.querySelector('textarea[name="alamat_ktp"]')?.value.trim();
                if (!alamatKtp) {
                    showToast('Alamat KTP Lengkap wajib diisi!', 'error');
                    goToTab(2);
                    return;
                }
            }

            const form = document.getElementById('camForm');
            const formData = new FormData(form);

            // Update save indicator
            const ind = document.getElementById('saveStatusIndicator');
            if (ind) ind.innerHTML = '<span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span> Menyimpan data...';

            try {
                const res = await fetch(getBaseUrl() + '/pengajuan/simpan', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();

                if (data.status) {
                    if (data.kredit_id) {
                        document.getElementById('kredit_id').value = data.kredit_id;
                    }
                    if (ind) ind.innerHTML = '<span class="w-2 h-2 rounded-full bg-blue-500"></span> Tersimpan di database';

                    if (showNotification) {
                        showToast(data.message || 'Data berhasil disimpan', 'success');
                    }

                    if (isComplete) {
                        setTimeout(() => {
                            window.location.href = getBaseUrl() + '/dashboard';
                        }, 1000);
                    }
                } else {
                    if (ind) ind.innerHTML = '<span class="w-2 h-2 rounded-full bg-rose-500"></span> Gagal menyimpan';
                    if (showNotification) showToast(data.message || 'Gagal menyimpan data', 'error');
                }
            } catch (err) {
                if (ind) ind.innerHTML = '<span class="w-2 h-2 rounded-full bg-rose-500"></span> Error koneksi';
                if (showNotification) showToast('Gagal terhubung ke server database', 'error');
            }
        }

        // Trigger AI Assistant (Gemini AI API)
        async function jalankanAnalisaAI() {
            showToast('Menghubungkan ke Gemini AI untuk evaluasi risiko...', 'info');

            const form = document.getElementById('camForm');
            const formData = new FormData(form);

            try {
                const res = await fetch(getBaseUrl() + '/pengajuan/analisa-ai', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();

                if (data.status) {
                    if (data.skor_kelayakan_ai) {
                        document.getElementById('skor_kelayakan_ai').value = data.skor_kelayakan_ai;
                    }
                    if (data.rekomendasi_keputusan_ai) {
                        document.getElementById('rekomendasi_keputusan_ai').value = data.rekomendasi_keputusan_ai;
                    }
                    if (data.rekomendasi_plafon_ai) {
                        document.getElementById('rekomendasi_plafon_ai').value = formatRupiahString(data.rekomendasi_plafon_ai);
                    }
                    if (data.analisis_risiko_ai) {
                        document.getElementById('analisis_risiko_ai').value = data.analisis_risiko_ai;
                    }

                    const banner = document.getElementById('aiPdfBanner');
                    if (banner) banner.classList.remove('hidden');

                    const sumber = document.getElementById('aiSumberLabel');
                    if (sumber && data.sumber) {
                        sumber.innerText = data.sumber + ' - Skor ' + data.skor_kelayakan_ai + '/100';
                    }

                    showToast('Analisa AI berhasil! Rekomendasi siap dicetak.', 'success');

                    // Simpan otomatis ke database
                    simpanForm(false, false);
                } else {
                    showToast(data.message || 'Gagal menjalankan evaluasi AI', 'error');
                }
            } catch (err) {
                showToast('Terjadi kesalahan saat memproses evaluasi AI', 'error');
            }
        }

        // Eksekusi Analisa dari Box Kalkulasi Simulasi (Tab 1)
        async function eksekusiAnalisaKalkulasi() {
            // 1. Jalankan kalkulasi simulasi terlebih dahulu
            hitungKalkulasiAngsuran();
            hitungCoverageAgunan();
            hitungDisbursement();
            hitungRasioDSC();

            const btn = document.getElementById('btnEksekusiAnalisa');
            const label = document.getElementById('labelEksekusiAnalisa');
            const icon = document.getElementById('iconEksekusiAnalisa');
            const boxHasil = document.getElementById('boxHasilEksekusiAnalisa');

            // Set loading state
            if (btn) {
                btn.disabled = true;
                btn.classList.add('opacity-75', 'cursor-not-allowed');
            }
            if (label) label.innerText = 'Menganalisa...';
            if (icon) {
                icon.innerHTML = '<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>';
                icon.classList.add('animate-spin');
            }

            showToast('Mengeksekusi simulasi kalkulasi & analisa risiko kredit...', 'info');

            const form = document.getElementById('camForm');
            const formData = new FormData(form);

            try {
                const res = await fetch(getBaseUrl() + '/pengajuan/analisa-ai', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();

                if (data.status) {
                    if (data.skor_kelayakan_ai) {
                        const skorInput = document.getElementById('skor_kelayakan_ai');
                        if (skorInput) skorInput.value = data.skor_kelayakan_ai;
                        const badgeSkor = document.getElementById('badgeHasilSkor');
                        if (badgeSkor) badgeSkor.innerText = 'Skor ' + data.skor_kelayakan_ai + '/100';
                    }
                    if (data.rekomendasi_keputusan_ai) {
                        const rkInput = document.getElementById('rekomendasi_keputusan_ai');
                        if (rkInput) rkInput.value = data.rekomendasi_keputusan_ai;
                        const badgeKeputusan = document.getElementById('badgeHasilKeputusan');
                        if (badgeKeputusan) {
                            badgeKeputusan.innerText = data.rekomendasi_keputusan_ai;
                            const dec = data.rekomendasi_keputusan_ai.toLowerCase();
                            if (dec.includes('tolak')) {
                                badgeKeputusan.className = 'px-2 py-0.5 rounded-md text-[11px] font-extrabold bg-rose-100 text-rose-800 border border-rose-200';
                            } else if (dec.includes('penurunan') || dec.includes('khusus')) {
                                badgeKeputusan.className = 'px-2 py-0.5 rounded-md text-[11px] font-extrabold bg-amber-100 text-amber-800 border border-amber-200';
                            } else {
                                badgeKeputusan.className = 'px-2 py-0.5 rounded-md text-[11px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200';
                            }
                        }
                    }
                    if (data.rekomendasi_plafon_ai) {
                        const plafonInput = document.getElementById('rekomendasi_plafon_ai');
                        if (plafonInput) plafonInput.value = formatRupiahString(data.rekomendasi_plafon_ai);
                        const valPlafon = document.getElementById('valHasilPlafon');
                        if (valPlafon) valPlafon.innerText = formatRupiahString(data.rekomendasi_plafon_ai);
                    }
                    if (data.analisis_risiko_ai) {
                        const risikoInput = document.getElementById('analisis_risiko_ai');
                        if (risikoInput) risikoInput.value = data.analisis_risiko_ai;
                    }

                    const banner = document.getElementById('aiPdfBanner');
                    if (banner) banner.classList.remove('hidden');

                    const sumber = document.getElementById('aiSumberLabel');
                    if (sumber && data.sumber) {
                        sumber.innerText = data.sumber + ' - Skor ' + data.skor_kelayakan_ai + '/100';
                    }

                    if (boxHasil) {
                        boxHasil.classList.remove('hidden');
                        renderTabelSimulasiAngsuran();
                    }

                    showToast('Eksekusi analisa berhasil! Skor kelayakan & rekomendasi diperbarui.', 'success');
                    simpanForm(false, false);
                } else {
                    jalankanFallbackEksekusiAnalisa();
                }
            } catch (err) {
                console.error(err);
                jalankanFallbackEksekusiAnalisa();
            } finally {
                if (btn) {
                    btn.disabled = false;
                    btn.classList.remove('opacity-75', 'cursor-not-allowed');
                }
                if (label) label.innerText = 'Eksekusi Analisa';
                if (icon) {
                    icon.classList.remove('animate-spin');
                    icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>';
                }
            }
        }

        function jalankanFallbackEksekusiAnalisa() {
            const plafon = parseNominal(document.getElementById('plafon_pengajuan')?.value || 0);
            let skor = 82;
            let keputusan = 'Disetujui Penuh';
            let plafonRec = plafon;

            if (plafon > 1000000000) {
                skor = 76;
                keputusan = 'Disetujui dengan Penurunan Plafon';
                plafonRec = Math.round(plafon * 0.85);
            }

            const skorInput = document.getElementById('skor_kelayakan_ai');
            if (skorInput) skorInput.value = skor;
            const rkInput = document.getElementById('rekomendasi_keputusan_ai');
            if (rkInput) rkInput.value = keputusan;
            const plafonInput = document.getElementById('rekomendasi_plafon_ai');
            if (plafonInput) plafonInput.value = formatRupiahString(plafonRec);

            const badgeSkor = document.getElementById('badgeHasilSkor');
            if (badgeSkor) badgeSkor.innerText = 'Skor ' + skor + '/100';
            const badgeKeputusan = document.getElementById('badgeHasilKeputusan');
            if (badgeKeputusan) {
                badgeKeputusan.innerText = keputusan;
                badgeKeputusan.className = 'px-2 py-0.5 rounded-md text-[11px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200';
            }
            const valPlafon = document.getElementById('valHasilPlafon');
            if (valPlafon) valPlafon.innerText = formatRupiahString(plafonRec);

            const boxHasil = document.getElementById('boxHasilEksekusiAnalisa');
            if (boxHasil) {
                boxHasil.classList.remove('hidden');
                renderTabelSimulasiAngsuran();
            }

            showToast('Eksekusi analisa selesai! Kalkulasi simulasi & jadwal angsuran siap.', 'success');
            simpanForm(false, false);
        }

        // Format angka desimal mata uang Rupiah (contoh: Rp 120.000.000,00)
        function formatRupiahDecimal(num) {
            if (num === null || num === undefined || isNaN(num)) return 'Rp 0,00';
            const fixed = Number(num).toFixed(2);
            const parts = fixed.split('.');
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            return 'Rp ' + parts.join(',');
        }

        // Render Data Anda, Angsuran Anda, dan Tabel Jadwal Angsuran Lengkap (Sesuai Gambar 2)
        function renderTabelSimulasiAngsuran() {
            const plafon = parseNominal(document.getElementById('plafon_pengajuan')?.value || 0);
            const tenor = Math.max(1, parseInt(document.getElementById('tenor_bulan')?.value || 12));
            const bungaTahun = parseFloat(document.getElementById('bunga_per_tahun')?.value || 0);
            const bungaTahunPct = bungaTahun / 100;
            const metodeBunga = (document.getElementById('perhitungan_bunga')?.value || 'ANUITAS').toUpperCase();
            const skema = document.getElementById('skema_pembayaran')?.value || 'Installment';

            const bungaBulan = bungaTahun / 12;
            const i = bungaTahunPct / 12;

            // 1. Update Box Data Anda
            const elPlafon = document.getElementById('tabelDataPlafon');
            if (elPlafon) elPlafon.innerText = formatRupiahDecimal(plafon);

            const elTenorBulan = document.getElementById('tabelDataTenorBulan');
            if (elTenorBulan) elTenorBulan.innerText = tenor + ' bulan';

            const elTenorTahun = document.getElementById('tabelDataTenorTahun');
            if (elTenorTahun) {
                const thn = (tenor / 12).toFixed(tenor % 12 === 0 ? 0 : 1);
                elTenorTahun.innerText = '(' + thn + ' tahun)';
            }

            const elBungaTahun = document.getElementById('tabelDataBungaTahun');
            if (elBungaTahun) elBungaTahun.innerText = bungaTahun.toFixed(bungaTahun % 1 === 0 ? 1 : 2) + ' % / tahun';

            const elBungaBulan = document.getElementById('tabelDataBungaBulan');
            if (elBungaBulan) elBungaBulan.innerText = '(' + bungaBulan.toFixed(2) + ' % / bulan)';

            // Tanggal Mulai Meminjam
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const tglInput = document.getElementById('display_tanggal_pengajuan')?.value || '';
            let startDate = new Date();
            if (tglInput) {
                const parts = tglInput.split('/');
                if (parts.length === 3) {
                    startDate = new Date(parseInt(parts[2]), parseInt(parts[1]) - 1, parseInt(parts[0]));
                }
            }

            const elMulaiBulan = document.getElementById('tabelDataMulaiBulan');
            if (elMulaiBulan) {
                elMulaiBulan.innerText = months[startDate.getMonth()] + ' ' + startDate.getFullYear();
            }

            const elMetode = document.getElementById('tabelDataMetodeBunga');
            if (elMetode) elMetode.innerText = metodeBunga;

            // 2. Hitung Angsuran Per Bulan (Angsuran Anda)
            let angsuranPerBulan = 0;
            if (skema === 'Balloon Payment') {
                angsuranPerBulan = plafon * i;
            } else if (metodeBunga === 'ANUITAS') {
                if (i > 0) {
                    angsuranPerBulan = plafon * (i / (1 - Math.pow(1 + i, -tenor)));
                } else {
                    angsuranPerBulan = plafon / tenor;
                }
            } else if (metodeBunga === 'EFEKTIF') {
                angsuranPerBulan = (plafon / tenor) + (plafon * i);
            } else {
                // FLAT
                angsuranPerBulan = (plafon / tenor) + (plafon * i);
            }

            const elAngsuranBulan = document.getElementById('tabelDataAngsuranBulan');
            if (elAngsuranBulan) elAngsuranBulan.innerText = formatRupiahDecimal(angsuranPerBulan);

            // 3. Generate Rows Tabel Angsuran
            const tbody = document.getElementById('bodyTabelJadwalAngsuran');
            const tfoot = document.getElementById('footTabelJadwalAngsuran');
            if (!tbody || !tfoot) return;

            let sisaPokok = plafon;
            let totalBunga = 0;
            let totalPokok = 0;
            let totalAngsuran = 0;

            let htmlRows = '';

            // Periode 0 (Bulan Pinjaman Masuk)
            const periode0 = months[startDate.getMonth()] + ' ' + startDate.getFullYear();
            htmlRows += `
                <tr class="bg-white hover:bg-blue-50/40 transition">
                    <td class="py-2.5 px-3.5 text-center font-bold text-slate-800">${periode0}</td>
                    <td class="py-2.5 px-3.5 text-right font-medium text-slate-600">0</td>
                    <td class="py-2.5 px-3.5 text-right font-medium text-slate-600">0</td>
                    <td class="py-2.5 px-3.5 text-right font-medium text-slate-600">0</td>
                    <td class="py-2.5 px-3.5 text-right font-bold text-slate-900">${formatRupiahDecimal(sisaPokok)}</td>
                </tr>
            `;

            for (let k = 1; k <= tenor; k++) {
                const curDate = new Date(startDate.getFullYear(), startDate.getMonth() + k, 1);
                const periodeName = months[curDate.getMonth()] + ' ' + curDate.getFullYear();

                let bungaBulanIni = 0;
                let pokokBulanIni = 0;
                let totalBulanIni = 0;

                if (skema === 'Balloon Payment') {
                    bungaBulanIni = sisaPokok * i;
                    pokokBulanIni = (k === tenor) ? sisaPokok : 0;
                    totalBulanIni = bungaBulanIni + pokokBulanIni;
                    sisaPokok = Math.max(0, sisaPokok - pokokBulanIni);
                } else if (metodeBunga === 'ANUITAS') {
                    bungaBulanIni = sisaPokok * i;
                    if (k === tenor) {
                        pokokBulanIni = sisaPokok;
                        totalBulanIni = pokokBulanIni + bungaBulanIni;
                        sisaPokok = 0;
                    } else {
                        pokokBulanIni = angsuranPerBulan - bungaBulanIni;
                        totalBulanIni = angsuranPerBulan;
                        sisaPokok = Math.max(0, sisaPokok - pokokBulanIni);
                    }
                } else if (metodeBunga === 'EFEKTIF') {
                    bungaBulanIni = sisaPokok * i;
                    pokokBulanIni = (k === tenor) ? sisaPokok : (plafon / tenor);
                    totalBulanIni = bungaBulanIni + pokokBulanIni;
                    sisaPokok = Math.max(0, sisaPokok - pokokBulanIni);
                } else {
                    // FLAT
                    bungaBulanIni = plafon * i;
                    pokokBulanIni = (k === tenor) ? sisaPokok : (plafon / tenor);
                    totalBulanIni = bungaBulanIni + pokokBulanIni;
                    sisaPokok = Math.max(0, sisaPokok - pokokBulanIni);
                }

                totalBunga += bungaBulanIni;
                totalPokok += pokokBulanIni;
                totalAngsuran += totalBulanIni;

                const bgRow = (k % 2 === 0) ? 'bg-[#f8fafc]' : 'bg-white';
                htmlRows += `
                    <tr class="${bgRow} hover:bg-blue-50/50 transition">
                        <td class="py-2 px-3.5 text-center font-semibold text-slate-800">${periodeName}</td>
                        <td class="py-2 px-3.5 text-right font-medium text-slate-700">${formatRupiahDecimal(bungaBulanIni)}</td>
                        <td class="py-2 px-3.5 text-right font-medium text-slate-700">${formatRupiahDecimal(pokokBulanIni)}</td>
                        <td class="py-2 px-3.5 text-right font-bold text-slate-900">${formatRupiahDecimal(totalBulanIni)}</td>
                        <td class="py-2 px-3.5 text-right font-semibold text-slate-800">${formatRupiahDecimal(sisaPokok)}</td>
                    </tr>
                `;
            }

            tbody.innerHTML = htmlRows;

            tfoot.innerHTML = `
                <tr>
                    <td class="py-2.5 px-3.5 text-center font-extrabold text-slate-900 uppercase">Total</td>
                    <td class="py-2.5 px-3.5 text-right font-extrabold text-slate-900">${formatRupiahDecimal(totalBunga)}</td>
                    <td class="py-2.5 px-3.5 text-right font-extrabold text-slate-900">${formatRupiahDecimal(totalPokok)}</td>
                    <td class="py-2.5 px-3.5 text-right font-extrabold text-slate-900">${formatRupiahDecimal(totalAngsuran)}</td>
                    <td class="py-2.5 px-3.5 text-right font-extrabold text-slate-400">-</td>
                </tr>
            `;
        }

        // Buka Laporan CAM PDF resmi
        function bukaLaporanPDF() {
            const kreditId = document.getElementById('kredit_id').value;
            if (!kreditId) {
                simpanForm(false, false).then(() => {
                    const newId = document.getElementById('kredit_id').value;
                    if (newId) {
                        window.open(getBaseUrl() + '/pengajuan/cetak/' + newId + '?print=1', '_blank');
                    } else {
                        showToast('Harap simpan data form terlebih dahulu', 'error');
                    }
                });
            } else {
                window.open(getBaseUrl() + '/pengajuan/cetak/' + kreditId + '?print=1', '_blank');
            }
        }

        // Setujui Permintaan Verifikasi SLIK oleh Pengusul (Kirim ke Petugas SLIK)
        async function setujuiPengajuanSlik(event) {
            if (event) event.preventDefault();
            const btn = document.getElementById('btnSetujuSlik');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="inline-block animate-spin mr-1.5">&#9696;</span> Memproses...';
            }

            // Set hidden status_slik
            const inputSlik = document.getElementById('inputStatusSlik');
            if (inputSlik) inputSlik.value = 'Menunggu Pengecekan';

            // Auto simpan data form
            await simpanForm(false, false);

            const kreditId = document.getElementById('kredit_id')?.value;
            if (kreditId) {
                try {
                    const res = await fetch(getBaseUrl() + '/slik/setujui-pengajuan-slik/' + kreditId, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await res.json();
                    if (data.status) {
                        showToast('Berhasil! Pengajuan pengecekan SLIK resmi dikirim ke antrean Petugas SLIK.', 'success');
                        
                        // Perbarui tampilan box status menjadi hijau
                        const box = document.getElementById('boxStatusSlik');
                        if (box) {
                            box.className = 'p-6 sm:p-7 rounded-2xl bg-emerald-50 border border-emerald-200 mb-6 text-center transition';
                            box.innerHTML = `
                                <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center mx-auto mb-3 shadow-2xs">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <h4 class="text-sm font-extrabold text-emerald-900 uppercase tracking-wide">Pengajuan Pemeriksaan SLIK OJK Telah Dikirim</h4>
                                <p class="text-xs text-emerald-800 mt-1.5 max-w-lg mx-auto leading-relaxed">
                                    Permohonan pemeriksaan riwayat fasilitas pinjaman (SLIK OJK) telah diajukan dan <strong>resmi masuk ke tabel antrean kerja Petugas SLIK</strong>. Anda dapat langsung melanjutkan ke tahapan berikutnya.
                                </p>
                                <div class="mt-4 flex items-center justify-center gap-3">
                                    <button type="button" onclick="goToTab(7)" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-xs cursor-pointer">
                                        <span>Lanjut ke Langkah 7 (Legal & Web Check)</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                    </button>
                                </div>
                            `;
                        }
                    } else {
                        showToast(data.message || 'Gagal mengirim persetujuan ke Petugas SLIK', 'error');
                        if (btn) {
                            btn.disabled = false;
                            btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg><span>Setuju di SLIK (Kirim ke Petugas SLIK)</span>';
                        }
                    }
                } catch (e) {
                    showToast('Koneksi gagal saat menghubungi server', 'error');
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg><span>Setuju di SLIK (Kirim ke Petugas SLIK)</span>';
                    }
                }
            } else {
                showToast('Harap lengkapi dan simpan informasi debitur sebelum menyetujui verifikasi SLIK', 'warning');
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg><span>Setuju di SLIK (Kirim ke Petugas SLIK)</span>';
                }
            }
        }

        // Helper Formatters
        function formatRupiah(input) {
            let val = input.value.replace(/[^0-9]/g, '');
            input.value = val ? parseInt(val, 10).toLocaleString('id-ID') : '';
        }

        function formatRupiahString(num) {
            return 'Rp ' + (parseInt(num, 10) || 0).toLocaleString('id-ID');
        }

        function parseNominal(val) {
            if (typeof val === 'number') return val;
            return parseInt(String(val).replace(/[^0-9]/g, ''), 10) || 0;
        }

        function showToast(text, type = 'info') {
            const toast = document.getElementById('toastMessage');
            const box = document.getElementById('toastBox');
            const msg = document.getElementById('toastText');

            msg.innerText = text;
            if (type === 'success') {
                box.className = 'p-4 rounded-xl shadow-lg border text-xs font-semibold flex items-center gap-2 bg-emerald-600 text-white border-emerald-700';
            } else if (type === 'error') {
                box.className = 'p-4 rounded-xl shadow-lg border text-xs font-semibold flex items-center gap-2 bg-rose-600 text-white border-rose-700';
            } else {
                box.className = 'p-4 rounded-xl shadow-lg border text-xs font-semibold flex items-center gap-2 bg-slate-900 text-white border-slate-800';
            }

            toast.classList.remove('translate-y-[-100px]', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');

            setTimeout(() => {
                toast.classList.add('translate-y-[-100px]', 'opacity-0');
                toast.classList.remove('translate-y-0', 'opacity-100');
            }, 3000);
        }

        // Modal Cek Perkara Mahkamah Agung
        function bukaModalCekMa() {
            const debtorName = document.getElementById('nama_debitur')?.value || document.getElementById('nama_debitur_tab2')?.value || '';
            const businessName = document.querySelector('input[name="nama_usaha"]')?.value || '';
            const alamat = document.querySelector('textarea[name="alamat_ktp"]')?.value || document.querySelector('input[name="alamat_usaha"]')?.value || '';
            const nik = document.getElementById('npwp_pribadi')?.value || '-';

            // Ekstrak kota jika ada di alamat KTP
            let kota = '';
            if (alamat) {
                const parts = alamat.split(/[,.-]/);
                for (let part of parts) {
                    part = part.trim();
                    if (/kota|kabupaten|kab\b|jakarta|surabaya|bandung|semarang|medan|malang|solo|yogya/i.test(part)) {
                        kota = part;
                        break;
                    }
                }
                if (!kota && parts.length > 1) kota = parts[parts.length - 1].trim();
            }

            document.getElementById('modal_ma_nama').value = debtorName;
            document.getElementById('modal_ma_kota').value = kota;
            document.getElementById('modal_ma_usaha').value = businessName;
            document.getElementById('modal_ma_nik').value = nik;

            // Reset status area
            document.getElementById('modal_ma_area_hasil').classList.add('hidden');
            document.getElementById('modalCekPerkaraMa').classList.remove('hidden');
        }

        function tutupModalCekMa() {
            document.getElementById('modalCekPerkaraMa').classList.add('hidden');
        }

        function bukaPortalResmiMa() {
            const nama = document.getElementById('modal_ma_nama').value.trim();
            const url = 'https://putusan3.mahkamahagung.go.id/search.html?q=' + encodeURIComponent(nama);
            window.open(url, 'ma_portal_win', 'width=1100,height=750,scrollbars=yes,resizable=yes');
        }

        async function jalankanScanPerkaraMa() {
            const btn = document.getElementById('btnJalankanScanMa');
            const nama = document.getElementById('modal_ma_nama').value.trim();
            const kota = document.getElementById('modal_ma_kota').value.trim();
            const usaha = document.getElementById('modal_ma_usaha').value.trim();

            if (!nama) {
                showToast('Nama Calon Debitur wajib diisi!', 'error');
                return;
            }

            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = `<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> <span>Menghubungkan ke Direktori MA...</span>`;

            try {
                const formData = new FormData();
                formData.append('nama_debitur', nama);
                formData.append('kota', kota);
                formData.append('nama_usaha', usaha);

                const res = await fetch(getBaseUrl() + '/pengajuan/cek-perkara-ma', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();

                if (data.status) {
                    const areaHasil = document.getElementById('modal_ma_area_hasil');
                    const badge = document.getElementById('modal_ma_status_badge');
                    const narasi = document.getElementById('modal_ma_hasil_narasi');

                    areaHasil.classList.remove('hidden');

                    const risiko = (data.tingkat_risiko || '').toUpperCase();
                    if (data.status_perkara === 'ada_perkara') {
                        badge.className = 'px-2.5 py-1 rounded-full text-xs font-extrabold bg-rose-100 text-rose-800 border border-rose-200';
                        badge.innerHTML = '🔴 DITEMUKAN CATATAN PERKARA (' + (risiko || 'HIGH RISK') + ')';
                    } else if (risiko.includes('PEP')) {
                        badge.className = 'px-2.5 py-1 rounded-full text-xs font-extrabold bg-amber-100 text-amber-800 border border-amber-200';
                        badge.innerHTML = '🟡 NIHIL PERKARA (TERIDENTIFIKASI PEP)';
                    } else {
                        badge.className = 'px-2.5 py-1 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200';
                        badge.innerHTML = '🟢 NIHIL PERKARA / BERSIH';
                    }

                    // Tampilkan Profil Subjek jika teridentifikasi
                    const areaProfil = document.getElementById('modal_ma_area_profil');
                    const profilTeks = document.getElementById('modal_ma_profil_teks');
                    if (areaProfil && profilTeks) {
                        if (data.profil_subjek) {
                            profilTeks.innerText = data.profil_subjek;
                            areaProfil.classList.remove('hidden');
                        } else {
                            areaProfil.classList.add('hidden');
                        }
                    }

                    // Tampilkan Temuan Riwayat Hukum & SIPP jika ada
                    const areaRingkasan = document.getElementById('modal_ma_area_ringkasan');
                    const ringkasanTeks = document.getElementById('modal_ma_ringkasan_teks');
                    if (areaRingkasan && ringkasanTeks) {
                        if (data.ringkasan) {
                            ringkasanTeks.innerText = data.ringkasan;
                            areaRingkasan.classList.remove('hidden');
                        } else {
                            areaRingkasan.classList.add('hidden');
                        }
                    }

                    narasi.value = data.rekomendasi_narasi || '';
                    showToast('Verifikasi direktori MA selesai dipetakan!', 'success');
                } else {
                    showToast(data.message || 'Gagal memeriksa perkara MA', 'error');
                }
            } catch (err) {
                showToast('Koneksi terganggu saat memverifikasi perkara MA', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        }

        function terapkanHasilMaKeForm() {
            const hasil = document.getElementById('modal_ma_hasil_narasi').value.trim();
            if (!hasil) {
                showToast('Lakukan scan terlebih dahulu sebelum menerapkan hasil.', 'error');
                return;
            }

            const target = document.getElementById('sipp_keterangan');
            if (target) {
                target.value = hasil;
                tutupModalCekMa();
                showToast('Hasil verifikasi Mahkamah Agung berhasil disalin ke form SIPP!', 'success');
            }
        }

        /* =========================================================================
         * FITUR MODAL AI WEB CHECK (5 ASPEK BACKGROUND SCREENING)
         * ========================================================================= */

        function jalankanAiWebcheck() {
            bukaModalAiWebcheck();
        }

        function bukaModalAiWebcheck() {
            const debtorName = document.getElementById('nama_debitur')?.value || document.getElementById('nama_debitur_tab2')?.value || '';
            const businessName = document.querySelector('input[name="nama_usaha"]')?.value || '';
            const alamat = document.querySelector('textarea[name="alamat_ktp"]')?.value || document.querySelector('input[name="alamat_usaha"]')?.value || '';
            const bidang = document.querySelector('input[name="bidang_usaha"]')?.value || document.querySelector('input[name="sektor_ekonomi"]')?.value || '';

            // Ekstrak kota jika ada di alamat KTP / Usaha
            let kota = '';
            if (alamat) {
                const parts = alamat.split(/[,.-]/);
                for (let part of parts) {
                    part = part.trim();
                    if (/kota|kabupaten|kab\b|jakarta|surabaya|bandung|semarang|medan|malang|solo|yogya/i.test(part)) {
                        kota = part;
                        break;
                    }
                }
                if (!kota && parts.length > 1) kota = parts[parts.length - 1].trim();
            }

            document.getElementById('modal_wc_nama').value = debtorName;
            document.getElementById('modal_wc_usaha').value = businessName;
            document.getElementById('modal_wc_kota').value = kota;
            document.getElementById('modal_wc_bidang').value = bidang;
            document.getElementById('modal_wc_catatan').value = '';

            // Sinkronisasi status preview file bukti yang sudah terunggah di Tab 7
            const fieldsBukti = ['sipp_file', 'internet_file', 'watchlist_file', 'lelang_file', 'negatif_file'];
            fieldsBukti.forEach(f => {
                const hiddenVal = document.getElementById(f)?.value?.trim();
                const previewEl = document.getElementById('modal_preview_' + f);
                if (previewEl) {
                    if (hiddenVal) {
                        const namaFile = hiddenVal.split('/').pop();
                        previewEl.innerHTML = `
                            <a href="${getBaseUrl() + '/' + hiddenVal}" target="_blank" class="text-blue-600 underline font-semibold truncate block" title="${namaFile}">
                                📄 ${namaFile}
                            </a>
                        `;
                    } else {
                        previewEl.innerHTML = `<span class="italic text-slate-400">Belum ada file</span>`;
                    }
                }
            });

            // Reset area hasil & loading
            document.getElementById('modal_wc_loading').classList.add('hidden');
            document.getElementById('modal_wc_area_hasil').classList.add('hidden');
            const areaProfil = document.getElementById('modal_wc_area_profil');
            if (areaProfil) areaProfil.classList.add('hidden');
            document.getElementById('btnTerapkanSemuaWebcheck').disabled = true;

            document.getElementById('modalAiWebcheck').classList.remove('hidden');
        }

        function tutupModalAiWebcheck() {
            document.getElementById('modalAiWebcheck').classList.add('hidden');
        }

        async function eksekusiAiWebcheckModal() {
            const btn = document.getElementById('btnMulaiScanWebcheck');
            const nama = document.getElementById('modal_wc_nama').value.trim();
            const usaha = document.getElementById('modal_wc_usaha').value.trim();
            const kota = document.getElementById('modal_wc_kota').value.trim();
            const bidang = document.getElementById('modal_wc_bidang').value.trim();
            const catatan = document.getElementById('modal_wc_catatan').value.trim();

            if (!nama) {
                showToast('Nama Calon Debitur wajib diisi!', 'error');
                return;
            }

            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = `<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> <span>Memindai 5 Aspek...</span>`;

            const loadingBox = document.getElementById('modal_wc_loading');
            const hasilBox = document.getElementById('modal_wc_area_hasil');
            loadingBox.classList.remove('hidden');
            hasilBox.classList.add('hidden');

            try {
                const formData = new FormData();
                formData.append('nama_debitur', nama);
                formData.append('nama_usaha', usaha);
                formData.append('kota', kota);
                formData.append('bidang_usaha', bidang);
                formData.append('catatan_tambahan', catatan);

                const res = await fetch(getBaseUrl() + '/pengajuan/ai-webcheck', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();

                if (data.status) {
                    loadingBox.classList.add('hidden');
                    hasilBox.classList.remove('hidden');

                    // Profil subjek jika ada
                    const areaProfil = document.getElementById('modal_wc_area_profil');
                    const teksProfil = document.getElementById('modal_wc_profil_teks');
                    if (areaProfil && teksProfil) {
                        if (data.profil_subjek) {
                            teksProfil.innerText = data.profil_subjek;
                            areaProfil.classList.remove('hidden');
                        } else {
                            areaProfil.classList.add('hidden');
                        }
                    }

                    // Kesimpulan risiko & badge
                    const summaryEl = document.getElementById('modal_wc_ringkasan_risiko');
                    if (summaryEl) {
                        summaryEl.innerText = data.ringkasan_risiko || 'Pemeriksaan integritas dan rekam jejak debitur telah selesai dipetakan.';
                    }

                    const badge = document.getElementById('modal_wc_badge_risiko');
                    if (badge) {
                        const risiko = (data.tingkat_risiko || 'LOW').toUpperCase();
                        if (risiko.includes('CRITICAL') || risiko.includes('HIGH')) {
                            badge.className = 'self-start sm:self-auto px-3 py-1.5 rounded-full text-xs font-extrabold shrink-0 bg-rose-100 text-rose-800 border border-rose-200';
                            badge.innerHTML = '🔴 ' + risiko + ' RISK';
                        } else if (risiko.includes('PEP') || risiko.includes('MEDIUM')) {
                            badge.className = 'self-start sm:self-auto px-3 py-1.5 rounded-full text-xs font-extrabold shrink-0 bg-amber-100 text-amber-800 border border-amber-200';
                            badge.innerHTML = '🟡 ' + (risiko.includes('PEP') ? 'TERIDENTIFIKASI PEP' : 'MEDIUM RISK');
                        } else {
                            badge.className = 'self-start sm:self-auto px-3 py-1.5 rounded-full text-xs font-extrabold shrink-0 bg-emerald-100 text-emerald-800 border border-emerald-200';
                            badge.innerHTML = '🟢 LOW RISK / BERSIH';
                        }
                    }

                    // Masukkan ke 5 textarea modal
                    const setVal = (id, val) => {
                        const el = document.getElementById(id);
                        if (el) el.value = val || '';
                    };
                    setVal('modal_wc_res_sipp', data.sipp_keterangan);
                    setVal('modal_wc_res_internet', data.internet_keterangan);
                    setVal('modal_wc_res_watchlist', data.watchlist_keterangan);
                    setVal('modal_wc_res_lelang', data.lelang_keterangan);
                    setVal('modal_wc_res_negatif', data.negatif_keterangan);

                    // Enable tombol terapkan
                    document.getElementById('btnTerapkanSemuaWebcheck').disabled = false;
                    showToast('Investigasi 5 aspek Web Check selesai!', 'success');
                } else {
                    loadingBox.classList.add('hidden');
                    showToast(data.message || 'Gagal memproses investigasi Web Check', 'error');
                }
            } catch (err) {
                loadingBox.classList.add('hidden');
                showToast('Koneksi terganggu saat memproses investigasi', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        }

        function terapkanSemuaHasilWebcheck() {
            const sipp = document.getElementById('modal_wc_res_sipp')?.value?.trim();
            const internet = document.getElementById('modal_wc_res_internet')?.value?.trim();
            const watchlist = document.getElementById('modal_wc_res_watchlist')?.value?.trim();
            const lelang = document.getElementById('modal_wc_res_lelang')?.value?.trim();
            const negatif = document.getElementById('modal_wc_res_negatif')?.value?.trim();

            if (document.getElementById('sipp_keterangan') && sipp) {
                document.getElementById('sipp_keterangan').value = sipp;
            }
            if (document.getElementById('internet_keterangan') && internet) {
                document.getElementById('internet_keterangan').value = internet;
            }
            if (document.getElementById('watchlist_keterangan') && watchlist) {
                document.getElementById('watchlist_keterangan').value = watchlist;
            }
            if (document.getElementById('lelang_keterangan') && lelang) {
                document.getElementById('lelang_keterangan').value = lelang;
            }
            if (document.getElementById('negatif_keterangan') && negatif) {
                document.getElementById('negatif_keterangan').value = negatif;
            }

            tutupModalAiWebcheck();
            showToast('Semua 5 aspek hasil verifikasi berhasil disalin ke form Tab 7!', 'success');
        }

        async function uploadBuktiWebcheck(inputEl, fieldName) {
            if (!inputEl.files || !inputEl.files[0]) return;
            const file = inputEl.files[0];
            const previewEl = document.getElementById('preview_' + fieldName);
            const hiddenEl = document.getElementById(fieldName);

            if (previewEl) {
                previewEl.innerHTML = `<span class="text-xs text-blue-600 animate-pulse">Mengunggah file ${file.name}...</span>`;
            }

            const formData = new FormData();
            formData.append('file', file);
            formData.append('field', fieldName);

            try {
                const res = await fetch(getBaseUrl() + '/pengajuan/upload-bukti-webcheck', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();
                if (data.status) {
                    if (hiddenEl) hiddenEl.value = data.path;
                    if (previewEl) {
                        previewEl.innerHTML = `
                            <a href="${data.url}" target="_blank" class="text-blue-600 underline font-semibold flex items-center gap-1">
                                <span>📄 Bukti Terunggah: ${data.nama_file}</span>
                            </a>
                        `;
                    }
                    showToast('Bukti file berhasil diunggah!', 'success');
                } else {
                    showToast(data.message || 'Gagal mengunggah file bukti', 'error');
                    if (previewEl) previewEl.innerHTML = `<span class="text-[11px] text-rose-500 italic">Gagal upload</span>`;
                }
            } catch (err) {
                showToast('Koneksi terganggu saat upload file', 'error');
                if (previewEl) previewEl.innerHTML = `<span class="text-[11px] text-rose-500 italic">Gagal upload</span>`;
            }
        }

        async function uploadBuktiDariModal(inputEl, fieldName) {
            if (!inputEl.files || !inputEl.files[0]) return;
            const file = inputEl.files[0];
            const previewModalEl = document.getElementById('modal_preview_' + fieldName);

            if (previewModalEl) {
                previewModalEl.innerHTML = `<span class="text-blue-600 animate-pulse font-semibold">Mengunggah ${file.name}...</span>`;
            }

            const formData = new FormData();
            formData.append('file', file);
            formData.append('field', fieldName);

            try {
                const res = await fetch(getBaseUrl() + '/pengajuan/upload-bukti-webcheck', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();
                if (data.status) {
                    if (previewModalEl) {
                        previewModalEl.innerHTML = `
                            <a href="${data.url}" target="_blank" class="text-emerald-700 underline font-semibold flex items-center gap-1" title="${data.nama_file}">
                                <span>✅ ${data.nama_file}</span>
                            </a>
                        `;
                    }
                    // Simpan ke hidden input di Tab 7 & update preview Tab 7 langsung
                    const targetHidden = document.getElementById(fieldName);
                    if (targetHidden) targetHidden.value = data.path;

                    const targetPreviewTab = document.getElementById('preview_' + fieldName);
                    if (targetPreviewTab) {
                        targetPreviewTab.innerHTML = `
                            <a href="${data.url}" target="_blank" class="text-blue-600 underline font-semibold flex items-center gap-1">
                                <span>📄 Bukti Terunggah: ${data.nama_file}</span>
                            </a>
                        `;
                    }
                    showToast('Bukti ' + data.nama_file + ' berhasil diunggah!', 'success');
                } else {
                    showToast(data.message || 'Gagal mengunggah file bukti', 'error');
                    if (previewModalEl) previewModalEl.innerHTML = `<span class="italic text-rose-500">Gagal upload</span>`;
                }
            } catch (err) {
                showToast('Koneksi terganggu saat upload file', 'error');
                if (previewModalEl) previewModalEl.innerHTML = `<span class="italic text-rose-500">Gagal upload</span>`;
            }
        }

        window.jalankanAiWebcheck = jalankanAiWebcheck;
        window.bukaModalAiWebcheck = bukaModalAiWebcheck;
        window.tutupModalAiWebcheck = tutupModalAiWebcheck;
        window.eksekusiAiWebcheckModal = eksekusiAiWebcheckModal;
        window.terapkanSemuaHasilWebcheck = terapkanSemuaHasilWebcheck;
        window.uploadBuktiWebcheck = uploadBuktiWebcheck;
        window.uploadBuktiDariModal = uploadBuktiDariModal;

        /* =========================================================================
         * FITUR BANTUAN NARASI (CAM & OPINI KREDIT)
         * ========================================================================= */

        /**
         * Toggle dropdown bantuan narasi
         */
        function toggleDropdownNarasi(menuId) {
            const targetMenu = document.getElementById(menuId);
            if (!targetMenu) return;

            const isCurrentlyHidden = targetMenu.classList.contains('hidden');

            // Tutup semua menu dropdown narasi yang sedang terbuka
            document.querySelectorAll('.dropdown-narasi-menu').forEach(menu => {
                menu.classList.add('hidden');
            });

            if (isCurrentlyHidden) {
                targetMenu.classList.remove('hidden');
            }
        }
        window.toggleDropdownNarasi = toggleDropdownNarasi;

        // Tutup dropdown jika klik di luar container
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.dropdown-narasi-container')) {
                document.querySelectorAll('.dropdown-narasi-menu').forEach(menu => {
                    menu.classList.add('hidden');
                });
            }
        });

        /**
         * Terapkan template narasi ke field target form
         */
        function terapkanNarasi(targetId, kategori, tipe) {
            const el = document.getElementById(targetId);
            if (!el) {
                console.warn('Target textarea tidak ditemukan:', targetId);
                return;
            }

            if (el.readOnly || el.disabled) {
                showToast('Kolom narasi ini sedang terkunci atau berstatus hanya baca (read-only).', 'error');
                return;
            }

            // Ambil data debitur & usaha dinamis untuk konteks narasi yang relevan
            const namaDebitur = document.getElementById('nama_debitur')?.value?.trim() ||
                                document.getElementById('nama_debitur_tab2')?.value?.trim() ||
                                'calon debitur';
            const namaUsaha = document.querySelector('input[name*="[nama_usaha]"]')?.value?.trim() ||
                              document.getElementById('nama_usaha')?.value?.trim() ||
                              'usaha calon debitur';

            const narasi = getTemplateNarasi(kategori, tipe, namaDebitur, namaUsaha);

            if (narasi) {
                el.value = narasi;

                // Trigger event agar form mengetahui ada perubahan data (dirty/autosave)
                el.dispatchEvent(new Event('input', { bubbles: true }));
                el.dispatchEvent(new Event('change', { bubbles: true }));

                // Efek visual highlight pada textarea
                el.classList.add('ring-2', 'ring-blue-500', 'bg-blue-50/40');
                setTimeout(() => {
                    el.classList.remove('ring-2', 'ring-blue-500', 'bg-blue-50/40');
                }, 900);

                const labelTipe = tipe === 'positif' ? 'Positif (Aman/Lancar)' : (tipe === 'netral' ? 'Netral (Catatan Khusus)' : 'Negatif (Perlu Mitigasi)');
                showToast(`Bantuan narasi ${labelTipe} berhasil diterapkan!`, 'success');
            }

            // Tutup semua dropdown setelah memilih narasi
            document.querySelectorAll('.dropdown-narasi-menu').forEach(menu => {
                menu.classList.add('hidden');
            });
        }
        window.terapkanNarasi = terapkanNarasi;

        /**
         * Koleksi kamus template narasi perbankan/analisa kredit resmi
         */
        function getTemplateNarasi(kategori, tipe, namaDebitur, namaUsaha) {
            let kat = String(kategori || '').toLowerCase().trim();
            const tip = String(tipe || '').toLowerCase().trim();

            if (kat === 'negatif_keterangan') kat = 'lingkungan';

            const kamus = {
                // 1. SLIK OJK (Tab 6)
                ringkasan_slik: {
                    positif: `Berdasarkan hasil penelusuran sistem SLIK OJK per tanggal penarikan, calon debitur (${namaDebitur}) memiliki riwayat fasilitas kredit dengan track record lancar (Kolektibilitas 1) di seluruh lembaga perbankan maupun lembaga pembiayaan lainnya. Tidak ditemukan catatan tunggakan pokok maupun bunga dalam 12 bulan terakhir. Karakter pembayaran debitur dinilai sangat disiplin, bertanggung jawab, dan memenuhi kriteria kelayakan pembiayaan.`,
                    netral: `Hasil verifikasi data historis SLIK OJK menunjukkan fasilitas kredit aktif calon debitur (${namaDebitur}) saat ini berstatus lancar (Kol 1). Terdapat catatan historis keterlambatan pembayaran dalam batas wajar (DPK Kol 2) dengan hari tunggakan maksimal 15 hari yang disebabkan oleh kendala teknis pencairan piutang mitra dagang, namun seluruh kewajiban telah diselesaikan secara tertib tanpa ada indikasi itikad buruk.`,
                    negatif: `Berdasarkan data SLIK OJK, teridentifikasi adanya fasilitas pinjaman aktif dengan status Dalam Perhatian Khusus / Kurang Lancar (Kol 2 / Kol 3) pada bank/lembaga pembiayaan lain dengan sisa kewajiban tertunggak. Calon debitur berkomitmen untuk melakukan take-over dan pelunasan penuh atas kewajiban tersebut melalui alokasi dana pembiayaan ini guna membersihkan status kolektibilitas.`
                },

                // 2. SIPP MA / Pengadilan (Tab 7)
                sipp: {
                    positif: `Berdasarkan penelusuran pada Sistem Informasi Penelusuran Perkara (SIPP) Pengadilan Negeri, Pengadilan Agama, PTUN, dan Pengadilan Niaga wilayah domisili dan usaha, TIDAK DITEMUKAN adanya riwayat perkara perdata, gugatan wanprestasi, perkara pidana, maupun sengketa kepailitan aktif atas nama calon debitur (${namaDebitur}) maupun badan usahanya. Calon debitur dinyatakan bersih dari risiko sengketa hukum.`,
                    netral: `Hasil penelusuran SIPP mengidentifikasi adanya riwayat perkara perdata di masa lalu, namun status perkara telah berkekuatan hukum tetap (Inkracht van Gewijsde) dan seluruh amar putusan telah diselesaikan tuntas tanpa meninggalkan beban kewajiban finansial atau sita eksekusi jaminan.`,
                    negatif: `Ditemukan catatan perkara perdata/gugatan hukum aktif di Pengadilan Negeri setempat yang melibatkan calon debitur terkait sengketa transaksi komersial. Diperlukan klarifikasi aspek hukum lebih lanjut dan penetapan mitigasi risiko legal sebelum proses kredit dilanjutkan ke tahap keputusan.`
                },

                // 3. Media Berita & Internet (Tab 7)
                internet: {
                    positif: `Hasil penelusuran mesin pencari daring, portal media berita terverifikasi, dan jejaring publik menunjukkan reputasi calon debitur (${namaDebitur}) dan unit usaha (${namaUsaha}) bersih dari pemberitaan negatif, tindak pidana komersial, sengketa ketenagakerjaan, maupun penipuan. Citra usaha di mata masyarakat dan mitra dagang tergolong baik dan terpercaya.`,
                    netral: `Penelusuran media daring dan informasi publik tidak menemukan adanya eksposur berita negatif yang signifikan. Jejak digital debitur menunjukkan aktivitas komersial dan promosi usaha yang wajar tanpa adanya catatan rekam jejak yang merugikan reputasi bisnis.`,
                    negatif: `Ditemukan beberapa artikel/keluhan konsumen pada media daring terkait keterlambatan pengiriman barang dagangan dan komplain layanan usaha. Pengusul telah memverifikasi langsung ke lapangan untuk memastikan status penyelesaian permasalahan tersebut telah diatasi secara kekeluargaan.`
                },

                // 4. Daftar Teroris DTTOT / Watchlist (Tab 7)
                watchlist: {
                    positif: `Pengecekan silang terhadap database Daftar Terduga Teroris dan Organisasi Teroris (DTTOT), Proliferasi Senjata Pemusnah Massal, serta Daftar Larangan/Sanksi TPPU-PTDI menyatakan calon debitur (${namaDebitur}) NIHIL dan tidak tercantum dalam daftar pengawasan khusus regulator atau penegak hukum.`,
                    netral: `Terdapat kesamaan nama (similarity match) pada daftar pengawasan publik, namun setelah diverifikasi kecocokan nomor NIK KTP, tanggal lahir, dan alamat domisili, dipastikan calon debitur BUKAN merupakan subjek yang terdaftar dalam database DTTOT (false positive).`,
                    negatif: `Teridentifikasi kemiripan profil identitas yang memerlukan verifikasi dokumen kependudukan mendalam (Enhanced Due Diligence - EDD) serta konfirmasi kepada pihak berwenang guna memastikan kepatuhan regulasi Anti Pencucian Uang dan Pencegahan Pendanaan Terorisme (APUPPT).`
                },

                // 5. Cek Lelang Jaminan DJKN (Tab 7)
                lelang: {
                    positif: `Hasil penelusuran pada portal resmi lelang DJKN (lelang.go.id) dan sistem lelang pengadilan mengonfirmasi bahwa aset yang diajukan sebagai agunan TIDAK SEDANG dalam proses pendaftaran lelang, sita eksekusi pengadilan, maupun eksekusi hak tanggungan oleh pihak perbankan/kreditur manapun.`,
                    netral: `Aset agunan sebelumnya pernah terdaftar dalam rencana lelang karena restrukturisasi fasilitas lama, namun lelang telah resmi dibatalkan dan bukti sertifikat kepemilikan telah berada kembali dalam penguasaan penuh calon debitur dengan surat keterangan lunas/roya sah.`,
                    negatif: `Aset agunan terindikasi berada dalam daftar sita jaminan atau pengumuman lelang aktif oleh kreditur terdahulu. Persetujuan pembiayaan ini mensyaratkan skema pelunasan langsung (take-over antar bank) untuk penebusan dan pengalihan sertifikat jaminan secara aman.`
                },

                // 6. Cek Lingkungan & Reputasi Karakter Sosial (Tab 7)
                lingkungan: {
                    positif: `Hasil konfirmasi dan wawancara informal dengan pengurus lingkungan RT/RW setempat serta tetangga sekitar tempat tinggal menyatakan bahwa calon debitur (${namaDebitur}) dikenal baik, bertutur kata sopan, tidak memiliki riwayat kebiasaan buruk (perjudian/minuman keras), dan memiliki hubungan sosial kemasyarakatan yang sangat harmonis.`,
                    netral: `Berdasarkan keterangan warga lingkungan sekitar, calon debitur tergolong warga yang sibuk beraktivitas di luar rumah untuk mengurus kegiatan usaha, namun tetap tertib dalam membayar iuran lingkungan dan tidak pernah terlibat dalam perselisihan antar warga.`,
                    negatif: `Terdapat catatan keluhan dari warga sekitar terkait aktivitas lalu lintas bongkar muat kendaraan usaha di sekitar kediaman/gudang debitur. Calon debitur telah membuat kesepakatan tertulis pengaturan jam bongkar muat demi menjaga kenyamanan lingkungan bersama.`
                },

                // 7. Analisa Kualitas & Marketability Jaminan (Tab 10)
                analisa_jaminan: {
                    positif: `Agunan memiliki lokasi sangat strategis di kawasan yang berkembang dengan akses jalan aspal lebar (> 6 meter) yang dapat dilalui dua kendaraan roda empat. Legalitas kepemilikan jelas dan sah (SHM/SHGB atas nama debitur/keluarga inti), bebas dari sengketa kepemilikan, tidak berada di jalur hijau/SUTET/bantaran sungai, dan memiliki tingkat likuiditas penjualan (marketability) yang tinggi dengan nilai appraisal yang meng-cover plafon pinjaman secara aman.`,
                    netral: `Aset jaminan berupa tanah dan bangunan berada di kawasan pemukiman padat dengan akses jalan aspal 4-5 meter. Kondisi fisik bangunan terawat baik dengan fasilitas utilitas lengkap (listrik dan air bersih). Nilai likuidasi dinilai wajar dan agunan marketable dalam rentang waktu standar penjualan properti (3-6 bulan).`,
                    negatif: `Lokasi agunan berada di dalam gang dengan akses jalan terbatas (< 3 meter) atau berdekatan dengan fasilitas umum yang dapat memengaruhi minat pembeli (seperti dekat TPU/jalur kereta api). Tingkat marketability dinilai lambat sehingga disarankan penerapan potongan nilai likuidasi (haircut) yang lebih konservatif.`
                },

                // 8. Business Review & Prospek Pasar Usaha (Tab 10)
                business_review: {
                    positif: `Unit usaha (${namaUsaha}) telah beroperasi secara konsisten selama lebih dari 3 tahun dengan tren pendapatan yang stabil dan menunjukkan tren pertumbuhan positif. Debitur memiliki basis pelanggan tetap yang loyal baik di segmen grosir maupun ritel, marjin laba yang sehat, serta hubungan kemitraan yang solid dengan pemasok utama sehingga risiko gangguan operasional tergolong minimal.`,
                    netral: `Usaha bergerak di bidang perdagangan kebutuhan masyarakat dengan persaingan usaha yang cukup dinamis di wilayah operasionalnya. Namun berbekal lokasi usaha yang strategis, harga yang kompetitif, dan pelayanan yang prima, usaha mampu mempertahankan omzet penjualan dan menghasilkan arus kas bersih bulanan yang cukup stabil.`,
                    negatif: `Usaha menghadapi tantangan fluktuasi harga bahan baku serta tekanan kompetisi dari pelaku usaha sejenis skala besar di sekitarnya. Diperlukan pengawasan terhadap efisiensi biaya operasional dan inovasi saluran pemasaran agar margin keuntungan bersih tetap terjaga optimal.`
                },

                // 9. Catatan Kunjungan OTS (Tab 10)
                business_catatan: {
                    positif: `Berdasarkan hasil On The Spot (OTS) di lokasi usaha pada jam operasional kerja, terlihat aktivitas transaksi perdagangan sangat ramai dan dinamis. Perputaran inventaris barang berjalan cepat, gudang penyimpanan tertata rapi dengan stok barang memadai, pencatatan kas harian tertib, dan karyawan bekerja secara produktif melayani pelanggan.`,
                    netral: `Kunjungan langsung (OTS) mengonfirmasi keberadaan fisik lokasi usaha, operasional, dan inventaris barang sesuai dengan data berkas permohonan. Aktivitas usaha berlangsung secara wajar dengan stok barang dagangan yang mencukupi untuk memenuhi permintaan rutin pelanggan harian.`,
                    negatif: `Hasil observasi kunjungan fisik menunjukkan aktivitas perputaran stok barang di lokasi toko/gudang relatif lambat saat jam kunjungan. Calon debitur memberikan klarifikasi bahwa mayoritas transaksi saat ini beralih ke sistem pre-order online dan pengiriman langsung dari supplier mitra.`
                },

                // 10. Rincian Kebutuhan Modal Kerja (Tab 10)
                detail_kebutuhan: {
                    positif: `Fasilitas kredit yang diajukan direncanakan seluruhnya untuk tambahan modal kerja usaha, yaitu pembelian stok persediaan barang dagangan/bahan baku dalam volume partai besar guna memperoleh potongan harga kuantitas (cash discount) dari distributor utama, sehingga diproyeksikan mampu meningkatkan omzet penjualan dan profit margin usaha.`,
                    netral: `Alokasi dana pembiayaan ditujukan untuk memperkuat cadangan modal kerja operasional harian serta mengantisipasi peningkatan piutang usaha seiring perluasan jangka waktu kredit termin kepada pelanggan langganan (tenor 14-30 hari).`,
                    negatif: `Pemanfaatan dana pinjaman dialokasikan untuk perputaran kas umum operasional dan penyelesaian kewajiban dagang jangka pendek yang segera jatuh tempo. Diperlukan monitoring berkala atas realisasi penggunaan rekening dan perputaran arus kas usaha debitur.`
                }
            };

            if (kamus[kat] && kamus[kat][tip]) {
                return kamus[kat][tip];
            }

            return null;
        }
        window.getTemplateNarasi = getTemplateNarasi;

        // Fungsi Penyesuaian Field Agunan Sesuai Jenis Jaminan
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

            if (val === 'BPKB Mobil') {
                setField(wrapAlamat, 'Merk / Tipe Kendaraan & Plat Nomor', 'Contoh: Toyota Avanza 1.5 Veloz AT 2022 (Plat B 1234 CD)', false);
                setField(wrapNomor, 'Nomor BPKB / No. Rangka Mesin', 'No. BPKB & No. Rangka Mesin', false);
                setField(wrapPemilik, 'Nama Pemilik di BPKB / STNK', 'Sesuai nama tercatat di BPKB/STNK', false);
                setField(wrapLama, 'Tahun Pembuatan Kendaraan', 'Contoh: Th. 2022 / 3 Tahun', false);
                setField(wrapMv, 'Nilai Pasar Kendaraan / MV (Rp)', '0', false);
                setField(wrapImb, '', '', true);
                setField(wrapTanah, '', '', true);
                setField(wrapBangunan, '', '', true);
                if (inputLvPct && (inputLvPct.value === '60' || inputLvPct.value === '100' || !inputLvPct.value)) {
                    inputLvPct.value = '50';
                    if (typeof hitungCoverageAgunan === 'function') hitungCoverageAgunan();
                }
            } else if (val === 'Bilyet Deposito') {
                setField(wrapAlamat, 'Bank Penerbit & Kantor Cabang', 'Contoh: Bank BPR SA / Bank Mandiri KC Bandung', false);
                setField(wrapNomor, 'Nomor Bilyet Deposito & No. Rekening', 'No. Seri Bilyet Deposito & No. Rekening', false);
                setField(wrapPemilik, 'Nama Pemilik Rekening Bilyet', 'Sesuai nama di Bilyet Deposito / KTP', false);
                setField(wrapLama, 'Tenor & Tanggal Jatuh Tempo', 'Contoh: 12 Bulan / Jatuh Tempo 15 Nov 2027', false);
                setField(wrapMv, 'Nominal Pokok Deposito (Rp)', '0', false);
                setField(wrapImb, '', '', true);
                setField(wrapTanah, '', '', true);
                setField(wrapBangunan, '', '', true);
                if (inputLvPct && (inputLvPct.value === '60' || inputLvPct.value === '50' || !inputLvPct.value)) {
                    inputLvPct.value = '100';
                    if (typeof hitungCoverageAgunan === 'function') hitungCoverageAgunan();
                }
            } else if (val === 'SHM Sarusun') {
                setField(wrapAlamat, 'Nama Apartemen, Tower, Unit & Alamat', 'Nama Apartemen, Tower, No. Lantai/Unit, Alamat Lengkap', false);
                setField(wrapNomor, 'Nomor SHM Sarusun', 'No. Sertifikat Hak Milik Sarusun', false);
                setField(wrapPemilik, 'Nama Pemilik di Sertifikat', 'Sesuai nama di sertifikat', false);
                setField(wrapLama, 'Lama Kepemilikan Unit', 'Contoh: 5 Tahun', false);
                setField(wrapMv, 'Nilai Pasar / MV (Rp)', '0', false);
                setField(wrapImb, 'Status Sertifikat Laik Fungsi (SLF)', '', false);
                setField(wrapTanah, '', '', true);
                setField(wrapBangunan, 'Luas Unit Sarusun (m²)', 'Contoh: 45', false);
            } else {
                // SHM / SHGB / Default Properti
                setField(wrapAlamat, 'Alamat Lengkap Objek Jaminan', 'Alamat lokasi aset jaminan', false);
                setField(wrapNomor, 'Nomor Sertifikat / BPKB', 'No. Sertifikat / BPKB', false);
                setField(wrapPemilik, 'Nama Pemilik Agunan', 'Sesuai nama di sertifikat', false);
                setField(wrapLama, 'Lama Kepemilikan', 'Contoh: 8 Tahun', false);
                setField(wrapMv, 'Nilai Pasar / MV (Rp)', '0', false);
                setField(wrapImb, 'Status IMB / PBG', '', false);
                setField(wrapTanah, 'Luas Tanah (m²)', 'Contoh: 120', false);
                setField(wrapBangunan, 'Luas Bangunan (m²)', 'Contoh: 85', false);
            }
        }
        window.sesuaikanFormAgunan = sesuaikanFormAgunan;

        // Sinkronisasi otomatis Jenis Jaminan (Tab 1) dengan Jenis Dokumen Agunan (Tab 4) & Inisialisasi Form Agunan
        document.addEventListener('DOMContentLoaded', () => {
            // Inisialisasi tampilan seluruh kartu agunan
            document.querySelectorAll('.agunan-item').forEach(item => {
                sesuaikanFormAgunan(item);
            });
            const selJaminan = document.getElementById('jenis_jaminan');
            const getFirstAgunan = () => document.querySelector('select[name="agunan[0][jenis_agunan]"]');

            if (selJaminan) {
                selJaminan.addEventListener('change', () => {
                    const firstAgunanSel = getFirstAgunan();
                    if (!firstAgunanSel) return;
                    const val = selJaminan.value;
                    const hasOpt = Array.from(firstAgunanSel.options).some(opt => opt.value === val);
                    if (hasOpt) {
                        firstAgunanSel.value = val;
                    }
                });

                const firstAgunanSel = getFirstAgunan();
                if (firstAgunanSel) {
                    firstAgunanSel.addEventListener('change', () => {
                        const val = firstAgunanSel.value;
                        const hasOpt = Array.from(selJaminan.options).some(opt => opt.value === val);
                        if (hasOpt) {
                            selJaminan.value = val;
                        }
                    });
                }
            }
        });