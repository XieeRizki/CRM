<!-- Modal Edit Company -->
<div id="editCompanyModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden animate-modal-in">
        <!-- Header -->
        <div style="background: linear-gradient(to right, #4f46e5, #7c3aed); padding: 1.25rem 1.5rem;">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-xl font-semibold text-white">Edit Perusahaan</h3>
                    <p class="text-sm text-indigo-100 mt-1">Update informasi perusahaan</p>
                </div>
                <button onclick="closeEditCompanyModal()" 
                    class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-2 transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>

        <!-- Body -->
        <div class="overflow-y-auto max-h-[calc(90vh-140px)]" style="background-color: #f3f4f6; padding: 1.5rem;">
            <form id="editCompanyForm" method="POST" style="display: flex; flex-direction: column; gap: 1rem;">
                @csrf
                @method('PUT')
                
                <input type="hidden" id="edit_company_id" name="company_id">
                
                <!-- Nama -->
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                        Nama Perusahaan <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text" 
                        id="edit_company_name"
                        name="company_name" 
                        style="width: 100%; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.75rem 1rem; font-size: 0.875rem;" 
                        required>
                </div>
                
                <!-- Jenis -->
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                        Jenis Perusahaan <span style="color: #ef4444;">*</span>
                    </label>
                    <select id="edit_company_type_id"
                            name="company_type_id" 
                            style="width: 100%; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.75rem 1rem; font-size: 0.875rem;" 
                            required>
                        <option value="">-- Pilih Jenis --</option>
                        @foreach($types as $type)
                        <option value="{{ $type->company_type_id }}">{{ $type->type_name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Tier -->
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                        Tier
                    </label>
                    <select id="edit_tier"
                            name="tier" 
                            style="width: 100%; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.75rem 1rem; font-size: 0.875rem;">
                        <option value="">-- Pilih Tier --</option>
                        <option value="A">Tier A</option>
                        <option value="B">Tier B</option>
                        <option value="C">Tier C</option>
                        <option value="D">Tier D</option>
                    </select>
                </div>
                
                <!-- Deskripsi -->
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                        Deskripsi
                    </label>
                    <textarea id="edit_description"
                              name="description" 
                              rows="3" 
                              style="width: 100%; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.75rem 1rem; font-size: 0.875rem; resize: none;" 
                              placeholder="Tambahkan keterangan tentang perusahaan..."></textarea>
                </div>
                
                <!-- Status -->
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                        Status
                    </label>
                    <select id="edit_status"
                            name="status" 
                            style="width: 100%; background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.75rem 1rem; font-size: 0.875rem;">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                
                <!-- Tombol -->
                <div style="display: flex; justify-content: flex-end; gap: 0.75rem; padding-top: 1rem; border-top: 1px solid #e5e7eb; margin-top: 1.5rem;">
                    <button type="button" 
                            onclick="closeEditCompanyModal()" 
                            style="background-color: #ffffff; border: 1px solid #d1d5db; border-radius: 0.5rem; padding: 0.625rem 1.5rem; font-weight: 500; font-size: 0.875rem; cursor: pointer; transition: all 0.2s;">
                        Batal
                    </button>
                    <button type="submit" 
                            style="background-color: #4f46e5; color: white; border: none; border-radius: 0.5rem; padding: 0.625rem 1.5rem; font-weight: 500; font-size: 0.875rem; cursor: pointer; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1); transition: all 0.2s;">
                        <i class="fas fa-save" style="margin-right: 0.5rem;"></i>
                        Update Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openAddCompanyModal() {
    document.getElementById('addCompanyModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeAddCompanyModal() {
    document.getElementById('addCompanyModal').classList.add('hidden');
    document.getElementById('addCompanyForm').reset();
    document.body.style.overflow = 'auto';
}

function openEditCompanyModal(id, name, typeId, tier, description, status) {
    document.getElementById('edit_company_id').value = id;
    document.getElementById('edit_company_name').value = name;
    document.getElementById('edit_company_type_id').value = typeId;
    document.getElementById('edit_tier').value = tier;
    document.getElementById('edit_description').value = description;
    document.getElementById('edit_status').value = status;
    
    // Set form action
    document.getElementById('editCompanyForm').action = `/companies/${id}`;
    
    document.getElementById('editCompanyModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeEditCompanyModal() {
    document.getElementById('editCompanyModal').classList.add('hidden');
    document.getElementById('editCompanyForm').reset();
    document.body.style.overflow = 'auto';
}
</script>

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
</style>