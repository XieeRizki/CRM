@props([
    'currentUser' => auth()->user(),
    'salesList'   => [],
])

<div class="bg-white rounded-xl shadow-lg p-6 card-hover fade-in">

    <!-- Hidden Data -->
    <input type="hidden" id="currentUserId" value="{{ $currentUser?->user_id }}">
    <input type="hidden" id="currentUserRole" value="{{ $currentUser?->role_id }}">

    <!-- HEADER -->
    <div class="flex flex-col gap-4 mb-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">
                @if($currentUser?->role_id == 1)
                    Trend Kunjungan Semua Sales
                @else
                    Trend Kunjungan Saya
                @endif
            </h3>

            <p class="text-sm text-gray-500 mt-1">
                @if($currentUser?->role_id == 1)
                    Data keseluruhan kunjungan tim sales
                @else
                    Data kunjungan {{ $currentUser?->username ?? 'Sales' }}
                @endif
            </p>
        </div>

        <!-- FILTER BAR -->
        <div class="flex flex-wrap items-center gap-2">

            <button data-trend-period="daily"
                class="px-3 py-1.5 text-xs rounded-lg border bg-white text-gray-700 hover:bg-blue-500 hover:text-white">
                Per Hari
            </button>

            <button data-trend-period="monthly"
                class="px-3 py-1.5 text-xs rounded-lg border bg-blue-500 text-white border-blue-500">
                Per Bulan
            </button>

            <button data-trend-period="yearly"
                class="px-3 py-1.5 text-xs rounded-lg border bg-white text-gray-700 hover:bg-blue-500 hover:text-white">
                Per Tahun
            </button>

            <div class="h-6 w-px bg-gray-300 mx-1"></div>

            <!-- Custom Range Button -->
            <button id="customRangeBtn"
                class="px-3 py-1.5 text-xs rounded-lg border bg-white text-gray-700 hover:bg-purple-400 hover:text-white flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Custom Range
            </button>

            <!-- SUPERADMIN SALES DROPDOWN -->
            @if($currentUser?->role_id == 1)
            <div class="ml-3">
                <label class="font-semibold text-xs">Filter Sales:</label>
                <select id="salesFilter" class="border px-2 py-1 rounded text-xs">
                    <option value="">SEMUA SALES</option>

                    @foreach($salesList as $s)
                        <option value="{{ $s->user_id }}">{{ $s->username }}</option>
                    @endforeach
                </select>
            </div>
            @endif

        </div>

        <!-- DATE RANGE PICKER -->
        <div id="dateRangePicker" class="hidden bg-gray-50 border rounded-lg p-3 mt-3">
            <div class="flex flex-wrap items-end gap-2">

                <div class="flex-1 min-w-[140px]">
                    <label class="text-xs">Start Date</label>
                    <input type="date" id="startDate" class="w-full px-2 py-1.5 text-xs border rounded-lg">
                </div>

                <div class="flex-1 min-w-[140px]">
                    <label class="text-xs">End Date</label>
                    <input type="date" id="endDate" class="w-full px-2 py-1.5 text-xs border rounded-lg">
                </div>

                <button id="applyDateRange"
                    class="px-3 py-1.5 text-xs bg-purple-500 text-white rounded-lg">
                    Apply
                </button>

                <button id="cancelDateRange"
                    class="px-3 py-1.5 text-xs bg-gray-200 text-gray-700 rounded-lg">
                    Cancel
                </button>
            </div>
        </div>
    </div>

    <!-- CHART -->
    <div class="relative h-80">
        <div id="visitTrendLoading"
            class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-75 hidden">
            <div class="text-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto mb-2"></div>
                <p class="text-sm text-gray-600">Loading data...</p>
            </div>
        </div>

        <canvas id="visitTrend"></canvas>
    </div>

    <!-- STAT -->
    <div class="mt-6 grid grid-cols-2 gap-4 text-center">

        <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
            <div class="text-xs text-gray-600 mb-2">Total Kunjungan</div>
            <div class="text-3xl font-bold text-blue-600" id="totalVisits">0</div>
        </div>

        <div class="bg-green-50 rounded-lg p-4 border border-green-100">
            <div class="text-xs text-gray-600 mb-2">Rata-rata</div>
            <div class="text-xl font-bold text-green-600" id="averageVisits">0</div>
        </div>

    </div>

    <div class="mt-4 text-center text-xs text-gray-500">
        @if($currentUser?->role_id == 1)
            Grafik menunjukkan jumlah kunjungan semua sales per periode
            <p class="text-blue-500 mt-1">* Data mencakup seluruh tim sales</p>
        @else
            Grafik menunjukkan jumlah kunjungan Anda per periode
            <p class="text-blue-500 mt-1">* Hanya menampilkan data kunjungan Anda</p>
        @endif
    </div>

</div>

<style>
    .card-hover {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .card-hover:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }
    .fade-in {
        animation: fadeIn 0.5s ease-in;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }
</style>