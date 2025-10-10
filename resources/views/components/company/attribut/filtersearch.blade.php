@props([
    'tableId',
    'types' => [],
    'ajaxUrl' => null,
])

<div class="bg-white rounded-xl shadow-sm p-6 mb-4 border border-gray-200 fade in">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
        <div class="flex flex-wrap items-center gap-4">
            <!-- Search input -->
            <div class="relative">
                <input
                    type="text"
                    id="{{ $tableId }}SearchInput"
                    placeholder="Cari nama perusahaan..."
                    class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none"
                />
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
            </div>

            <!-- Filter Type Perusahaan -->
            <select id="{{ $tableId }}TypeFilter"
                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none">
                <option value="">Semua Type</option>
                @foreach($types as $type)
                    <option value="{{ $type->company_type_id }}">{{ $type->type_name }}</option>
                @endforeach
            </select>

            <!-- Filter Tier -->
            <select id="{{ $tableId }}TierFilter"
                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none">
                <option value="">Semua Tier</option>
                <option value="A">Tier A</option>
                <option value="B">Tier B</option>
                <option value="C">Tier C</option>
                <option value="D">Tier D</option>
            </select>

            <!-- Filter Status -->
            <select id="{{ $tableId }}StatusFilter"
                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none">
                <option value="">Semua Status</option>
                <option value="active">Aktif</option>
                <option value="inactive">Nonaktif</option>
            </select>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tableId = '{{ $tableId }}';
    const ajaxUrl = '{{ $ajaxUrl }}';

    const input = document.getElementById(tableId + 'SearchInput');
    const typeFilter = document.getElementById(tableId + 'TypeFilter');
    const tierFilter = document.getElementById(tableId + 'TierFilter');
    const statusFilter = document.getElementById(tableId + 'StatusFilter');
    let debounceTimer;

    // Target tbody yang spesifik dengan ID
    const companyTable = document.querySelector('#' + tableId + ' tbody');

    if (!companyTable) {
        console.error('Table tbody tidak ditemukan untuk ID:', tableId);
        return;
    }

    if (!ajaxUrl) {
        console.error('ajaxUrl tidak ditemukan');
        return;
    }

    function fetchData(page = 1) {
        const search = input?.value || '';
        const type = typeFilter?.value || '';
        const tier = tierFilter?.value || '';
        const status = statusFilter?.value || '';

        // Loading state
        companyTable.innerHTML = `<tr><td colspan="7" class="text-center py-6"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>`;

        const query = new URLSearchParams({
            search,
            type,
            tier,
            status,
            page
        });

        fetch(`${ajaxUrl}?${query.toString()}`)
            .then(res => {
                if (!res.ok) throw new Error('Network response was not ok');
                return res.json();
            })
            .then(data => {
                if (data.data && data.meta) {
                    renderTable(data.data, data.meta);
                } else {
                    throw new Error('Format data tidak valid');
                }
            })
            .catch(err => {
                console.error('Error:', err);
                companyTable.innerHTML = `<tr><td colspan="7" class="text-center text-red-600 py-6">Gagal memuat data. Silakan refresh halaman.</td></tr>`;
            });
    }

    function renderTable(companies, meta) {
        if (!companies || companies.length === 0) {
            companyTable.innerHTML = `<tr><td colspan="7" class="text-center py-6 text-gray-500">Tidak ada data ditemukan.</td></tr>`;
            return;
        }

        let rows = '';
        companies.forEach((company, index) => {
            const rowNum = (meta.from ?? 1) + index; // nomor urut sesuai pagination Laravel

            // Tentukan warna status
            let statusClass = company.status === 'active' 
                ? 'bg-green-100 text-green-800' 
                : 'bg-gray-100 text-gray-800';

            // Display tier dengan format "Tier X"
            const tierDisplay = company.tier ? `Tier ${company.tier}` : '-';

            // Escape string untuk keamanan
            const companyName = String(company.company_name || '').replace(/'/g, "\\'").replace(/"/g, '&quot;');
            const description = String(company.description || '').replace(/`/g, '\\`').replace(/\$/g, '\\$');

            rows += `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm text-gray-900">${rowNum}</td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">${company.company_name || '-'}</td>
                    <td class="px-6 py-4 text-sm text-gray-900">${company.company_type?.type_name || '-'}</td>
                    <td class="px-6 py-4 text-sm text-gray-900">${tierDisplay}</td>
                    <td class="px-6 py-4 text-sm text-gray-900">${company.description || '-'}</td>
                    <td class="px-6 py-4 text-sm">
                        <span class="px-3 py-1 rounded-full text-xs font-medium ${statusClass}">
                            ${company.status ? company.status.charAt(0).toUpperCase() + company.status.slice(1) : '-'}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm font-medium">
                        <div class="flex items-center space-x-2">
                            <button onclick="openEditCompanyModal('${company.company_id}', '${companyName}', '${company.company_type_id || ''}', '${company.tier || ''}', \`${description}\`, '${company.status}')" 
                                class="text-blue-600 hover:text-blue-900 p-2 rounded-lg hover:bg-blue-50 flex items-center" 
                                title="Edit Company">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="/companies/${company.company_id}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus perusahaan ini?')">
                                <input type="hidden" name="_token" value="${document.querySelector('meta[name=csrf-token]')?.content || ''}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="text-red-600 hover:text-red-900 p-2 flex items-center" title="Hapus Perusahaan">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            `;
        });

        companyTable.innerHTML = rows;
    }

    // Events
    input?.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => fetchData(1), 400);
    });

    typeFilter?.addEventListener('change', () => fetchData(1));
    tierFilter?.addEventListener('change', () => fetchData(1));
    statusFilter?.addEventListener('change', () => fetchData(1));

    // Load initial data
    fetchData(1);
});
</script>
