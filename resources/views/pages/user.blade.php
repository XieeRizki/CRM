@extends('layout.settings')
@section('title','User Management')

@section('content')
<div class="container-expanded mx-auto px-6 lg:px-8 py-4">
    <x-settingsm.kpi />
    <!-- User Management Card dengan Everything Inside -->
    <div style="background-color: #ffffff; border-radius: 0.5rem; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1); border: 1px solid #e5e7eb; overflow: hidden;">
        
        <!-- Card Header dengan Title dan Action Buttons -->
        <div style="padding: 0.5rem 1.5rem; border-bottom: 1px solid #e5e7eb;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h3 style="font-size: 1.125rem; font-weight: 600; color: #111827; margin: 0;">User Management</h3>
                    <p style="font-size: 0.875rem; color: #6b7280; margin: 0.25rem 0 0 0;">Kelola data user dan informasinya</p>
                </div>
                
                <!-- Action Button - HANYA TAMBAH USER -->
                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                    @if(auth()->user()->canAccess($currentMenuId, 'create'))
                    <button onclick="openUserModal()"
                        style="display: flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1rem; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: white; border: none; border-radius: 0.5rem; font-weight: 500; font-size: 0.875rem; cursor: pointer; box-shadow: 0 2px 4px rgba(99, 102, 241, 0.2); transition: all 0.2s;">
                        <i class="fas fa-plus"></i>
                        <span>Tambah User</span>
                    </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Search and Filter Section -->
        <div style="padding: 0.5rem 1.5rem; background-color: #f9fafb; border-bottom: 1px solid #e5e7eb;">
            <div style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: center;">
                <!-- Search Input -->
                <div style="flex: 1; min-width: 300px;">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" 
                            id="searchInput" 
                            placeholder="Cari username, email, atau phone..."
                            class="w-full border border-gray-300 rounded-lg pl-10 pr-3 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                            style="font-size: 0.875rem;">
                    </div>
                </div>

                <!-- Filter Role -->
                <select id="filterRole" class="border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all" style="font-size: 0.875rem;">
                    <option value="">Semua Role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->role_id }}">{{ $role->role_name }}</option>
                    @endforeach
                </select>

                <!-- Filter Status -->
                <select id="filterStatus" class="border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all" style="font-size: 0.875rem;">
                    <option value="">Semua Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div>

        <!-- Table Section - NO PADDING! -->
        <x-settingsm.user.utable :users="$users" :currentMenuId="$currentMenuId" />

        <!-- Pagination -->
        @if($users->hasPages())
        <div style="border-top: 1px solid #e5e7eb; background-color: #f9fafb;">
            <x-globals.pagination :paginator="$users" />
        </div>
        @endif
    </div>
</div>

<!-- Modals -->
<x-settingsm.user.uform :roles="$roles" :provinces="$provinces" />
<x-settingsm.user.uedit :roles="$roles" :provinces="$provinces" />

@push('scripts')
<script src="{{ asset('js/address-cascade.js') }}"></script>
<script src="{{ asset('js/global-toast.js') }}"></script>
<script src="{{ asset('js/user-modal.js') }}"></script>
<script src="{{ asset('js/search.js') }}"></script>
@endpush

<style>
    /* Hover effects for buttons */
    button:hover, a[href]:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    /* Focus styles */
    #searchInput:focus,
    #filterRole:focus,
    #filterStatus:focus {
        outline: none;
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        button span, a span {
            display: none;
        }
    }
</style>

<script>
    window.Laravel = {!! json_encode(['csrfToken' => csrf_token()]) !!};

    // Function untuk delete user
    function deleteUser(userId, deleteRoute, csrfToken) {
        console.log('deleteUser called:', {userId, deleteRoute, csrfToken});
        
        deleteRecord(userId, deleteRoute, csrfToken, (data) => {
            console.log('Delete success:', data);
            if (window.userTableHandler) {
                console.log('Refreshing table...');
                window.userTableHandler.refresh();
            } else {
                console.warn('userTableHandler not found, reloading page');
                location.reload();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        console.log('🚀 User Management page loaded');
        
        if (typeof TableHandler === 'undefined') {
            console.error('❌ TableHandler class not found. search.js may not be loaded.');
            return;
        }

        console.log('✅ Creating TableHandler instance...');
        
        try {
            window.userTableHandler = new TableHandler({
                tableId: 'userTable',
                ajaxUrl: '{{ route("users.search") }}',
                filters: ['role', 'status'],
                columns: ['number', 'user', 'phone', 'date_birth', 'alamat', 'role', 'status', 'actions'],
                searchParam: 'q'
            });
            
            console.log('✅ TableHandler initialized successfully');
        } catch (error) {
            console.error('❌ Error initializing TableHandler:', error);
        }

        // Initialize cascade untuk CREATE form
        if (typeof AddressCascade !== 'undefined') {
            const createCascade = new AddressCascade({
                provinceId: 'create-province',
                regencyId: 'create-regency',
                districtId: 'create-district',
                villageId: 'create-village'
            });
        }
    });
</script>
@endsection