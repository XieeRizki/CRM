// ==================== ADD MODAL ====================
let createVisitCascade = null;

function openVisitModal() {
    const modal = document.getElementById('visitModal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Initialize address cascade untuk CREATE modal
    initCreateVisitCascade();
    
    setTimeout(() => {
        const firstInput = modal.querySelector('select[name="sales_id"]');
        if (firstInput && !firstInput.disabled) firstInput.focus();
    }, 300);
}

function initCreateVisitCascade() {
    // Destroy existing instance if any
    if (createVisitCascade) {
        createVisitCascade.destroy();
    }

    // Initialize new cascade instance untuk CREATE modal
    createVisitCascade = new AddressCascade({
        provinceId: 'create-province',
        regencyId: 'create-regency',
        districtId: 'create-district',
        villageId: 'create-village',
        baseUrl: '/salesvisit'
    });
}

function closeVisitModal() {
    const modal = document.getElementById('visitModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
    
    // Destroy cascade instance
    if (createVisitCascade) {
        createVisitCascade.destroy();
        createVisitCascade = null;
    }
    
    const form = modal.querySelector('form');
    if (form) form.reset();
    
    // Reset dropdowns
    document.getElementById('create-regency').innerHTML = '<option value="">Pilih Kabupaten/Kota</option>';
    document.getElementById('create-district').innerHTML = '<option value="">Pilih Kecamatan</option>';
    document.getElementById('create-village').innerHTML = '<option value="">Pilih Kelurahan/Desa</option>';
}

// ==================== EDIT MODAL ====================
let editVisitCascade = null;
let currentEditData = null;

function openEditVisitModalFromButton(button) {
    const visitData = {
        id: button.getAttribute('data-visit-id'),
        salesId: button.getAttribute('data-sales-id'),
        customerName: button.getAttribute('data-customer-name'),
        company: button.getAttribute('data-company'),
        provinceId: button.getAttribute('data-province-id'),
        regencyId: button.getAttribute('data-regency-id') || null,
        districtId: button.getAttribute('data-district-id') || null,
        villageId: button.getAttribute('data-village-id') || null,
        address: button.getAttribute('data-address'),
        visitDate: button.getAttribute('data-visit-date'),
        purpose: button.getAttribute('data-purpose'),
        followUp: parseInt(button.getAttribute('data-follow-up'))
    };
    
    openEditVisitModal(visitData);
}

function openEditVisitModal(visitData) {
    console.log('Opening Edit Visit Modal:', visitData);

    // Validasi data
    if (!visitData || !visitData.id) {
        console.error('Invalid visit data:', visitData);
        showNotification('Data kunjungan tidak valid', 'error');
        return;
    }

    try {
        // Convert string numbers to actual numbers
        visitData.id = parseInt(visitData.id);
        visitData.salesId = parseInt(visitData.salesId);
        visitData.provinceId = parseInt(visitData.provinceId);
        visitData.regencyId = visitData.regencyId ? parseInt(visitData.regencyId) : null;
        visitData.districtId = visitData.districtId ? parseInt(visitData.districtId) : null;
        visitData.villageId = visitData.villageId ? parseInt(visitData.villageId) : null;
        visitData.followUp = parseInt(visitData.followUp);

        // Show modal
        const modal = document.getElementById('editVisitModal');
        if (!modal) {
            console.error('Edit modal element not found');
            return;
        }
        
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        // Set form action
        const form = document.getElementById('editVisitForm');
        if (form) {
            form.action = `/salesvisit/${visitData.id}`;
        }

        // Set basic fields dengan null checking
        document.getElementById('editVisitId').value = visitData.id || '';
        document.getElementById('editCustomerName').value = visitData.customerName || '';
        document.getElementById('editCompany').value = visitData.company || '';
        document.getElementById('editAddress').value = visitData.address || '';
        document.getElementById('editVisitDate').value = visitData.visitDate || '';
        document.getElementById('editPurpose').value = visitData.purpose || '';
        
        // Set follow up radio buttons
        if (visitData.followUp == 1) {
            document.getElementById('editFollowUpYes').checked = true;
        } else {
            document.getElementById('editFollowUpNo').checked = true;
        }

        // Store current data dengan default values
        currentEditData = {
            salesId: visitData.salesId || '',
            provinceId: visitData.provinceId || '',
            regencyId: visitData.regencyId || null,
            districtId: visitData.districtId || null,
            villageId: visitData.villageId || null
        };

        console.log('Current edit data:', currentEditData);

        // Load data via AJAX untuk mendapatkan sales users dan provinces
        loadEditVisitData(visitData.id);
    } catch (error) {
        console.error('Error opening edit modal:', error);
        showNotification('Gagal membuka modal edit: ' + error.message, 'error');
    }
}

function loadEditVisitData(visitId) {
    console.log('Loading edit data for visit:', visitId);
    
    // Show loading state
    const salesSelect = document.getElementById('editSalesId');
    salesSelect.innerHTML = '<option value="">Loading...</option>';
    salesSelect.disabled = true;

    // Load sales users dan provinces via AJAX
    Promise.all([
        fetch('/users/search?role=sales').then(r => r.json()),
        fetch('/salesvisit/get-provinces').then(r => r.json())
    ]).then(([salesData, provincesData]) => {
        console.log('Sales data:', salesData);
        console.log('Provinces data:', provincesData);

        // Populate sales dropdown
        const salesSelect = document.getElementById('editSalesId');
        salesSelect.innerHTML = '<option value="">Pilih Sales</option>';
        
        if (salesData && salesData.users) {
            salesData.users.forEach(sales => {
                const option = document.createElement('option');
                option.value = sales.user_id;
                option.textContent = `${sales.username} - ${sales.email}`;
                if (sales.user_id == currentEditData.salesId) {
                    option.selected = true;
                }
                salesSelect.appendChild(option);
            });
        }

        // Populate provinces dropdown
        const provinceSelect = document.getElementById('edit-province');
        provinceSelect.innerHTML = '<option value="">Pilih Provinsi</option>';
        
        if (provincesData && provincesData.provinces) {
            provincesData.provinces.forEach(province => {
                const option = document.createElement('option');
                option.value = province.id;
                option.textContent = province.name;
                if (province.id == currentEditData.provinceId) {
                    option.selected = true;
                }
                provinceSelect.appendChild(option);
            });
        }

        salesSelect.disabled = false;
        
        // Initialize cascade setelah data loaded
        initEditVisitCascade();
        
    }).catch(error => {
        console.error('Error loading edit data:', error);
        showNotification('Gagal memuat data edit', 'error');
        
        // Enable select anyway
        const salesSelect = document.getElementById('editSalesId');
        salesSelect.disabled = false;
        salesSelect.innerHTML = '<option value="">Error loading data</option>';
        
        initEditVisitCascade();
    });
}

function initEditVisitCascade() {
    if (editVisitCascade) {
        editVisitCascade.destroy();
    }

    editVisitCascade = new AddressCascade({
        provinceId: 'edit-province',
        regencyId: 'edit-regency',
        districtId: 'edit-district',
        villageId: 'edit-village',
        baseUrl: '/salesvisit'
    });

    if (currentEditData.provinceId) {
        const provinceSelect = document.getElementById('edit-province');
        provinceSelect.value = currentEditData.provinceId;
        
        const changeEvent = new Event('change');
        provinceSelect.dispatchEvent(changeEvent);
        
        // Chain the cascade selections dengan delay
        setTimeout(() => {
            if (currentEditData.regencyId) {
                const regencySelect = document.getElementById('edit-regency');
                regencySelect.value = currentEditData.regencyId;
                regencySelect.dispatchEvent(new Event('change'));
                
                setTimeout(() => {
                    if (currentEditData.districtId) {
                        const districtSelect = document.getElementById('edit-district');
                        districtSelect.value = currentEditData.districtId;
                        districtSelect.dispatchEvent(new Event('change'));
                        
                        setTimeout(() => {
                            if (currentEditData.villageId) {
                                const villageSelect = document.getElementById('edit-village');
                                villageSelect.value = currentEditData.villageId;
                            }
                        }, 500);
                    }
                }, 500);
            }
        }, 500);
    }
}

function closeEditVisitModal() {
    const modal = document.getElementById('editVisitModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';

    if (editVisitCascade) {
        editVisitCascade.destroy();
        editVisitCascade = null;
    }

    currentEditData = null;

    const form = document.getElementById('editVisitForm');
    if (form) form.reset();

    // Reset sales dropdown & re-enable if needed
    const salesSelect = document.getElementById('editSalesId');
    salesSelect.disabled = false;
    salesSelect.classList.remove('bg-gray-100', 'cursor-not-allowed');
    salesSelect.innerHTML = '<option value="">-- Pilih Sales --</option>';

    document.getElementById('edit-province').innerHTML = '<option value="">-- Pilih Provinsi --</option>';
    document.getElementById('edit-regency').innerHTML = '<option value="">-- Pilih Kabupaten/Kota --</option>';
    document.getElementById('edit-district').innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
    document.getElementById('edit-village').innerHTML = '<option value="">-- Pilih Kelurahan/Desa --</option>';
}

// ==================== DELETE ====================
function deleteVisit(id, deleteUrl, csrfToken) {
    if (confirm('Apakah Anda yakin ingin menghapus data kunjungan ini?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = deleteUrl;

        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken;

        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';

        form.appendChild(csrfInput);
        form.appendChild(methodInput);
        document.body.appendChild(form);
        form.submit();
    }
}

// ==================== NOTIFICATION SYSTEM ====================
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-[60] p-4 rounded-lg shadow-lg text-white transform transition-all duration-300 translate-x-full`;
    
    const bgColor = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        info: 'bg-blue-500'
    };
    
    notification.classList.add(bgColor[type]);
    notification.innerHTML = `
        <div class="flex items-center gap-2">
            <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'times' : 'info'}-circle"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// ==================== EVENT LISTENERS ====================
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        if (!document.getElementById('visitModal').classList.contains('hidden')) {
            closeVisitModal();
        }
        if (!document.getElementById('editVisitModal').classList.contains('hidden')) {
            closeEditVisitModal();
        }
    }
});

document.addEventListener('click', (e) => {
    if (e.target.id === 'visitModal') {
        closeVisitModal();
    }
    if (e.target.id === 'editVisitModal') {
        closeEditVisitModal();
    }
});

// ==================== INIT ON PAGE LOAD ====================
document.addEventListener('DOMContentLoaded', () => {
    console.log('SalesVisit Modal JS Loaded');
    
    // Check if AddressCascade is available
    if (typeof AddressCascade === 'undefined') {
        console.error('AddressCascade class not found! Make sure address-cascade.js is loaded.');
    }
});