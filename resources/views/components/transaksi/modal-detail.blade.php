<div id="detailModal" class="modal">
    <div class="modal-content" style="max-width: 1000px;">
        <!-- Header -->
        <div style="padding: 1rem; border-bottom: 1px solid #e5e7eb; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); display: flex; justify-content: space-between; align-items: center; flex-shrink: 0;">
            <h3 style="color: white; font-weight: 600; margin: 0; font-size: 0.95rem;">
                <i class="fas fa-info-circle"></i> Detail Transaksi
            </h3>
            <button onclick="closeDetailModal()" class="text-white hover:text-gray-200 transition-colors p-2">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Content -->
        <div id="detailContent" style="padding: 1.5rem; max-height: calc(90vh - 150px); overflow-y: auto; background-color: #fafbfc;">
        </div>
    </div>
</div>

<script>
    function viewTransaksi(id) {
        fetch(`/transaksi/${id}`)
            .then(response => response.json())
            .then(data => {
                console.log('Data received:', data); // Debug
                
                let statusBadge = data.status === 'Deals' 
                    ? '<span style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.375rem 0.75rem; background: linear-gradient(135deg, #dcfce7 0%, #bef264 100%); color: #166534; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; box-shadow: 0 2px 4px rgba(34, 197, 94, 0.2);"><i class="fas fa-check-circle"></i> DEALS</span>'
                    : '<span style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.375rem 0.75rem; background: linear-gradient(135deg, #fee2e2 0%, #fca5a5 100%); color: #991b1b; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; box-shadow: 0 2px 4px rgba(239, 68, 68, 0.2);"><i class="fas fa-times-circle"></i> FAILS</span>';
                
                let picHtml = '';
                if (data.pic) {
                    picHtml = `
                        <div style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border: 1px solid #bae6fd; border-radius: 0.375rem; padding: 1rem; margin-bottom: 1rem;">
                            <p style="margin: 0 0 0.75rem 0; font-size: 0.75rem; color: #0369a1; font-weight: 700;"><i class="fas fa-user-tie"></i> INFORMASI PIC (PERSON IN CHARGE)</p>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                                <div style="background: white; padding: 0.75rem; border-radius: 0.3rem; border-left: 3px solid #0284c7;">
                                    <p style="margin: 0; font-size: 0.65rem; color: #0c4a6e; font-weight: 600;">Nama PIC</p>
                                    <p style="margin: 0.5rem 0 0 0; font-size: 0.9rem; color: #0369a1; font-weight: 600;">${data.pic.pic_name}</p>
                                </div>
                                <div style="background: white; padding: 0.75rem; border-radius: 0.3rem; border-left: 3px solid #0284c7;">
                                    <p style="margin: 0; font-size: 0.65rem; color: #0c4a6e; font-weight: 600;">Posisi/Jabatan</p>
                                    <p style="margin: 0.5rem 0 0 0; font-size: 0.9rem; color: #0369a1; font-weight: 600;">${data.pic.position || '-'}</p>
                                </div>
                                <div style="background: white; padding: 0.75rem; border-radius: 0.3rem; border-left: 3px solid #0284c7;">
                                    <p style="margin: 0; font-size: 0.65rem; color: #0c4a6e; font-weight: 600;">Email</p>
                                    <p style="margin: 0.5rem 0 0 0; font-size: 0.9rem; color: #0369a1; font-weight: 600; word-break: break-all;">${data.pic.email || '-'}</p>
                                </div>
                                <div style="background: white; padding: 0.75rem; border-radius: 0.3rem; border-left: 3px solid #0284c7;">
                                    <p style="margin: 0; font-size: 0.65rem; color: #0c4a6e; font-weight: 600;">Telepon</p>
                                    <p style="margin: 0.5rem 0 0 0; font-size: 0.9rem; color: #0369a1; font-weight: 600;">${data.pic.phone || '-'}</p>
                                </div>
                            </div>
                        </div>
                    `;
                } else if (data.pic_name) {
                    picHtml = `
                        <div style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border: 1px solid #fcd34d; border-radius: 0.375rem; padding: 1rem; margin-bottom: 1rem;">
                            <p style="margin: 0; font-size: 0.75rem; color: #92400e; font-weight: 700;"><i class="fas fa-user-tie"></i> PIC: ${data.pic_name}</p>
                        </div>
                    `;
                }
                
                // Format tanggal dengan fungsi helper
                function formatTanggal(tanggal) {
                    if (!tanggal) return '-';
                    const date = new Date(tanggal);
                    const day = String(date.getDate()).padStart(2, '0');
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const year = date.getFullYear();
                    return `${day}/${month}/${year}`;
                }
                
                // Hitung durasi dalam hari
                function hitungDurasi(mulai, selesai) {
                    if (!mulai || !selesai) return 0;
                    const dateMulai = new Date(mulai);
                    const dateSelesai = new Date(selesai);
                    const diffTime = Math.abs(dateSelesai - dateMulai);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                    return diffDays;
                }
                
                let html = `
                    <div style="background: white; border-radius: 0.375rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        <!-- Header Section -->
                        <div style="padding: 1.5rem; border-bottom: 2px solid #e5e7eb; background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; margin-bottom: 1.5rem;">
                                <div>
                                    <p style="font-size: 0.65rem; color: #6b7280; font-weight: 600; text-transform: uppercase; margin: 0;">Nomor Transaksi</p>
                                    <p style="font-size: 1.25rem; color: #3b82f6; font-weight: 700; margin: 0.25rem 0 0 0;">#TRX-${String(data.id).padStart(5, '0')}</p>
                                </div>
                                <div style="text-align: right;">
                                    ${statusBadge}
                                </div>
                            </div>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 0.75rem;">
                                <div>
                                    <p style="font-size: 0.65rem; color: #0c4a6e; font-weight: 600; text-transform: uppercase; margin: 0;">Tanggal Dibuat</p>
                                    <p style="color: #0369a1; font-weight: 500; margin: 0.25rem 0 0 0; font-size: 0.875rem;">${formatTanggal(data.created_at)}</p>
                                </div>
                                <div>
                                    <p style="font-size: 0.65rem; color: #0c4a6e; font-weight: 600; text-transform: uppercase; margin: 0;">Jam Dibuat</p>
                                    <p style="color: #0369a1; font-weight: 500; margin: 0.25rem 0 0 0; font-size: 0.875rem;">${new Date(data.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })} WIB</p>
                                </div>
                            </div>
                        </div>

                        <!-- Sales & Company Section -->
                        <div style="padding: 1.5rem; border-bottom: 2px solid #e5e7eb;">
                            <p style="margin: 0 0 1rem 0; font-size: 0.75rem; color: #6b7280; font-weight: 700; text-transform: uppercase;"><i class="fas fa-handshake"></i> Info Sales & Perusahaan</p>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
                                <div style="background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%); padding: 1rem; border-radius: 0.375rem; border-left: 4px solid #111827;">
                                    <p style="margin: 0; font-size: 0.65rem; color: #6b7280; font-weight: 600; text-transform: uppercase;">Nama Sales</p>
                                    <p style="margin: 0.5rem 0 0 0; font-size: 0.95rem; color: #111827; font-weight: 600;">${data.nama_sales}</p>
                                </div>
                                <div style="background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%); padding: 1rem; border-radius: 0.375rem; border-left: 4px solid #111827;">
                                    <p style="margin: 0; font-size: 0.65rem; color: #6b7280; font-weight: 600; text-transform: uppercase;">Perusahaan</p>
                                    <p style="margin: 0.5rem 0 0 0; font-size: 0.95rem; color: #111827; font-weight: 600;">${data.nama_perusahaan}</p>
                                </div>
                            </div>
                        </div>

                        <!-- PIC Section -->
                        <div style="padding: 1.5rem; border-bottom: 2px solid #e5e7eb;">
                            ${picHtml || '<p style="margin: 0; font-size: 0.75rem; color: #6b7280;"><i class="fas fa-info-circle"></i> Tidak ada PIC untuk transaksi ini</p>'}
                        </div>

                        <!-- Nilai & Tanggal Section -->
                        <div style="padding: 1.5rem; border-bottom: 2px solid #e5e7eb;">
                            <p style="margin: 0 0 1rem 0; font-size: 0.75rem; color: #6b7280; font-weight: 700; text-transform: uppercase;"><i class="fas fa-file-contract"></i> Detail Proyek</p>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1rem;">
                                <div style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); padding: 1rem; border-radius: 0.375rem; border: 1px solid #bfdbfe;">
                                    <p style="margin: 0; font-size: 0.65rem; color: #1e40af; font-weight: 600; text-transform: uppercase;">Nilai Proyek</p>
                                    <p style="margin: 0.5rem 0 0 0; font-size: 1.1rem; color: #3b82f6; font-weight: 700;">Rp${new Intl.NumberFormat('id-ID').format(data.nilai_proyek)}</p>
                                </div>
                            </div>
                            ${data.tanggal_mulai_kerja ? `
                            <div style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); padding: 1rem; border-radius: 0.375rem; border: 1px solid #fcd34d;">
                                <p style="margin: 0 0 0.75rem 0; font-size: 0.75rem; color: #92400e; font-weight: 700; text-transform: uppercase;"><i class="fas fa-calendar-alt"></i> PERIODE PENGERJAAN</p>
                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 0.75rem;">
                                    <div style="background: white; padding: 0.75rem; border-radius: 0.3rem; border-left: 3px solid #f59e0b;">
                                        <p style="margin: 0; font-size: 0.65rem; color: #92400e; font-weight: 600; text-transform: uppercase;">Mulai</p>
                                        <p style="margin: 0.25rem 0 0 0; font-size: 0.85rem; color: #78350f; font-weight: 600;">${formatTanggal(data.tanggal_mulai_kerja)}</p>
                                    </div>
                                    ${data.tanggal_selesai_kerja ? `
                                    <div style="background: white; padding: 0.75rem; border-radius: 0.3rem; border-left: 3px solid #f59e0b;">
                                        <p style="margin: 0; font-size: 0.65rem; color: #92400e; font-weight: 600; text-transform: uppercase;">Selesai</p>
                                        <p style="margin: 0.25rem 0 0 0; font-size: 0.85rem; color: #78350f; font-weight: 600;">${formatTanggal(data.tanggal_selesai_kerja)}</p>
                                    </div>
                                    <div style="background: white; padding: 0.75rem; border-radius: 0.3rem; border-left: 3px solid #f59e0b;">
                                        <p style="margin: 0; font-size: 0.65rem; color: #92400e; font-weight: 600; text-transform: uppercase;">Durasi</p>
                                        <p style="margin: 0.25rem 0 0 0; font-size: 0.85rem; color: #78350f; font-weight: 600;">${hitungDurasi(data.tanggal_mulai_kerja, data.tanggal_selesai_kerja)} Hari</p>
                                    </div>
                                    ` : ''}
                                </div>
                            </div>
                            ` : ''}
                        </div>

                        <!-- Keterangan Section -->
                        ${data.keterangan ? `
                        <div style="padding: 1.5rem; border-bottom: 2px solid #e5e7eb;">
                            <p style="margin: 0 0 0.75rem 0; font-size: 0.75rem; color: #6b7280; font-weight: 700; text-transform: uppercase;"><i class="fas fa-sticky-note"></i> Keterangan</p>
                            <div style="background-color: #f9fafb; padding: 1rem; border-radius: 0.375rem; border-left: 4px solid #3b82f6;">
                                <p style="margin: 0; font-size: 0.9rem; color: #111827; line-height: 1.6; white-space: pre-wrap;">${data.keterangan}</p>
                            </div>
                        </div>
                        ` : ''}

                        <!-- File Section -->
                        <div style="padding: 1.5rem; border-bottom: 2px solid #e5e7eb;">
                            <p style="margin: 0 0 1rem 0; font-size: 0.75rem; color: #6b7280; font-weight: 700; text-transform: uppercase;"><i class="fas fa-file-pdf"></i> Dokumen Pendukung</p>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                                ${data.bukti_spk ? `
                                <div style="background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); padding: 1rem; border-radius: 0.375rem; border: 1px solid #fca5a5; text-align: center;">
                                    <i class="fas fa-file-pdf" style="font-size: 2rem; color: #dc2626; margin-bottom: 0.5rem;"></i>
                                    <p style="margin: 0; font-size: 0.7rem; color: #7f1d1d; font-weight: 600;">Bukti SPK</p>
                                    <a href="/storage/${data.bukti_spk}" target="_blank" 
                                       style="display: inline-block; margin-top: 0.5rem; padding: 0.375rem 0.75rem; background-color: #dc2626; color: white; text-decoration: none; border-radius: 0.25rem; font-size: 0.7rem; font-weight: 600; transition: all 0.2s;"
                                       onmouseover="this.style.backgroundColor='#991b1b'"
                                       onmouseout="this.style.backgroundColor='#dc2626'">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                </div>
                                ` : ''}
                                ${data.bukti_dp ? `
                                <div style="background: linear-gradient(135deg, #dcfce7 0%, #bef264 100%); padding: 1rem; border-radius: 0.375rem; border: 1px solid #86efac; text-align: center;">
                                    <i class="fas fa-file-pdf" style="font-size: 2rem; color: #16a34a; margin-bottom: 0.5rem;"></i>
                                    <p style="margin: 0; font-size: 0.7rem; color: #166534; font-weight: 600;">Bukti DP</p>
                                    <a href="/storage/${data.bukti_dp}" target="_blank" 
                                       style="display: inline-block; margin-top: 0.5rem; padding: 0.375rem 0.75rem; background-color: #16a34a; color: white; text-decoration: none; border-radius: 0.25rem; font-size: 0.7rem; font-weight: 600; transition: all 0.2s;"
                                       onmouseover="this.style.backgroundColor='#166534'"
                                       onmouseout="this.style.backgroundColor='#16a34a'">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                </div>
                                ` : ''}
                                ${!data.bukti_spk && !data.bukti_dp ? `
                                <div style="background: #f3f4f6; padding: 1rem; border-radius: 0.375rem; border: 1px dashed #d1d5db; text-align: center; grid-column: 1 / -1;">
                                    <i class="fas fa-inbox" style="font-size: 1.5rem; color: #9ca3af; margin-bottom: 0.5rem;"></i>
                                    <p style="margin: 0; font-size: 0.75rem; color: #6b7280; font-weight: 600;">Belum ada dokumen pendukung</p>
                                </div>
                                ` : ''}
                            </div>
                        </div>

                        <!-- Summary Section -->
                        <div style="padding: 1.5rem; background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);">
                            <p style="margin: 0 0 1rem 0; font-size: 0.75rem; color: #0369a1; font-weight: 700; text-transform: uppercase;"><i class="fas fa-chart-pie"></i> Ringkasan</p>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem;">
                                <div style="background: white; padding: 0.75rem; border-radius: 0.3rem; text-align: center; border: 1px solid #bae6fd;">
                                    <p style="margin: 0; font-size: 0.65rem; color: #0c4a6e; font-weight: 600;">Status</p>
                                    <p style="margin: 0.5rem 0 0 0; font-size: 0.85rem; color: #0369a1; font-weight: 600;">${data.status}</p>
                                </div>
                                <div style="background: white; padding: 0.75rem; border-radius: 0.3rem; text-align: center; border: 1px solid #bae6fd;">
                                    <p style="margin: 0; font-size: 0.65rem; color: #0c4a6e; font-weight: 600;">Tipe Dokumen</p>
                                    <p style="margin: 0.5rem 0 0 0; font-size: 0.85rem; color: #0369a1; font-weight: 600;">${data.bukti_spk && data.bukti_dp ? 'SPK + DP' : (data.bukti_spk ? 'SPK' : (data.bukti_dp ? 'DP' : 'Belum Ada'))}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                document.getElementById('detailContent').innerHTML = html;
                document.getElementById('detailModal').classList.add('active');
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mengambil data detail');
            });
    }

    function closeDetailModal() {
        document.getElementById('detailModal').classList.remove('active');
    }

    window.addEventListener('click', function(event) {
        const modal = document.getElementById('detailModal');
        if (event.target === modal) {
            modal.classList.remove('active');
        }
    });
</script>