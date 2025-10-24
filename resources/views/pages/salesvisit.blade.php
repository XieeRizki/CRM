@extends('layout.main')
@section('title','Sales Visit')

@section('content')
<div class="container-expanded mx-auto px-6 lg:px-8 py-8 pt-[60px]">
    
    <!-- Tombol Add Component -->
    <x-salesvisit.action.action :currentMenuId="$currentMenuId" :salesUsers="$salesUsers" :provinces="$provinces" />

    <!-- HAPUS BARIS INI: -->
    <!-- <x-salesvisit.action.edit :currentMenuId="$currentMenuId" :salesUsers="$salesUsers" :provinces="$provinces" /> -->

    <!-- Sales Visit Table -->
    <div class="bg-white rounded-xl shadow-sm border mt-4">
        <div class="p-6">

            <x-globals.filtersearch
                tableId="salesVisitTable"
                :columns="[
                    'number',
                    'sales',
                    'customer_name',
                    'company',
                    'province',
                    'visit_date',
                    'purpose',
                    'follow_up',
                    'actions'
                ]"
                :filters="['Follow Up' => ['Ya', 'Tidak']]"
                ajaxUrl="{{ route('salesvisit.search') }}"
                placeholder="Cari nama customer, company, atau sales..."
            />
            
            <!-- Table Component -->
            <x-salesvisit.table.table :salesVisits="$salesVisits" :currentMenuId="$currentMenuId" />

            <!-- Pagination -->
            <x-globals.pagination :paginator="$salesVisits" />
        </div>
    </div>
</div>

<!-- Edit Modal -->

@push('scripts')
<script src="{{ asset('js/search.js') }}"></script>
<script src="{{ asset('js/address-cascade.js') }}"></script>
<script src="{{ asset('js/salesvisit-modal.js') }}"></script>
@endpush

<script>
function deleteVisit(visitId, deleteRoute, csrfToken) {
    console.log('deleteVisit called:', { visitId, deleteRoute, csrfToken });

    if (confirm('Apakah Anda yakin ingin menghapus data kunjungan ini?')) {
        // Gunakan route yang benar dengan ID
        const correctRoute = `/salesvisit/${visitId}`;
        
        fetch(correctRoute, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                // Jika response tidak ok, coba parse error message
                return response.json().then(errorData => {
                    throw new Error(errorData.message || 'Network response was not ok');
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                // Refresh table atau reload page setelah 1 detik
                setTimeout(() => {
                    if (window.salesVisitTableHandler && typeof window.salesVisitTableHandler.refresh === 'function') {
                        window.salesVisitTableHandler.refresh();
                    } else {
                        location.reload();
                    }
                }, 1000);
            } else {
                throw new Error(data.message || 'Gagal menghapus data');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Gagal menghapus data: ' + error.message, 'error');
        });
    }
}

// Function untuk show notification
function showNotification(message, type = 'info') {
    // Buat element notification jika belum ada
    let notification = document.getElementById('global-notification');
    if (!notification) {
        notification = document.createElement('div');
        notification.id = 'global-notification';
        notification.className = 'fixed top-4 right-4 z-[1000] p-4 rounded-lg shadow-lg text-white transform transition-all duration-300';
        document.body.appendChild(notification);
    }
    
    const bgColor = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        info: 'bg-blue-500'
    };
    
    notification.className = `fixed top-4 right-4 z-[1000] p-4 rounded-lg shadow-lg text-white transform transition-all duration-300 ${bgColor[type]}`;
    notification.innerHTML = `
        <div class="flex items-center gap-2">
            <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'times' : 'info'}-circle"></i>
            <span>${message}</span>
        </div>
    `;
    
    // Show notification
    setTimeout(() => {
        notification.classList.remove('opacity-0');
    }, 100);
    
    // Hide after 3 seconds
    setTimeout(() => {
        notification.classList.add('opacity-0');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

document.addEventListener('DOMContentLoaded', () => {
    console.log('SalesVisit page loaded');
});
</script>
@endsection