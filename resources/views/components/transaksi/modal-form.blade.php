<div id="transaksiModal" class="modal">
    <div class="modal-content" style="max-width: 1200px; display: flex; flex-direction: column; max-height: 90vh;">
        <!-- Header -->
        <div style="padding: 1rem; border-bottom: 1px solid #e5e7eb; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); display: flex; justify-content: space-between; align-items: center; flex-shrink: 0;">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <div style="width: 2rem; height: 2rem; background-color: rgba(255, 255, 255, 0.2); border-radius: 0.3rem; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-file-invoice" style="color: white; font-size: 1rem;"></i>
                </div>
                <h3 id="modalTitle" style="color: white; font-weight: 600; margin: 0; font-size: 0.95rem;">Tambah Transaksi Baru</h3>
            </div>
            <button onclick="closeTransaksiModal()" class="text-white hover:text-gray-200 transition-colors p-2">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Form Container dengan 2 Kolom -->
        <div style="display: flex; flex: 1; overflow: hidden;">
            <!-- KOLOM KIRI: Sales, Company, PIC (40%) -->
            <div style="flex: 0 0 40%; border-right: 1px solid #e5e7eb; overflow-y: auto; padding: 1.5rem; background-color: #ffffff;">
                <form id="transaksiForm">
                    @csrf
                    <input type="hidden" id="transaksiId" name="transaksi_id">
                    <input type="hidden" id="formMethod" name="_method" value="POST">
                    <input type="hidden" id="status" name="status" value="Fails">

                    <!-- STATUS SELECTION (Radio Button) - Paling Atas Form -->
                    <div style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border: 2px solid #bae6fd; border-radius: 0.5rem; padding: 1rem; margin-bottom: 1.5rem;">
                        <label style="display: block; font-size: 0.7rem; font-weight: 700; color: #0c4a6e; margin-bottom: 0.75rem; text-transform: uppercase;">
                            <i class="fas fa-tasks"></i> Pilih Status Transaksi
                        </label>
                        <div style="display: flex; gap: 0.5rem;">
                            <!-- Radio Fails -->
                            <label style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.625rem; background: white; border: 2px solid #fca5a5; border-radius: 0.375rem; cursor: pointer; transition: all 0.2s;"
                                onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 6px rgba(239, 68, 68, 0.15)'"
                                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'"
                                id="labelFails">
                                <input type="radio" name="status_radio" id="statusFails" value="Fails" checked 
                                    onchange="handleStatusChange()"
                                    style="width: 1rem; height: 1rem; cursor: pointer; accent-color: #ef4444;">
                                <span style="font-size: 0.75rem; font-weight: 700; color: #991b1b;">
                                    <i class="fas fa-times-circle"></i> Fails
                                </span>
                            </label>

                            <!-- Radio Deals -->
                            <label style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.625rem; background: white; border: 2px solid #d1d5db; border-radius: 0.375rem; cursor: pointer; transition: all 0.2s;"
                                onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 6px rgba(34, 197, 94, 0.15)'"
                                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'"
                                id="labelDeals">
                                <input type="radio" name="status_radio" id="statusDeals" value="Deals" 
                                    onchange="handleStatusChange()"
                                    style="width: 1rem; height: 1rem; cursor: pointer; accent-color: #22c55e;">
                                <span style="font-size: 0.75rem; font-weight: 700; color: #166534;">
                                    <i class="fas fa-check-circle"></i> Deals
                                </span>
                            </label>
                        </div>
                        <small style="display: block; margin-top: 0.5rem; font-size: 0.65rem; color: #0369a1; text-align: center;">
                            <i class="fas fa-info-circle"></i> Pilih "Deals" untuk mengisi data proyek lengkap
                        </small>
                    </div>

                    <!-- 1. Sales Visit Selection -->
                    <div style="background-color: #eff6ff; border: 1px solid #bfdbfe; border-radius: 0.375rem; padding: 0.75rem; margin-bottom: 1.25rem;">
                        <label style="display: block; font-size: 0.7rem; font-weight: 500; color: #1e40af; margin-bottom: 0.375rem;">
                            <i class="fas fa-link"></i> Pilih Sales Visit (Opsional)
                        </label>
                        <select name="sales_visit_id" id="sales_visit_id"
                            style="width: 100%; padding: 0.5rem; border: 1px solid #bfdbfe; border-radius: 0.3rem; font-size: 0.75rem;"
                            onchange="handleSalesVisitChange()"
                            onfocus="this.style.borderColor='#0284c7'; this.style.boxShadow='0 0 0 3px rgba(2, 132, 199, 0.1)'"
                            onblur="this.style.borderColor='#bfdbfe'; this.style.boxShadow='none'">
                            <option value="">-- Pilih Sales Visit --</option>
                            @foreach ($salesVisits as $visit)
                                @php
                                    $salesName = $visit->sales?->username ?? $visit->user?->username ?? 'N/A';
                                    $companyName = $visit->company?->company_name ?? $visit->company_name ?? 'N/A';
                                    $salesId = $visit->sales_id ?? $visit->user_id;
                                    $picName = $visit->pic?->pic_name ?? 'Tidak ada PIC';
                                    $picId = $visit->pic_id ?? null;
                                    $picPosition = $visit->pic?->position ?? '';
                                    $picEmail = $visit->pic?->email ?? '';
                                    $picPhone = $visit->pic?->phone ?? '';
                                @endphp
                                <option value="{{ $visit->id }}" 
                                    data-company-id="{{ $visit->company_id }}"
                                    data-company-name="{{ $companyName }}"
                                    data-sales-id="{{ $salesId }}"
                                    data-sales-name="{{ $salesName }}"
                                    data-pic-id="{{ $picId }}"
                                    data-pic-name="{{ $picName }}"
                                    data-pic-position="{{ $picPosition }}"
                                    data-pic-email="{{ $picEmail }}"
                                    data-pic-phone="{{ $picPhone }}"
                                    data-visit-date="{{ $visit->visit_date ?? '' }}">
                                    {{ $companyName }} - {{ $salesName }} ({{ $picName }}) - {{ $visit->visit_date?->format('d M Y') ?? $visit->created_at->format('d M Y') }}
                                </option>
                            @endforeach
                        </select>
                        <small style="color: #1e40af; font-size: 0.65rem; margin-top: 0.25rem; display: block;">
                            <i class="fas fa-info-circle"></i> Auto-fill semua data termasuk PIC
                        </small>
                    </div>

                    <!-- 2. Sales Selection -->
                    <div style="margin-bottom: 1.25rem;">
                        <label style="display: block; font-size: 0.7rem; font-weight: 600; color: #111827; margin-bottom: 0.375rem;">
                            👤 Nama Sales <span style="color: #dc2626;">*</span>
                        </label>
                        <select name="sales_id" id="sales_id"
                            style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.3rem; font-size: 0.75rem;"
                            required onchange="updateSalesName()"
                            onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1)'"
                            onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                            <option value="">-- Pilih Sales --</option>
                            @foreach ($sales as $s)
                                <option value="{{ $s->user_id }}" data-name="{{ $s->username }}">
                                    {{ $s->username }} ({{ $s->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 3. Nama Sales Lengkap -->
                    <div style="margin-bottom: 1.25rem;">
                        <label style="display: block; font-size: 0.7rem; font-weight: 600; color: #111827; margin-bottom: 0.375rem;">
                            Nama Sales Lengkap <span style="color: #dc2626;">*</span>
                        </label>
                        <input type="text" name="nama_sales" id="nama_sales"
                            style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.3rem; font-size: 0.75rem; background-color: #f9fafb; color: #111827;"
                            required readonly>
                    </div>

                    <!-- 4. Company Selection -->
                    <div style="margin-bottom: 1.25rem;">
                        <label style="display: block; font-size: 0.7rem; font-weight: 600; color: #111827; margin-bottom: 0.375rem;">
                            🏢 Perusahaan <span style="color: #dc2626;">*</span>
                        </label>
                        <select name="company_id" id="company_id"
                            style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.3rem; font-size: 0.75rem;"
                            required onchange="updateCompanyInfo(); loadPicsForCompany()"
                            onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1)'"
                            onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                            <option value="">-- Pilih Perusahaan --</option>
                            @foreach ($companies as $company)
                                <option value="{{ $company->company_id }}" 
                                    data-name="{{ $company->company_name }}"
                                    data-type="{{ $company->companyType?->type_name ?? 'N/A' }}"
                                    data-phone="{{ $company->phone ?? '' }}"
                                    data-email="{{ $company->email ?? '' }}"
                                    data-address="{{ $company->full_address ?? $company->address ?? '' }}">
                                    {{ $company->company_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 5. Nama Perusahaan -->
                    <div style="margin-bottom: 1.25rem;">
                        <label style="display: block; font-size: 0.7rem; font-weight: 600; color: #111827; margin-bottom: 0.375rem;">
                            Nama Perusahaan <span style="color: #dc2626;">*</span>
                        </label>
                        <input type="text" name="nama_perusahaan" id="nama_perusahaan"
                            style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.3rem; font-size: 0.75rem; background-color: #f9fafb; color: #111827;"
                            required readonly>
                    </div>

                    <!-- 6. Company Info Box -->
                    <div id="companyInfoBox" style="display: none; background-color: #f3f4f6; border: 1px solid #d1d5db; border-radius: 0.375rem; padding: 0.75rem; margin-bottom: 1.25rem;">
                        <p style="margin: 0 0 0.5rem 0; font-size: 0.65rem; color: #6b7280; font-weight: 600;">ℹ️ INFO PERUSAHAAN</p>
                        <div style="display: grid; grid-template-columns: 1fr; gap: 0.375rem;">
                            <div>
                                <p style="margin: 0; font-size: 0.65rem; color: #6b7280;">Tipe:</p>
                                <p id="companyType" style="margin: 0; font-size: 0.75rem; color: #111827; font-weight: 500;">-</p>
                            </div>
                            <div>
                                <p style="margin: 0; font-size: 0.65rem; color: #6b7280;">Telepon:</p>
                                <p id="companyPhone" style="margin: 0; font-size: 0.75rem; color: #111827; font-weight: 500;">-</p>
                            </div>
                            <div>
                                <p style="margin: 0; font-size: 0.65rem; color: #6b7280;">Email:</p>
                                <p id="companyEmail" style="margin: 0; font-size: 0.75rem; color: #111827; font-weight: 500;">-</p>
                            </div>
                            <div>
                                <p style="margin: 0; font-size: 0.65rem; color: #6b7280;">Alamat:</p>
                                <p id="companyAddress" style="margin: 0; font-size: 0.75rem; color: #111827; font-weight: 500; max-height: 50px; overflow: hidden;">-</p>
                            </div>
                        </div>
                    </div>

                    <!-- 7. PIC Selection -->
                    <div style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border: 1px solid #bae6fd; border-radius: 0.375rem; padding: 1rem; margin-bottom: 1.25rem;">
                        <label style="display: block; font-size: 0.7rem; font-weight: 600; color: #0c4a6e; margin-bottom: 0.5rem;">
                            👤 Pilih PIC (Person In Charge)
                        </label>
                        
                        <div style="margin-bottom: 0.75rem;">
                            <select name="pic_id" id="pic_id"
                                style="width: 100%; padding: 0.5rem; border: 1px solid #bae6fd; border-radius: 0.3rem; font-size: 0.75rem; background-color: #f0f9ff; color: #0c4a6e;"
                                onchange="handlePicChange()">
                                <option value="">-- Pilih PIC dari Company --</option>
                            </select>
                        </div>

                        <!-- Nama PIC -->
                        <div>
                            <label style="display: block; font-size: 0.65rem; font-weight: 600; color: #0c4a6e; margin-bottom: 0.25rem;">Nama PIC</label>
                            <input type="text" name="pic_name" id="pic_name"
                                style="width: 100%; padding: 0.5rem; border: 1px solid #bae6fd; border-radius: 0.3rem; font-size: 0.75rem; background-color: #f0f9ff; color: #0c4a6e;"
                                placeholder="Auto-fill dari PIC yang dipilih">
                        </div>
                    </div>

                    <!-- 8. PIC Info Box -->
                    <div id="picInfoBox" style="display: none; background: white; border: 2px solid #bae6fd; border-radius: 0.375rem; padding: 0.875rem; box-shadow: 0 4px 6px rgba(2, 132, 199, 0.1); margin-bottom: 1.25rem;">
                        <p style="margin: 0 0 0.75rem 0; font-size: 0.7rem; color: #0369a1; font-weight: 700;"><i class="fas fa-id-card"></i> INFORMASI PIC</p>
                        <div style="display: grid; grid-template-columns: 1fr; gap: 0.6rem;">
                            <div style="padding: 0.5rem; background-color: #f0f9ff; border-left: 3px solid #0284c7; border-radius: 0.25rem;">
                                <p style="margin: 0; font-size: 0.6rem; color: #0c4a6e; font-weight: 700; text-transform: uppercase;">Posisi/Jabatan</p>
                                <p id="picPosition" style="margin: 0.25rem 0 0 0; font-size: 0.8rem; color: #0369a1; font-weight: 600;">-</p>
                            </div>
                            <div style="padding: 0.5rem; background-color: #f0f9ff; border-left: 3px solid #0284c7; border-radius: 0.25rem;">
                                <p style="margin: 0; font-size: 0.6rem; color: #0c4a6e; font-weight: 700; text-transform: uppercase;">Email</p>
                                <p id="picEmail" style="margin: 0.25rem 0 0 0; font-size: 0.75rem; color: #0369a1; font-weight: 600; word-break: break-all;">-</p>
                            </div>
                            <div style="padding: 0.5rem; background-color: #f0f9ff; border-left: 3px solid #0284c7; border-radius: 0.25rem;">
                                <p style="margin: 0; font-size: 0.6rem; color: #0c4a6e; font-weight: 700; text-transform: uppercase;">Telepon</p>
                                <p id="picPhone" style="margin: 0.25rem 0 0 0; font-size: 0.8rem; color: #0369a1; font-weight: 600;">-</p>
                            </div>
                        </div>
                    </div>

                    <!-- No PIC Info -->
                    <div id="noPicBox" style="display: block; background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); border: 2px dashed #fcd34d; border-radius: 0.375rem; padding: 0.75rem; text-align: center; margin-bottom: 1.25rem;">
                        <i class="fas fa-inbox" style="color: #f59e0b; font-size: 1.25rem;"></i>
                        <p style="margin: 0.5rem 0 0 0; font-size: 0.7rem; color: #92400e; font-weight: 600;">Pilih Company untuk load PIC</p>
                    </div>
                </form>
            </div>

            <!-- KOLOM KANAN: Nilai, File, Tanggal, Keterangan (60%) -->
            <div id="rightColumn" style="flex: 0 0 60%; overflow-y: auto; padding: 1.5rem; background-color: #f3f4f6; opacity: 0.6; pointer-events: none; transition: all 0.3s;">
                <!-- Warning Badge (hanya muncul saat disabled) -->
                <div id="disabledWarning" style="background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); border: 2px solid #fca5a5; border-radius: 0.5rem; padding: 1rem; margin-bottom: 1.5rem; text-align: center; box-shadow: 0 4px 6px rgba(239, 68, 68, 0.2);">
                    <i class="fas fa-lock" style="font-size: 2rem; color: #dc2626; margin-bottom: 0.5rem;"></i>
                    <p style="margin: 0; font-size: 0.85rem; font-weight: 700; color: #991b1b;">Bagian ini dinonaktifkan untuk transaksi FAILS</p>
                    <p style="margin: 0.25rem 0 0 0; font-size: 0.7rem; color: #7f1d1d;">Pilih status "Deals" untuk mengisi data proyek</p>
                </div>

                <form id="transaksiFormRight">
                    <!-- Nilai Proyek -->
                    <div style="margin-bottom: 1.25rem;">
                        <label style="display: block; font-size: 0.7rem; font-weight: 600; color: #111827; margin-bottom: 0.375rem;">
                            💰 Nilai Proyek (Rp) <span style="color: #dc2626;">*</span>
                        </label>
                        <input type="number" name="nilai_proyek" id="nilai_proyek"
                            style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.3rem; font-size: 0.75rem; background-color: #e5e7eb; cursor: not-allowed;"
                            placeholder="0" min="0" step="0.01" disabled
                            onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1)'"
                            onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                    </div>

                    <!-- Bukti SPK -->
                    <div style="margin-bottom: 1.25rem;">
                        <label style="display: block; font-size: 0.7rem; font-weight: 600; color: #111827; margin-bottom: 0.375rem;">
                            📄 Bukti SPK
                        </label>
                        <div style="border: 2px dashed #d1d5db; border-radius: 0.375rem; padding: 0.75rem; text-align: center; cursor: not-allowed; transition: all 0.2s; background-color: #e5e7eb;"
                            id="drop_spk">
                            <i class="fas fa-cloud-upload-alt" style="font-size: 1.125rem; color: #9ca3af; display: block;"></i>
                            <p style="color: #6b7280; margin: 0.25rem 0 0 0; font-size: 0.7rem; font-weight: 500;">Klik atau drag file</p>
                        </div>
                        <input type="file" name="bukti_spk" id="bukti_spk" style="display: none;"
                            accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" disabled
                            onchange="updateFileName('bukti_spk')">
                        <p id="bukti_spk_name" style="color: #22c55e; font-size: 0.65rem; margin-top: 0.375rem;"></p>
                    </div>

                    <!-- Bukti DP -->
                    <div style="margin-bottom: 1.25rem;">
                        <label style="display: block; font-size: 0.7rem; font-weight: 600; color: #111827; margin-bottom: 0.375rem;">
                            📄 Bukti DP
                        </label>
                        <div style="border: 2px dashed #d1d5db; border-radius: 0.375rem; padding: 0.75rem; text-align: center; cursor: not-allowed; transition: all 0.2s; background-color: #e5e7eb;"
                            id="drop_dp">
                            <i class="fas fa-cloud-upload-alt" style="font-size: 1.125rem; color: #9ca3af; display: block;"></i>
                            <p style="color: #6b7280; margin: 0.25rem 0 0 0; font-size: 0.7rem; font-weight: 500;">Klik atau drag file</p>
                        </div>
                        <input type="file" name="bukti_dp" id="bukti_dp" style="display: none;"
                            accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" disabled
                            onchange="updateFileName('bukti_dp')">
                        <p id="bukti_dp_name" style="color: #22c55e; font-size: 0.65rem; margin-top: 0.375rem;"></p>
                    </div>

                    <!-- Tanggal Mulai -->
                    <div style="margin-bottom: 1.25rem;">
                        <label style="display: block; font-size: 0.7rem; font-weight: 600; color: #111827; margin-bottom: 0.375rem;">
                            📅 Tanggal Mulai Kerja
                        </label>
                        <input type="date" name="tanggal_mulai_kerja" id="tanggal_mulai_kerja"
                            style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.3rem; font-size: 0.75rem; background-color: #e5e7eb; cursor: not-allowed;"
                            disabled
                            onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1)'"
                            onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                    </div>

                    <!-- Tanggal Selesai -->
                    <div style="margin-bottom: 1.25rem;">
                        <label style="display: block; font-size: 0.7rem; font-weight: 600; color: #111827; margin-bottom: 0.375rem;">
                            📅 Tanggal Selesai Kerja
                        </label>
                        <input type="date" name="tanggal_selesai_kerja" id="tanggal_selesai_kerja"
                            style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.3rem; font-size: 0.75rem; background-color: #e5e7eb; cursor: not-allowed;"
                            disabled
                            onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1)'"
                            onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                    </div>

                    <!-- Keterangan -->
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; font-size: 0.7rem; font-weight: 600; color: #111827; margin-bottom: 0.375rem;">
                            📝 Keterangan
                        </label>
                        <textarea name="keterangan" id="keterangan" rows="3"
                            style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.3rem; font-size: 0.75rem; font-family: inherit; resize: vertical; background-color: #e5e7eb; cursor: not-allowed;"
                            placeholder="Catatan tambahan..." disabled
                            onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1)'"
                            onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'"></textarea>
                    </div>
                </form>
            </div>
        </div>

        <!-- Footer dengan tombol KONFIRMASI -->
        <div id="footerButtons" style="padding: 1rem; border-top: 2px solid #e5e7eb; background-color: #f9fafb; display: flex; gap: 0.75rem; flex-shrink: 0; box-shadow: 0 -4px 6px rgba(0, 0, 0, 0.05);">
            <button type="button" onclick="closeTransaksiModal()"
                style="flex: 1; padding: 0.625rem 1rem; background-color: #6b7280; color: white; border: none; border-radius: 0.375rem; font-weight: 600; font-size: 0.8rem; cursor: pointer; transition: all 0.2s; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);"
                onmouseover="this.style.backgroundColor='#4b5563'; this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 8px rgba(0, 0, 0, 0.15)'"
                onmouseout="this.style.backgroundColor='#6b7280'; this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(0, 0, 0, 0.1)'">
                <i class="fas fa-times"></i> Batal
            </button>
            <button type="button" id="confirmButton" onclick="handleConfirmClick()"
                style="flex: 2; padding: 0.625rem 1rem; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; border: none; border-radius: 0.375rem; font-weight: 600; font-size: 0.8rem; cursor: pointer; transition: all 0.2s; box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);"
                onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 12px rgba(59, 130, 246, 0.4)'"
                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(59, 130, 246, 0.3)'">
                <i class="fas fa-save"></i> Konfirmasi & Simpan
            </button>
        </div>
    </div>
</div>

<!-- MODAL KONFIRMASI FAILS -->
<div id="confirmFailsModal" class="modal">
    <div class="modal-content" style="max-width: 500px;">
        <div style="padding: 1.5rem; text-align: center;">
            <div style="width: 4rem; height: 4rem; background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                <i class="fas fa-exclamation-triangle" style="font-size: 2rem; color: #dc2626;"></i>
            </div>
            <h3 style="font-size: 1.25rem; font-weight: 700; color: #111827; margin: 0 0 0.5rem 0;">Konfirmasi Transaksi Gagal</h3>
            <p style="font-size: 0.875rem; color: #6b7280; margin: 0 0 1.5rem 0;">Apakah Anda yakin transaksi ini <strong style="color: #dc2626;">GAGAL (Fails)</strong>?</p>
            
            <div style="background-color: #fef3c7; border: 1px solid #fcd34d; border-radius: 0.375rem; padding: 0.875rem; margin-bottom: 1.5rem;">
                <p style="margin: 0; font-size: 0.75rem; color: #92400e; font-weight: 600;">
                    <i class="fas fa-info-circle"></i> Data yang akan disimpan:
                </p>
                <ul style="margin: 0.5rem 0 0 1.25rem; padding: 0; font-size: 0.7rem; color: #78350f;">
                    <li>Informasi Sales & Perusahaan</li>
                    <li>Data PIC (jika ada)</li>
                    <li>Status: <strong>Fails</strong></li>
                </ul>
            </div>
            
            <div style="display: flex; gap: 0.75rem;">
                <button type="button" onclick="closeConfirmFailsModal()"
                    style="flex: 1; padding: 0.625rem 1rem; background-color: #e5e7eb; color: #374151; border: none; border-radius: 0.375rem; font-weight: 600; font-size: 0.8rem; cursor: pointer; transition: all 0.2s;"
                    onmouseover="this.style.backgroundColor='#d1d5db'"
                    onmouseout="this.style.backgroundColor='#e5e7eb'">
                    <i class="fas fa-arrow-left"></i> Kembali
                </button>
                <button type="button" onclick="confirmFailsAndSubmit()"
                    style="flex: 1; padding: 0.625rem 1rem; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; border: none; border-radius: 0.375rem; font-weight: 600; font-size: 0.8rem; cursor: pointer; transition: all 0.2s; box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3);"
                    onmouseover="this.style.boxShadow='0 6px 12px rgba(239, 68, 68, 0.4)'"
                    onmouseout="this.style.boxShadow='0 2px 4px rgba(239, 68, 68, 0.3)'">
                    <i class="fas fa-check"></i> Ya, Simpan Fails
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL KONFIRMASI DEALS -->
<div id="confirmDealsModal" class="modal">
    <div class="modal-content" style="max-width: 500px;">
        <div style="padding: 1.5rem; text-align: center;">
            <div style="width: 4rem; height: 4rem; background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                <i class="fas fa-check-circle" style="font-size: 2rem; color: #16a34a;"></i>
            </div>
            <h3 style="font-size: 1.25rem; font-weight: 700; color: #111827; margin: 0 0 0.5rem 0;">Konfirmasi Transaksi Berhasil</h3>
            <p style="font-size: 0.875rem; color: #6b7280; margin: 0 0 1.5rem 0;">Apakah semua data sudah benar dan siap disimpan?</p>
            
            <div style="background-color: #dcfce7; border: 1px solid #86efac; border-radius: 0.375rem; padding: 0.875rem; margin-bottom: 1.5rem; text-align: left;">
                <p style="margin: 0 0 0.5rem 0; font-size: 0.75rem; color: #166534; font-weight: 600;">
                    <i class="fas fa-clipboard-check"></i> Data yang akan disimpan:
                </p>
                <div style="font-size: 0.7rem; color: #15803d; display: grid; gap: 0.25rem;">
                    <div>✓ Informasi Sales & Perusahaan</div>
                    <div>✓ Data PIC</div>
                    <div>✓ Nilai Proyek & Tanggal</div>
                    <div>✓ Dokumen Pendukung</div>
                    <div>✓ Status: <strong>Deals</strong></div>
                </div>
            </div>
            
            <div style="display: flex; gap: 0.75rem;">
                <button type="button" onclick="closeConfirmDealsModal()"
                    style="flex: 1; padding: 0.625rem 1rem; background-color: #e5e7eb; color: #374151; border: none; border-radius: 0.375rem; font-weight: 600; font-size: 0.8rem; cursor: pointer; transition: all 0.2s;"
                    onmouseover="this.style.backgroundColor='#d1d5db'"
                    onmouseout="this.style.backgroundColor='#e5e7eb'">
                    <i class="fas fa-arrow-left"></i> Kembali
                </button>
                <button type="button" onclick="confirmDealsAndSubmit()"
                    style="flex: 1; padding: 0.625rem 1rem; background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); color: white; border: none; border-radius: 0.375rem; font-weight: 600; font-size: 0.8rem; cursor: pointer; transition: all 0.2s; box-shadow: 0 2px 4px rgba(34, 197, 94, 0.3);"
                    onmouseover="this.style.boxShadow='0 6px 12px rgba(34, 197, 94, 0.4)'"
                    onmouseout="this.style.boxShadow='0 2px 4px rgba(34, 197, 94, 0.3)'">
                    <i class="fas fa-check"></i> Ya, Simpan Deals
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// ==========================================
    // HANDLE STATUS CHANGE (Radio Button)
    // ==========================================
    function handleStatusChange() {
        const statusFails = document.getElementById('statusFails');
        const statusDeals = document.getElementById('statusDeals');
        const rightColumn = document.getElementById('rightColumn');
        const disabledWarning = document.getElementById('disabledWarning');
        const formRight = document.getElementById('transaksiFormRight');
        const labelFails = document.getElementById('labelFails');
        const labelDeals = document.getElementById('labelDeals');
        const statusInput = document.getElementById('status');
        
        if (statusFails.checked) {
            // Mode FAILS - Disable kolom kanan
            statusInput.value = 'Fails';
            rightColumn.style.backgroundColor = '#f3f4f6';
            rightColumn.style.opacity = '0.6';
            rightColumn.style.pointerEvents = 'none';
            disabledWarning.style.display = 'block';
            
            // Style radio labels
            labelFails.style.borderColor = '#fca5a5';
            labelFails.style.background = 'linear-gradient(135deg, #fee2e2 0%, #fecaca 100%)';
            labelDeals.style.borderColor = '#d1d5db';
            labelDeals.style.background = 'white';
            
            // Disable semua input di kolom kanan
            const inputs = formRight.querySelectorAll('input, textarea, select');
            inputs.forEach(input => {
                input.disabled = true;
                input.style.backgroundColor = '#e5e7eb';
                input.style.cursor = 'not-allowed';
            });
            
            // Update drop zones
            const dropSpk = document.getElementById('drop_spk');
            const dropDp = document.getElementById('drop_dp');
            if (dropSpk) {
                dropSpk.style.cursor = 'not-allowed';
                dropSpk.style.backgroundColor = '#e5e7eb';
                dropSpk.onclick = null;
            }
            if (dropDp) {
                dropDp.style.cursor = 'not-allowed';
                dropDp.style.backgroundColor = '#e5e7eb';
                dropDp.onclick = null;
            }
            
            // Kosongkan nilai (tidak required untuk Fails)
            document.getElementById('nilai_proyek').value = '';
            document.getElementById('nilai_proyek').removeAttribute('required');
            
        } else if (statusDeals.checked) {
            // Mode DEALS - Enable kolom kanan
            statusInput.value = 'Deals';
            rightColumn.style.backgroundColor = '#ffffff';
            rightColumn.style.opacity = '1';
            rightColumn.style.pointerEvents = 'auto';
            disabledWarning.style.display = 'none';
            
            // Style radio labels
            labelDeals.style.borderColor = '#86efac';
            labelDeals.style.background = 'linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%)';
            labelFails.style.borderColor = '#d1d5db';
            labelFails.style.background = 'white';
            
            // Enable semua input di kolom kanan
            const inputs = formRight.querySelectorAll('input, textarea, select');
            inputs.forEach(input => {
                input.disabled = false;
                input.style.backgroundColor = '';
                input.style.cursor = '';
            });
            
            // Update drop zones
            const dropSpk = document.getElementById('drop_spk');
            const dropDp = document.getElementById('drop_dp');
            if (dropSpk) {
                dropSpk.style.cursor = 'pointer';
                dropSpk.style.backgroundColor = '';
                dropSpk.onclick = () => document.getElementById('bukti_spk').click();
            }
            if (dropDp) {
                dropDp.style.cursor = 'pointer';
                dropDp.style.backgroundColor = '';
                dropDp.onclick = () => document.getElementById('bukti_dp').click();
            }
            
            // Set required untuk nilai proyek
            document.getElementById('nilai_proyek').setAttribute('required', '');
        }
    }

    // ==========================================
    // HANDLE CONFIRM CLICK
    // ==========================================
    function handleConfirmClick() {
        // Validasi data kiri (wajib untuk semua transaksi)
        if (!document.getElementById('sales_id').value) {
            alert('❌ Silakan pilih Sales terlebih dahulu!');
            return;
        }
        if (!document.getElementById('company_id').value) {
            alert('❌ Silakan pilih Perusahaan terlebih dahulu!');
            return;
        }
        
        const statusFails = document.getElementById('statusFails');
        
        if (statusFails.checked) {
            // Tampilkan modal konfirmasi FAILS
            document.getElementById('confirmFailsModal').classList.add('active');
        } else {
            // Validasi tambahan untuk Deals
            if (!document.getElementById('nilai_proyek').value) {
                alert('❌ Silakan masukkan Nilai Proyek!');
                return;
            }
            // Tampilkan modal konfirmasi DEALS
            document.getElementById('confirmDealsModal').classList.add('active');
        }
    }

    // ==========================================
    // KONFIRMASI FAILS & SUBMIT
    // ==========================================
    function confirmFailsAndSubmit() {
        closeConfirmFailsModal();
        submitForm();
    }

    // ==========================================
    // KONFIRMASI DEALS & SUBMIT
    // ==========================================
    function confirmDealsAndSubmit() {
        closeConfirmDealsModal();
        submitForm();
    }

    // ==========================================
    // CLOSE MODAL KONFIRMASI
    // ==========================================
    function closeConfirmFailsModal() {
        document.getElementById('confirmFailsModal').classList.remove('active');
    }

    function closeConfirmDealsModal() {
        document.getElementById('confirmDealsModal').classList.remove('active');
    }

    // ==========================================
    // SUBMIT FORM
    // ==========================================
    function submitForm() {
        const transaksiId = document.getElementById('transaksiId').value;
        const form = document.getElementById('transaksiForm');
        const formData = new FormData(form);
        
        const statusFails = document.getElementById('statusFails');
        
        // Append data dari form kiri
        formData.append('pic_id', document.getElementById('pic_id').value);
        formData.append('pic_name', document.getElementById('pic_name').value);
        formData.append('status', statusFails.checked ? 'Fails' : 'Deals');
        
        // Hanya append data kanan jika DEALS
        if (!statusFails.checked) {
            formData.append('nilai_proyek', document.getElementById('nilai_proyek').value);
            formData.append('tanggal_mulai_kerja', document.getElementById('tanggal_mulai_kerja').value);
            formData.append('tanggal_selesai_kerja', document.getElementById('tanggal_selesai_kerja').value);
            formData.append('keterangan', document.getElementById('keterangan').value);
            
            if (document.getElementById('bukti_spk').files.length > 0) {
                formData.append('bukti_spk', document.getElementById('bukti_spk').files[0]);
            }
            if (document.getElementById('bukti_dp').files.length > 0) {
                formData.append('bukti_dp', document.getElementById('bukti_dp').files[0]);
            }
        } else {
            // Untuk Fails, set nilai proyek ke 0
            formData.append('nilai_proyek', 0);
        }

        const url = transaksiId ? `/transaksi/${transaksiId}` : '/transaksi';
        if (transaksiId) formData.append('_method', 'PUT');

        const confirmButton = document.getElementById('confirmButton');
        const originalText = confirmButton.innerHTML;
        
        confirmButton.disabled = true;
        confirmButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (response.ok) {
                window.location.href = '/transaksi';
            } else {
                return response.json().then(data => {
                    let errors = '';
                    if (data.errors) {
                        errors = Object.values(data.errors).flat().join('\n');
                    }
                    alert('❌ Terjadi kesalahan:\n' + (errors || 'Coba lagi'));
                    
                    confirmButton.disabled = false;
                    confirmButton.innerHTML = originalText;
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('❌ Terjadi kesalahan: ' + error);
            
            confirmButton.disabled = false;
            confirmButton.innerHTML = originalText;
        });
    }

    // ==========================================
    // LOAD PIC BY COMPANY
    // ==========================================
    function loadPicsForCompany() {
        const companyId = document.getElementById('company_id').value;
        const picSelect = document.getElementById('pic_id');
        
        if (!companyId) {
            picSelect.innerHTML = '<option value="">-- Pilih Company terlebih dahulu --</option>';
            document.getElementById('noPicBox').style.display = 'block';
            document.getElementById('picInfoBox').style.display = 'none';
            return;
        }

        fetch(`/transaksi/pics/by-company/${companyId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.pics.length > 0) {
                    let options = '<option value="">-- Pilih PIC --</option>';
                    data.pics.forEach(pic => {
                        options += `<option value="${pic.pic_id}" 
                            data-pic-name="${pic.pic_name}" 
                            data-pic-position="${pic.position || ''}"
                            data-pic-email="${pic.email || ''}"
                            data-pic-phone="${pic.phone || ''}">${pic.pic_name}</option>`;
                    });
                    picSelect.innerHTML = options;
                    document.getElementById('noPicBox').style.display = 'block';
                    document.getElementById('picInfoBox').style.display = 'none';
                } else {
                    picSelect.innerHTML = '<option value="">Tidak ada PIC untuk company ini</option>';
                    document.getElementById('noPicBox').style.display = 'block';
                    document.getElementById('picInfoBox').style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error loading PICs:', error);
                picSelect.innerHTML = '<option value="">Error loading PICs</option>';
            });
    }

    // ==========================================
    // HANDLE PIC CHANGE
    // ==========================================
    function handlePicChange() {
        const select = document.getElementById('pic_id');
        const option = select.options[select.selectedIndex];

        if (select.value) {
            const picName = option.getAttribute('data-pic-name');
            const picPosition = option.getAttribute('data-pic-position');
            const picEmail = option.getAttribute('data-pic-email');
            const picPhone = option.getAttribute('data-pic-phone');

            document.getElementById('pic_name').value = picName;
            document.getElementById('picPosition').textContent = picPosition || '-';
            document.getElementById('picEmail').textContent = picEmail || '-';
            document.getElementById('picPhone').textContent = picPhone || '-';

            document.getElementById('picInfoBox').style.display = 'block';
            document.getElementById('noPicBox').style.display = 'none';
        } else {
            document.getElementById('pic_name').value = '';
            document.getElementById('picInfoBox').style.display = 'none';
            document.getElementById('noPicBox').style.display = 'block';
        }
    }

    // ==========================================
    // HANDLE SALES VISIT CHANGE
    // ==========================================
    function handleSalesVisitChange() {
        const select = document.getElementById('sales_visit_id');
        const visitId = select.value;
        const option = select.options[select.selectedIndex];

        if (visitId) {
            const companyId = option.getAttribute('data-company-id');
            const companyName = option.getAttribute('data-company-name');
            const salesId = option.getAttribute('data-sales-id');
            const salesName = option.getAttribute('data-sales-name');
            const picId = option.getAttribute('data-pic-id');
            const picName = option.getAttribute('data-pic-name');
            const picPosition = option.getAttribute('data-pic-position');
            const picEmail = option.getAttribute('data-pic-email');
            const picPhone = option.getAttribute('data-pic-phone');
            const visitDate = option.getAttribute('data-visit-date');
            
            document.getElementById('company_id').value = companyId;
            document.getElementById('nama_perusahaan').value = companyName;
            updateCompanyInfo();
            
            setTimeout(() => {
                loadPicsForCompany();
            }, 100);
            
            document.getElementById('sales_id').value = salesId;
            document.getElementById('nama_sales').value = salesName;
            
            if (picId && picId !== 'null' && picId !== '') {
                setTimeout(() => {
                    document.getElementById('pic_id').value = picId;
                    document.getElementById('pic_name').value = picName;
                    
                    document.getElementById('picPosition').textContent = picPosition || '-';
                    document.getElementById('picEmail').textContent = picEmail || '-';
                    document.getElementById('picPhone').textContent = picPhone || '-';
                    
                    document.getElementById('picInfoBox').style.display = 'block';
                    document.getElementById('noPicBox').style.display = 'none';
                }, 200);
            }
            
            if (visitDate && document.getElementById('statusDeals').checked) {
                document.getElementById('tanggal_mulai_kerja').value = visitDate;
            }
        } else {
            document.getElementById('company_id').value = '';
            document.getElementById('nama_perusahaan').value = '';
            document.getElementById('sales_id').value = '';
            document.getElementById('nama_sales').value = '';
            document.getElementById('pic_id').value = '';
            document.getElementById('pic_name').value = '';
            document.getElementById('companyInfoBox').style.display = 'none';
            document.getElementById('picInfoBox').style.display = 'none';
            document.getElementById('noPicBox').style.display = 'block';
        }
    }

    // ==========================================
    // UPDATE COMPANY INFO
    // ==========================================
    function updateCompanyInfo() {
        const select = document.getElementById('company_id');
        const selected = select.options[select.selectedIndex];
        
        if (selected.value) {
            document.getElementById('nama_perusahaan').value = selected.getAttribute('data-name') || '';
            document.getElementById('companyType').textContent = selected.getAttribute('data-type') || '-';
            document.getElementById('companyPhone').textContent = selected.getAttribute('data-phone') || '-';
            document.getElementById('companyEmail').textContent = selected.getAttribute('data-email') || '-';
            document.getElementById('companyAddress').textContent = selected.getAttribute('data-address') || '-';
            document.getElementById('companyInfoBox').style.display = 'block';
        } else {
            document.getElementById('companyInfoBox').style.display = 'none';
        }
    }

    // ==========================================
    // UPDATE SALES NAME
    // ==========================================
    function updateSalesName() {
        const select = document.getElementById('sales_id');
        const selected = select.options[select.selectedIndex];
        document.getElementById('nama_sales').value = selected.getAttribute('data-name') || '';
    }

    // ==========================================
    // UPDATE FILE NAME
    // ==========================================
    function updateFileName(fieldId) {
        const input = document.getElementById(fieldId);
        const nameDisplay = document.getElementById(fieldId + '_name');
        if (input.files.length > 0) {
            nameDisplay.textContent = '✓ ' + input.files[0].name;
        } else {
            nameDisplay.textContent = '';
        }
    }

    // ==========================================
    // HANDLE DROP
    // ==========================================
    function handleDrop(e, fieldId) {
        e.preventDefault();
        
        // Cek apakah dalam mode Deals
        if (document.getElementById('statusFails').checked) {
            return; // Jangan izinkan drop jika Fails
        }
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            document.getElementById(fieldId).files = files;
            updateFileName(fieldId);
        }
        const dropZone = fieldId === 'bukti_spk' ? 'drop_spk' : 'drop_dp';
        document.getElementById(dropZone).style.borderColor = '#d1d5db';
        document.getElementById(dropZone).style.backgroundColor = 'white';
    }

    // ==========================================
    // OPEN MODAL
    // ==========================================
    function openTransaksiModal() {
        // Reset forms
        const formLeft = document.getElementById('transaksiForm');
        const formRight = document.getElementById('transaksiFormRight');
        if (formLeft) formLeft.reset();
        if (formRight) formRight.reset();
        
        // Reset hidden inputs
        const transaksiId = document.getElementById('transaksiId');
        const formMethod = document.getElementById('formMethod');
        if (transaksiId) transaksiId.value = '';
        if (formMethod) formMethod.value = 'POST';
        
        // Reset file names
        const buktiSpkName = document.getElementById('bukti_spk_name');
        const buktiDpName = document.getElementById('bukti_dp_name');
        if (buktiSpkName) buktiSpkName.textContent = '';
        if (buktiDpName) buktiDpName.textContent = '';
        
        // Reset modal title
        const modalTitle = document.getElementById('modalTitle');
        if (modalTitle) modalTitle.textContent = 'Tambah Transaksi Baru';
        
        // Reset info boxes
        const companyInfoBox = document.getElementById('companyInfoBox');
        const picInfoBox = document.getElementById('picInfoBox');
        const noPicBox = document.getElementById('noPicBox');
        if (companyInfoBox) companyInfoBox.style.display = 'none';
        if (picInfoBox) picInfoBox.style.display = 'none';
        if (noPicBox) noPicBox.style.display = 'block';
        
        // Reset PIC select
        const picSelect = document.getElementById('pic_id');
        if (picSelect) picSelect.innerHTML = '<option value="">-- Pilih Company terlebih dahulu --</option>';
        
        // Reset nama sales dan perusahaan
        const namaSales = document.getElementById('nama_sales');
        const namaPerusahaan = document.getElementById('nama_perusahaan');
        if (namaSales) namaSales.value = '';
        if (namaPerusahaan) namaPerusahaan.value = '';
        
        // Set status default ke Fails dan trigger handleStatusChange
        document.getElementById('statusFails').checked = true;
        document.getElementById('statusDeals').checked = false;
        handleStatusChange();
        
        // Show modal
        const modal = document.getElementById('transaksiModal');
        if (modal) modal.classList.add('active');
    }

    // ==========================================
    // CLOSE MODAL
    // ==========================================
    function closeTransaksiModal() {
        const modal = document.getElementById('transaksiModal');
        if (modal) modal.classList.remove('active');
    }

    // ==========================================
    // EDIT TRANSAKSI
    // ==========================================
    function editTransaksi(id) {
        fetch(`/transaksi/${id}/edit`)
            .then(response => response.json())
            .then(data => {
                const transaksiId = document.getElementById('transaksiId');
                const salesVisitId = document.getElementById('sales_visit_id');
                const salesId = document.getElementById('sales_id');
                const companyId = document.getElementById('company_id');
                const namaSales = document.getElementById('nama_sales');
                const namaPerusahaan = document.getElementById('nama_perusahaan');
                const nilaiProyek = document.getElementById('nilai_proyek');
                const tanggalMulai = document.getElementById('tanggal_mulai_kerja');
                const tanggalSelesai = document.getElementById('tanggal_selesai_kerja');
                const keterangan = document.getElementById('keterangan');
                const modalTitle = document.getElementById('modalTitle');
                
                if (transaksiId) transaksiId.value = data.id;
                if (salesVisitId) salesVisitId.value = data.sales_visit_id || '';
                if (salesId) salesId.value = data.sales_id;
                if (companyId) companyId.value = data.company_id;
                if (namaSales) namaSales.value = data.nama_sales;
                if (namaPerusahaan) namaPerusahaan.value = data.nama_perusahaan;
                if (nilaiProyek) nilaiProyek.value = data.nilai_proyek;
                if (tanggalMulai) tanggalMulai.value = data.tanggal_mulai_kerja || '';
                if (tanggalSelesai) tanggalSelesai.value = data.tanggal_selesai_kerja || '';
                if (keterangan) keterangan.value = data.keterangan || '';
                
                // Set status radio button
                if (data.status === 'Deals') {
                    document.getElementById('statusDeals').checked = true;
                    document.getElementById('statusFails').checked = false;
                } else {
                    document.getElementById('statusFails').checked = true;
                    document.getElementById('statusDeals').checked = false;
                }
                handleStatusChange();
                
                updateCompanyInfo();
                
                setTimeout(() => {
                    loadPicsForCompany();
                    setTimeout(() => {
                        const picId = document.getElementById('pic_id');
                        if (picId) picId.value = data.pic_id || '';
                        handlePicChange();
                    }, 200);
                }, 100);
                
                if (modalTitle) modalTitle.textContent = 'Edit Transaksi';
                
                const modal = document.getElementById('transaksiModal');
                if (modal) modal.classList.add('active');
            })
            .catch(error => {
                console.error('Error:', error);
                alert('❌ Terjadi kesalahan saat mengambil data');
            });
    }

    // ==========================================
    // EVENT LISTENER - CLOSE ON BACKDROP CLICK
    // ==========================================
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('transaksiModal');
        const confirmFailsModal = document.getElementById('confirmFailsModal');
        const confirmDealsModal = document.getElementById('confirmDealsModal');
        
        if (event.target === modal) {
            modal.classList.remove('active');
        }
        if (event.target === confirmFailsModal) {
            confirmFailsModal.classList.remove('active');
        }
        if (event.target === confirmDealsModal) {
            confirmDealsModal.classList.remove('active');
        }
    });
</script>