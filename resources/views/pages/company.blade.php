@extends('layout.main')
@section('title','Company')

@section('content')
<div class="container-expanded mx-auto px-6 lg:px-8 py-8 pt-[60px] mt-0 fade-in">
    <!-- KPI Section -->
    <x-company.attribut.kpi
        :totalCompanies="$totalCompanies"
        :jenisCompanies="$jenisCompanies"
        :tierCompanies="$tierCompanies"
        :activeCompanies="$activeCompanies"
    />
    
    <!-- Company Card dengan Everything Inside -->
    <div style="background-color: #ffffff; border-radius: 0.5rem; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1); border: 1px solid #e5e7eb; overflow: hidden; margin-top: 0.5rem;">
        
        <!-- Card Header dengan Title dan Action Button -->
        <div style="padding: 0.5rem 1.5rem; border-bottom: 1px solid #e5e7eb;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h3 style="font-size: 1.125rem; font-weight: 600; color: #111827; margin: 0;">Company Management</h3>
                    <p style="font-size: 0.875rem; color: #6b7280; margin: 0.25rem 0 0 0;">Kelola data perusahaan dan informasinya</p>
                </div>
                
                <div style="display: flex; gap: 0.75rem; align-items: center;">
                    <button onclick="openExportPdfModal()" 
                        style="display: flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1rem; background: #ffffff; color: #4f46e5; border: 1px solid #4f46e5; border-radius: 0.5rem; font-weight: 500; font-size: 0.875rem; cursor: pointer; transition: all 0.2s;">
                        <i class="fas fa-file-pdf"></i>
                        <span>Export PDF</span>
                    </button>

                    <button onclick="openExportExcelModal()" 
                    style="display: flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1rem; background: #ffffff; color: #15803d; border: 1px solid #15803d; border-radius: 0.5rem; font-weight: 500; font-size: 0.875rem; cursor: pointer; transition: all 0.2s;">
                    <i class="fas fa-file-excel"></i>
                    <span>Export Excel</span>


                    @if(auth()->user()->canAccess($currentMenuId, 'create'))
                    <button onclick="openAddCompanyModal()" 
                        style="display: flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1rem; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: white; border: none; border-radius: 0.5rem; font-weight: 500; font-size: 0.875rem; cursor: pointer; box-shadow: 0 2px 4px rgba(99, 102, 241, 0.2); transition: all 0.2s;">
                        <i class="fas fa-plus"></i>
                        <span>Tambah Perusahaan</span>
                    </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Search and Filter Section -->
        <div style="padding: 0.5rem 1.5rem; background-color: #f9fafb; border-bottom: 1px solid #e5e7eb; fade-in">
            <div style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: center;">
                <x-globals.filtersearch
                    tableId="companyTable"
                    :columns="[
                        'number',
                        'company_name', 
                        'company_type',
                        'tier',
                        'description', 
                        'status',
                        'actions'
                    ]"
                    :filters="[
                        'Type' => $types->pluck('type_name', 'company_type_id')->toArray(),
                        'Tier' => ['A', 'B', 'C', 'D'],
                        'Status' => ['Active', 'Inactive']
                    ]"
                    ajaxUrl="{{ route('company.search') }}"
                    placeholder="Cari nama perusahaan, deskripsi, atau tipe..."
                />
            </div>
        </div>
        
        <!-- Table Section - NO PADDING! -->
        <x-company.table.table :companies="$companies" :currentMenuId="$currentMenuId" />
        
        <!-- Pagination -->
        @if($companies->hasPages())
        <div style="border-top: 1px solid #e5e7eb; background-color: #f9fafb;">
            <x-globals.pagination :paginator="$companies" />
        </div>
        @endif
    </div>
</div>

<!-- Modals -->
<x-company.action.action :types="$types" :provinces="$provinces"/>
<x-company.action.edit :types="$types" :provinces="$provinces"/>

<!-- Export PDF Modal -->
<!-- Export PDF Modal -->
<div id="exportPdfModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: #fff; border-radius: 0.5rem; width: 100%; max-width: 480px; padding: 1.5rem; box-shadow: 0 10px 25px rgba(0,0,0,0.2); max-height: 85vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="margin: 0; font-size: 1.125rem; font-weight: 600; color: #111827;">Export PDF Rumah Sakit</h3>
            <button onclick="closeExportPdfModal()" style="background: none; border: none; cursor: pointer; font-size: 1.25rem; color: #6b7280;">&times;</button>
        </div>

        <!-- Tab Switcher -->
        <div style="display: flex; gap: 0.5rem; margin-bottom: 1.25rem; border-bottom: 1px solid #e5e7eb;">
            <button type="button" onclick="switchExportTab('detail')" id="tabBtnDetail"
                style="padding: 0.5rem 0.75rem; background: none; border: none; border-bottom: 2px solid #4f46e5; color: #4f46e5; font-weight: 600; font-size: 0.8rem; cursor: pointer;">
                1 RS (Detail)
            </button>
            <button type="button" onclick="switchExportTab('filter')" id="tabBtnFilter"
                style="padding: 0.5rem 0.75rem; background: none; border: none; border-bottom: 2px solid transparent; color: #6b7280; font-weight: 600; font-size: 0.8rem; cursor: pointer;">
                Filter Tipe/Tier
            </button>
            <button type="button" onclick="switchExportTab('multi')" id="tabBtnMulti"
                style="padding: 0.5rem 0.75rem; background: none; border: none; border-bottom: 2px solid transparent; color: #6b7280; font-weight: 600; font-size: 0.8rem; cursor: pointer;">
                Pilih Beberapa RS
            </button>
        </div>

        <!-- Tab 1: Detail 1 RS -->
        <div id="tabContentDetail">
            <p style="font-size: 0.875rem; color: #6b7280; margin-bottom: 1rem;">Pilih satu rumah sakit untuk melihat profil lengkapnya dalam PDF.</p>
            <select id="exportPdfCompanySelect" style="width: 100%; padding: 0.625rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; margin-bottom: 1.25rem;">
                <option value="">-- Pilih Rumah Sakit --</option>
            </select>
        </div>

        <!-- Tab 2: Filter -->
        <div id="tabContentFilter" style="display: none;">
            <p style="font-size: 0.875rem; color: #6b7280; margin-bottom: 1rem;">Pilih tipe dan tier untuk mengekspor daftar rumah sakit. Biarkan kosong untuk menyertakan semua.</p>

            <label style="display: block; font-size: 0.8rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">Tipe</label>
            <select id="exportPdfTypeSelect" style="width: 100%; padding: 0.625rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; margin-bottom: 1rem;">
                <option value="">Semua Tipe</option>
                @foreach($types as $type)
                    <option value="{{ $type->company_type_id }}">{{ $type->type_name }}</option>
                @endforeach
            </select>

            <label style="display: block; font-size: 0.8rem; font-weight: 500; color: #374151; margin-bottom: 0.375rem;">Tier</label>
            <select id="exportPdfTierSelect" style="width: 100%; padding: 0.625rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem; margin-bottom: 1.25rem;">
                <option value="">Semua Tier</option>
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="C">C</option>
                <option value="D">D</option>
            </select>
        </div>

        <!-- Tab 3: Pilih Beberapa RS -->
        <div id="tabContentMulti" style="display: none;">
            <p style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.75rem;">Centang satu atau lebih rumah sakit yang ingin dimasukkan ke dalam daftar PDF.</p>
            <div id="exportPdfMultiList" style="max-height: 220px; overflow-y: auto; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.5rem; margin-bottom: 1.25rem;">
                <p style="font-size: 0.8rem; color: #9ca3af; margin: 0.5rem;">Memuat data...</p>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
            <button onclick="closeExportPdfModal()" 
                style="padding: 0.625rem 1rem; background: #f3f4f6; color: #374151; border: none; border-radius: 0.5rem; font-weight: 500; font-size: 0.875rem; cursor: pointer;">
                Batal
            </button>
            <button onclick="submitExportPdf()" 
                style="padding: 0.625rem 1rem; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: white; border: none; border-radius: 0.5rem; font-weight: 500; font-size: 0.875rem; cursor: pointer;">
                Export
            </button>
        </div>
    </div>
</div>

<!-- Export Excel Modal -->
<div id="exportExcelModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: #fff; border-radius: 0.5rem; width: 100%; max-width: 420px; padding: 1.5rem; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="margin: 0; font-size: 1.125rem; font-weight: 600; color: #111827;">Export Pivot Excel</h3>
            <button onclick="closeExportExcelModal()" style="background: none; border: none; cursor: pointer; font-size: 1.25rem; color: #6b7280;">&times;</button>
        </div>

        <p style="font-size: 0.875rem; color: #6b7280; margin-bottom: 1rem;">
            File Excel akan berisi tabel rekap perusahaan per Tier, mencakup jumlah perusahaan, status aktif/nonaktif, dan total PIC.
        </p>

        <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
            <button onclick="closeExportExcelModal()" 
                style="padding: 0.625rem 1rem; background: #f3f4f6; color: #374151; border: none; border-radius: 0.5rem; font-weight: 500; font-size: 0.875rem; cursor: pointer;">
                Batal
            </button>
            <button onclick="submitExportExcel()" 
                style="padding: 0.625rem 1rem; background: linear-gradient(135deg, #15803d 0%, #22c55e 100%); color: white; border: none; border-radius: 0.5rem; font-weight: 500; font-size: 0.875rem; cursor: pointer;">
                Export
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/address-cascade.js') }}"></script>
<script src="{{ asset('js/company-modal.js') }}"></script>
<script src="{{ asset('js/search.js') }}"></script>
@endpush

<style>
    /* Hover effects for buttons */
    button:hover, a[href]:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    /* Focus styles for inputs */
    #searchInput:focus,
    #filterType:focus,
    #filterTier:focus,
    #filterStatus:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        button span, a span {
            display: none;
        }
    }
</style>

<script>
// ==================== DELETE FUNCTION ====================
function deleteCompany(companyId, deleteRoute, csrfToken) {
    console.log('🗑️ Delete company:', {companyId, deleteRoute, csrfToken});
    
    deleteRecord(companyId, deleteRoute, csrfToken, (data) => {
        console.log('✅ Delete success:', data);
        if (window.companyTableHandler) {
            console.log('🔄 Refreshing table...');
            window.companyTableHandler.refresh();
        } else {
            console.warn('⚠️ companyTableHandler not found, reloading page');
            location.reload();
        }
    });
}

// ==================== EXPORT PDF MODAL ====================
let currentExportTab = 'detail';

function openExportPdfModal() {
    document.getElementById('exportPdfModal').style.display = 'flex';
    switchExportTab('detail');
    loadCompanyOptionsForExport();
}

function closeExportPdfModal() {
    document.getElementById('exportPdfModal').style.display = 'none';
}

function switchExportTab(tab) {
    currentExportTab = tab;

    document.getElementById('tabContentDetail').style.display = tab === 'detail' ? 'block' : 'none';
    document.getElementById('tabContentFilter').style.display = tab === 'filter' ? 'block' : 'none';
    document.getElementById('tabContentMulti').style.display = tab === 'multi' ? 'block' : 'none';

    ['Detail', 'Filter', 'Multi'].forEach(key => {
        const btn = document.getElementById('tabBtn' + key);
        const isActive = key.toLowerCase() === tab;
        btn.style.borderBottomColor = isActive ? '#4f46e5' : 'transparent';
        btn.style.color = isActive ? '#4f46e5' : '#6b7280';
    });
}

function loadCompanyOptionsForExport() {
    fetch('{{ route("company.getCompaniesForDropdown") }}')
        .then(res => res.json())
        .then(data => {
            const select = document.getElementById('exportPdfCompanySelect');
            const multiList = document.getElementById('exportPdfMultiList');

            select.innerHTML = '<option value="">-- Pilih Rumah Sakit --</option>';
            multiList.innerHTML = '';

            if (data.success && data.companies.length > 0) {
                data.companies.forEach(company => {
                    const option = document.createElement('option');
                    option.value = company.id;
                    option.textContent = company.name;
                    select.appendChild(option);

                    const label = document.createElement('label');
                    label.style.cssText = 'display: flex; align-items: center; gap: 0.5rem; padding: 0.375rem 0.25rem; font-size: 0.85rem; color: #374151; cursor: pointer;';
                    label.innerHTML = `<input type="checkbox" class="export-pdf-checkbox" value="${company.id}"> ${company.name}`;
                    multiList.appendChild(label);
                });
            } else {
                multiList.innerHTML = '<p style="font-size: 0.8rem; color: #9ca3af; margin: 0.5rem;">Tidak ada data.</p>';
            }
        })
        .catch(err => {
            console.error('❌ Error loading companies for export:', err);
        });
}

function submitExportPdf() {
    if (currentExportTab === 'detail') {
        const companyId = document.getElementById('exportPdfCompanySelect').value;
        if (!companyId) {
            alert('Silakan pilih rumah sakit terlebih dahulu.');
            return;
        }
        window.open(`/company/${companyId}/pdf`, '_blank');

    } else if (currentExportTab === 'filter') {
        const typeId = document.getElementById('exportPdfTypeSelect').value;
        const tier = document.getElementById('exportPdfTierSelect').value;

        let url = '{{ route("company.pdf.list") }}?';
        const params = [];
        if (typeId) params.push('type_id=' + encodeURIComponent(typeId));
        if (tier) params.push('tier=' + encodeURIComponent(tier));
        url += params.join('&');

        window.open(url, '_blank');

    } else if (currentExportTab === 'multi') {
        const checked = document.querySelectorAll('.export-pdf-checkbox:checked');
        if (checked.length === 0) {
            alert('Pilih minimal satu rumah sakit.');
            return;
        }
        const ids = Array.from(checked).map(cb => cb.value).join(',');
        window.open('{{ route("company.pdf.selected") }}?ids=' + encodeURIComponent(ids), '_blank');
    }

    closeExportPdfModal();
}

// ==================== EXPORT EXCEL MODAL ====================
function openExportExcelModal() {
    document.getElementById('exportExcelModal').style.display = 'flex';
}

function closeExportExcelModal() {
    document.getElementById('exportExcelModal').style.display = 'none';
}

function submitExportExcel() {
    window.location.href = '{{ route("company.pivot.excel") }}';
    closeExportExcelModal();
}

// ==================== TABLE HANDLER INITIALIZATION ====================
document.addEventListener('DOMContentLoaded', () => {
    console.log('📋 Company page loaded');
    
    if (typeof TableHandler === 'undefined') {
        console.error('❌ TableHandler class not found. search.js may not be loaded.');
        return;
    }

    console.log('🎯 Creating TableHandler instance...');
    
    try {
        window.companyTableHandler = new TableHandler({
            tableId: 'companyTable',
            ajaxUrl: '{{ route("company.search") }}',
            filters: ['type', 'tier', 'status'],
            columns: ['number', 'company_name', 'company_type', 'tier', 'description', 'status', 'actions']
        });
        
        console.log('✅ TableHandler initialized:', window.companyTableHandler);
    } catch (error) {
        console.error('❌ Error initializing TableHandler:', error);
    }
});
</script>
@endsection