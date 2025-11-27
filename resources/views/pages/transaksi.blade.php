@extends('layout.main')

@section('content')
<div class="container-fluid mt-12" style="padding: 1rem;">

    <!-- KPI Cards -->
    @include('components.transaksi.kpi', ['transaksi' => $transaksi])
        
    <!-- Main Card -->
    <div style="background-color: white; border-radius: 0.375rem; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1); border: 1px solid #e5e7eb; overflow: hidden;">
        
        <!-- Header -->
        <div style="padding: 1rem; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h3 style="font-size: 1rem; font-weight: 600; color: #111827; margin: 0;">Manajemen Transaksi</h3>
                <p style="font-size: 0.75rem; color: #6b7280; margin: 0.25rem 0 0 0;">Kelola transaksi deals dan fails</p>
            </div>
            @if(auth()->user()->canAccess($currentMenuId ?? 17, 'create'))
            <button onclick="openTransaksiModal()"
                style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0.875rem; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; border: none; border-radius: 0.375rem; font-weight: 500; font-size: 0.75rem; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 6px rgba(59, 130, 246, 0.2);"
                onmouseover="this.style.boxShadow='0 10px 15px rgba(59, 130, 246, 0.3)'; this.style.transform='translateY(-2px)'"
                onmouseout="this.style.boxShadow='0 4px 6px rgba(59, 130, 246, 0.2)'; this.style.transform='translateY(0)'">
                <i class="fas fa-plus"></i>
                <span>Tambah Transaksi</span>
            </button>
            @endif
        </div>

        <!-- Search Filter -->
        <div style="padding: 0.75rem 1rem; background-color: white; border-bottom: 1px solid #e5e7eb;">
            <form action="{{ route('transaksi.search') }}" method="GET" style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                <input type="text" name="search" placeholder="Cari nama sales, perusahaan, atau status..."
                    value="{{ request('search', '') }}"
                    style="flex: 1; min-width: 200px; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.75rem; transition: all 0.2s;"
                    onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1)'"
                    onblur="this.style.borderColor='#d1d5db'; this.style.boxShadow='none'">
                <button type="submit"
                    style="padding: 0.5rem 0.875rem; background-color: #3b82f6; color: white; border: none; border-radius: 0.375rem; font-weight: 500; font-size: 0.75rem; cursor: pointer; transition: all 0.2s;"
                    onmouseover="this.style.backgroundColor='#2563eb'"
                    onmouseout="this.style.backgroundColor='#3b82f6'">
                    <i class="fas fa-search"></i> Cari
                </button>
            </form>
        </div>

        <!-- Table -->
        @include('components.transaksi.table', ['transaksi' => $transaksi, 'currentMenuId' => $currentMenuId])
    </div>
</div>

<!-- Modal Form Create/Edit -->
@include('components.transaksi.modal-form', ['sales' => $sales, 'companies' => $companies, 'salesVisits' => $salesVisits])

<!-- Modal Detail -->
@include('components.transaksi.modal-detail')

<style>
    @keyframes slideDown {
        from { transform: translateY(-20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    
    .modal {
        display: none;
        position: fixed;
        inset: 0;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 50;
        align-items: center;
        justify-content: center;
        overflow-y: auto;
    }

    .modal.active {
        display: flex;
    }

    .modal-content {
        background-color: white;
        border-radius: 0.375rem;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        max-width: 1200px;
        width: 95%;
        max-height: 90vh;
        overflow: hidden;
        animation: slideUp 0.3s ease-out;
        display: flex;
        flex-direction: column;
    }

    @keyframes slideUp {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
</style>

<script>
// ==========================================
// FUNGSI INI HANYA UNTUK MODAL DETAIL
// Semua fungsi untuk modal form ada di modal-form.blade.php
// ==========================================

// Fungsi viewTransaksi dipanggil dari modal-detail.blade.php
// JANGAN HAPUS fungsi ini
function viewTransaksi(id) {
    fetch(`/transaksi/${id}`)
        .then(response => response.json())
        .then(data => {
            let statusBadge = data.status === 'Deals' 
                ? '<span style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.25rem 0.625rem; background-color: #dcfce7; color: #166534; border-radius: 9999px; font-size: 0.7rem; font-weight: 600;"><i class="fas fa-check-circle"></i> Deals</span>'
                : '<span style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.25rem 0.625rem; background-color: #fee2e2; color: #991b1b; border-radius: 9999px; font-size: 0.7rem; font-weight: 600;"><i class="fas fa-times-circle"></i> Fails</span>';
            
            let html = `
                <div style="padding: 1rem;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <p style="font-size: 0.65rem; color: #6b7280; font-weight: 600; text-transform: uppercase; margin: 0;">Nama Sales</p>
                            <p style="color: #111827; font-weight: 500; margin: 0.25rem 0 0 0; font-size: 0.875rem;">${data.nama_sales}</p>
                        </div>
                        <div>
                            <p style="font-size: 0.65rem; color: #6b7280; font-weight: 600; text-transform: uppercase; margin: 0;">Perusahaan</p>
                            <p style="color: #111827; font-weight: 500; margin: 0.25rem 0 0 0; font-size: 0.875rem;">${data.nama_perusahaan}</p>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <p style="font-size: 0.65rem; color: #6b7280; font-weight: 600; text-transform: uppercase; margin: 0;">Nilai Proyek</p>
                            <p style="color: #111827; font-weight: 500; margin: 0.25rem 0 0 0; font-size: 0.875rem;">Rp${new Intl.NumberFormat('id-ID').format(data.nilai_proyek)}</p>
                        </div>
                        <div>
                            <p style="font-size: 0.65rem; color: #6b7280; font-weight: 600; text-transform: uppercase; margin: 0;">Status</p>
                            <div style="margin: 0.25rem 0 0 0;">${statusBadge}</div>
                        </div>
                    </div>

                    ${data.tanggal_mulai_kerja ? `
                    <div style="margin-bottom: 1rem;">
                        <p style="font-size: 0.65rem; color: #6b7280; font-weight: 600; text-transform: uppercase; margin: 0;">Tanggal Kerja</p>
                        <p style="color: #111827; font-weight: 500; margin: 0.25rem 0 0 0; font-size: 0.875rem;">${data.tanggal_mulai_kerja} s/d ${data.tanggal_selesai_kerja || '-'}</p>
                    </div>
                    ` : ''}
                    
                    ${data.keterangan ? `
                    <div>
                        <p style="font-size: 0.65rem; color: #6b7280; font-weight: 600; text-transform: uppercase; margin: 0;">Keterangan</p>
                        <p style="color: #111827; margin: 0.25rem 0 0 0; font-size: 0.875rem;">${data.keterangan}</p>
                    </div>
                    ` : ''}
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
    const detailModal = document.getElementById('detailModal');
    if (detailModal) detailModal.classList.remove('active');
}

// Event listener untuk close modal detail saat klik backdrop
window.addEventListener('click', function(event) {
    const detailModal = document.getElementById('detailModal');
    if (event.target === detailModal) {
        detailModal.classList.remove('active');
    }
});
</script>
@endsection