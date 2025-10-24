@props(['salesVisits', 'currentMenuId'])

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden fade-in">
    <!-- Header -->
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Sales Visit Management</h3>
            <p class="text-sm text-gray-600 mt-1">Kelola data kunjungan sales dan informasinya</p>
        </div>
        <div class="text-sm text-gray-600">
            Total: <span class="font-semibold text-gray-900">{{ $salesVisits->total() }}</span> kunjungan
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table id="salesVisitTable" class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">No</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Sales</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Customer Name</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Company</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Location</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Visit Date</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Purpose</th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Follow Up</th>
                    <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($salesVisits as $index => $visit)
                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                        <span class="font-medium">{{ $salesVisits->firstItem() + $index }}</span>
                    </td>
                    
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="h-10 w-10 flex-shrink-0">
                                <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                    <i class="fas fa-user-tie text-indigo-600"></i>
                                </div>
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-900">{{ $visit->sales->username ?? '-' }}</div>
                                <div class="text-xs text-gray-500">{{ $visit->sales->email ?? 'No email' }}</div>
                            </div>
                        </div>
                    </td>
                    
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $visit->customer_name ?? '-' }}</div>
                    </td>
                    
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">{{ $visit->company_name ?? '-' }}</div>
                    </td>
                    
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">
                            <div class="flex items-center space-x-1">
                                <i class="fas fa-map-marker-alt text-gray-400 text-xs"></i>
                                <span>{{ $visit->province->name ?? '-' }}</span>
                            </div>
                            @if($visit->regency)
                            <div class="text-xs text-gray-500 mt-1">{{ $visit->regency->name }}</div>
                            @endif
                        </div>
                    </td>
                    
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                            @if($visit->visit_date)
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-calendar text-gray-400 text-xs"></i>
                                    <span>{{ $visit->visit_date->format('d M Y') }}</span>
                                </div>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </div>
                    </td>
                    
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-700 max-w-xs">
                            @if($visit->visit_purpose)
                                <span class="line-clamp-2" title="{{ $visit->visit_purpose }}">
                                    {{ Str::limit($visit->visit_purpose, 50) }}
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </div>
                    </td>
                    
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($visit->is_follow_up)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>
                                Ya
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                <i class="fas fa-times-circle mr-1"></i>
                                Tidak
                            </span>
                        @endif
                    </td>
                    
                    <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                        <div class="flex items-center justify-end space-x-2">
                            @if(auth()->user()->canAccess($currentMenuId, 'edit'))
                            <button 
                            data-visit-id="{{ $visit->id }}"
                            data-sales-id="{{ $visit->sales_id }}"
                            data-customer-name="{{ $visit->customer_name }}"
                            data-company="{{ $visit->company_name ?? '' }}"
                            data-province-id="{{ $visit->province_id }}"
                            data-regency-id="{{ $visit->regency_id ?? '' }}"
                            data-district-id="{{ $visit->district_id ?? '' }}"
                            data-village-id="{{ $visit->village_id ?? '' }}"
                            data-address="{{ $visit->address ?? '' }}"
                            data-visit-date="{{ $visit->visit_date->format('Y-m-d') }}"
                            data-purpose="{{ $visit->visit_purpose }}"
                            data-follow-up="{{ $visit->is_follow_up ? 1 : 0 }}"
                            onclick="openEditVisitModalFromButton(this)"
                            class="text-blue-600 hover:text-blue-900 p-2 rounded-lg hover:bg-blue-50 transition-all duration-150 flex items-center" 
                            title="Edit Visit">
                            <i class="fas fa-edit"></i>
                        </button>
                            @endif
                            
                            @if(auth()->user()->canAccess($currentMenuId, 'delete'))
                            <button type="button" 
                                onclick="deleteVisit({{ $visit->id }}, '{{ route('salesvisit.destroy', $visit->id) }}', '{{ csrf_token() }}')"
                                class="text-red-600 hover:text-red-900 p-2 rounded-lg hover:bg-red-50 transition-all duration-150 flex items-center" 
                                title="Hapus Visit">
                                <i class="fas fa-trash"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <div class="h-24 w-24 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                                <i class="fas fa-inbox text-5xl text-gray-300"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-1">Belum Ada Data</h3>
                            <p class="text-sm text-gray-500">Belum ada data kunjungan sales yang tersedia</p>
                            @if(auth()->user()->canAccess($currentMenuId, 'create'))
                            <button onclick="openVisitModal()" class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                <i class="fas fa-plus mr-2"></i>
                                Tambah Kunjungan
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Info & Links -->
    @if($salesVisits->hasPages())
    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex items-center justify-between">
        <div class="text-sm text-gray-700">
            Menampilkan 
            <span class="font-medium">{{ $salesVisits->firstItem() ?? 0 }}</span>
            sampai 
            <span class="font-medium">{{ $salesVisits->lastItem() ?? 0 }}</span>
            dari 
            <span class="font-medium">{{ $salesVisits->total() }}</span>
            hasil
        </div>
        <div>
            {{ $salesVisits->links() }}
        </div>
    </div>
    @endif
</div>

<style>
@keyframes modalSlideIn {
    from { 
        opacity: 0; 
        transform: translateY(-20px) scale(0.95); 
    }
    to { 
        opacity: 1; 
        transform: translateY(0) scale(1); 
    }
}

.animate-modal-in { 
    animation: modalSlideIn 0.25s ease-out; 
}

.fade-in { 
    animation: fadeIn 0.3s ease-in; 
}

@keyframes fadeIn { 
    from { opacity: 0; } 
    to { opacity: 1; } 
}

/* Line clamp for purpose text */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Responsive improvements */
@media (max-width: 1024px) {
    #salesVisitTable {
        font-size: 0.875rem;
    }
    
    #salesVisitTable th,
    #salesVisitTable td {
        padding: 0.75rem 1rem;
    }
}

@media (max-width: 768px) {
    #salesVisitTable {
        font-size: 0.8125rem;
    }
    
    #salesVisitTable th,
    #salesVisitTable td {
        padding: 0.5rem 0.75rem;
    }
}
</style>