@extends('layout.main')

@section('content')
@php
    $can = fn ($aksi) => $currentMenuId ? auth()->user()->canAccess($currentMenuId, $aksi) : true;
    $badge = [
        'Menunggu'  => 'bg-amber-100 text-amber-800',
        'Disetujui' => 'bg-green-100 text-green-700',
        'Ditolak'   => 'bg-red-100 text-red-700',
    ];
    $adaFilter = $search !== '' || $status !== '';
@endphp

<div class="mt-12 p-4">
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm">

        {{-- Judul + tombol --}}
        <div class="flex flex-wrap items-center justify-between gap-3 p-4">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg border border-gray-300 text-gray-800">
                    <i class="fas fa-file-lines text-lg"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900">List RAB</h2>
                    <p class="text-xs text-gray-500">Data pengajuan RAB perjalanan</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" onclick="openPdf()"
                    class="inline-flex items-center gap-2 rounded-lg border border-indigo-600 px-4 py-2 text-xs font-semibold text-indigo-700 hover:bg-indigo-50">
                    <i class="fas fa-file-pdf"></i> Unduh PDF
                </button>
                @if ($can('create'))
                    <a href="{{ route('rab.create') }}"
                        class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-700">
                        <i class="fas fa-plus"></i> Tambah RAB
                    </a>
                @endif
            </div>
        </div>

        {{-- Pencarian + filter --}}
        <form method="GET" action="{{ route('rab') }}" class="flex flex-wrap items-center gap-2 px-4 pb-4">
            <div class="relative min-w-[220px] flex-1">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari ID RAB, tujuan, atau keperluan..."
                    class="w-full rounded-lg border border-gray-300 py-2 pl-9 pr-3 text-xs focus:border-indigo-500 focus:outline-none">
            </div>
            <select name="status" onchange="this.form.submit()"
                class="rounded-lg border border-gray-300 px-3 py-2 text-xs focus:border-indigo-500 focus:outline-none">
                <option value="">Semua status</option>
                @foreach ($statuses as $st)
                    <option value="{{ $st }}" @selected($status === $st)>{{ $st }}</option>
                @endforeach
            </select>
            <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-xs font-medium text-white hover:bg-indigo-700">Cari</button>
            @if ($adaFilter)
                <a href="{{ route('rab') }}" class="text-xs text-gray-600 underline">Reset</a>
            @endif
        </form>

        {{-- Tabel --}}
        <div class="overflow-x-auto px-4">
            <table class="w-full text-xs">
                <thead class="bg-indigo-50 text-gray-700">
                    <tr>
                        <th class="w-8 px-3 py-3 text-left">
                            <input type="checkbox" id="checkAll" onclick="toggleAll(this)" title="Pilih semua di halaman ini">
                        </th>
                        <th class="px-3 py-3 text-left font-semibold">No</th>
                        <th class="px-3 py-3 text-left font-semibold">ID Transaksi</th>
                        <th class="px-3 py-3 text-left font-semibold">ID RAB</th>
                        <th class="px-3 py-3 text-left font-semibold">Tujuan</th>
                        <th class="px-3 py-3 text-left font-semibold">Keperluan</th>
                        <th class="px-3 py-3 text-left font-semibold">Tanggal Berangkat</th>
                        <th class="px-3 py-3 text-left font-semibold">Tanggal Pulang</th>
                        <th class="px-3 py-3 text-left font-semibold">Jumlah</th>
                        <th class="px-3 py-3 text-left font-semibold">Status</th>
                        <th class="px-3 py-3 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rabs as $i => $r)
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="px-3 py-3">
                                <input type="checkbox" class="row-check" value="{{ $r->id }}" onchange="updateSelection()">
                            </td>
                            <td class="px-3 py-3">{{ $rabs->firstItem() + $i }}</td>
                            <td class="px-3 py-3 font-semibold text-gray-900">{{ $r->kode_transaksi }}</td>
                            <td class="px-3 py-3 font-semibold text-gray-900">{{ $r->kode_rab }}</td>
                            <td class="px-3 py-3">{{ $r->lokasi_tujuan }}</td>
                            <td class="px-3 py-3">{{ \Illuminate\Support\Str::limit($r->keperluan, 40) }}</td>
                            <td class="px-3 py-3">{{ optional($r->tanggal_berangkat)->format('d/m/Y') }}</td>
                            <td class="px-3 py-3">{{ optional($r->tanggal_pulang)->format('d/m/Y') }}</td>
                            <td class="px-3 py-3 font-semibold">Rp {{ number_format($r->total_anggaran, 0, ',', '.') }}</td>
                            <td class="px-3 py-3">
                                <span class="inline-block rounded-full px-2.5 py-0.5 text-[11px] font-semibold {{ $badge[$r->status] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ $r->status }}
                                </span>
                            </td>
                            <td class="px-3 py-3">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('rab.show', $r->id) }}" title="Lihat"
                                        class="flex h-7 w-7 items-center justify-center rounded-md bg-indigo-600 text-white hover:bg-indigo-700">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if ($can('edit'))
                                        <a href="{{ route('rab.edit', $r->id) }}" title="Edit"
                                            class="flex h-7 w-7 items-center justify-center rounded-md bg-amber-400 text-white hover:bg-amber-500">
                                            <i class="fas fa-pen-to-square"></i>
                                        </a>
                                    @endif
                                    <a href="{{ route('rab.pdf', $r->id) }}" title="Unduh PDF (surat pengajuan)"
                                        class="flex h-7 w-7 items-center justify-center rounded-md bg-slate-600 text-white hover:bg-slate-700">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                    @if ($can('delete'))
                                        <form method="POST" action="{{ route('rab.destroy', $r->id) }}" onsubmit="return confirmDelete(event, this)">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Hapus"
                                                class="flex h-7 w-7 items-center justify-center rounded-md bg-red-500 text-white hover:bg-red-600">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="px-3 py-10 text-center text-gray-500">
                                {{ $adaFilter ? 'Tidak ada RAB yang cocok dengan pencarian.' : 'Belum ada RAB. Klik "Tambah RAB" untuk membuat.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Info jumlah data + pagination --}}
        <div class="flex flex-wrap items-center justify-between gap-3 p-4 text-xs text-gray-600">
            <p>
                @if ($rabs->total() > 0)
                    Menampilkan {{ $rabs->firstItem() }} - {{ $rabs->lastItem() }} dari {{ $rabs->total() }} data
                @else
                    Menampilkan 0 data
                @endif
                &nbsp;|&nbsp; Dicentang: <b id="selCount">0</b>
            </p>

            <div class="flex items-center gap-1">
                @if ($rabs->onFirstPage())
                    <span class="rounded-md border border-gray-200 px-2.5 py-1 text-gray-300">&lsaquo;</span>
                @else
                    <a href="{{ $rabs->previousPageUrl() }}" class="rounded-md border border-gray-300 px-2.5 py-1 text-gray-600 hover:bg-gray-50">&lsaquo;</a>
                @endif

                @foreach ($rabs->getUrlRange(max(1, $rabs->currentPage() - 2), min($rabs->lastPage(), $rabs->currentPage() + 2)) as $page => $url)
                    <a href="{{ $url }}"
                        class="rounded-md px-2.5 py-1 {{ $page == $rabs->currentPage() ? 'bg-indigo-600 text-white' : 'border border-gray-300 text-gray-600 hover:bg-gray-50' }}">{{ $page }}</a>
                @endforeach

                @if ($rabs->hasMorePages())
                    <a href="{{ $rabs->nextPageUrl() }}" class="rounded-md border border-gray-300 px-2.5 py-1 text-gray-600 hover:bg-gray-50">&rsaquo;</a>
                @else
                    <span class="rounded-md border border-gray-200 px-2.5 py-1 text-gray-300">&rsaquo;</span>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ================= POPUP UNDUH PDF ================= --}}
<div id="pdfModal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/50 p-4">
    <div class="w-full max-w-md rounded-xl bg-white p-5 shadow-xl">
        <h3 class="text-base font-bold text-gray-900">Unduh PDF</h3>

        <form id="pdfForm" method="GET" action="{{ route('rab.pdf.list') }}" class="mt-3 space-y-4 text-xs" onsubmit="setTimeout(closePdf, 300)">
            <input type="hidden" name="search" value="{{ $search }}">
            <input type="hidden" name="status" value="{{ $status }}">
            <div id="pdfIds"></div>

            <fieldset class="space-y-2">
                <legend class="mb-1 font-semibold text-gray-700">Pilih RAB</legend>
                <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-gray-200 p-2.5">
                    <input type="radio" name="scope" value="pilih" id="scopePilih">
                    <span>Yang dicentang (<b id="pdfCount">0</b> RAB)</span>
                </label>
                <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-gray-200 p-2.5">
                    <input type="radio" name="scope" value="semua" id="scopeSemua" checked>
                    <span>Semua RAB ({{ $rabs->total() }} data{{ $adaFilter ? ', sesuai pencarian / filter yang aktif' : '' }})</span>
                </label>
                <p class="text-[11px] text-gray-500">Centang berlaku untuk halaman yang sedang dibuka. Untuk semua data, pilih "Semua RAB".</p>
            </fieldset>

            <fieldset class="space-y-2">
                <legend class="mb-1 font-semibold text-gray-700">Isi PDF</legend>
                <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-gray-200 p-2.5">
                    <input type="radio" name="format" value="rekap" checked>
                    <span>Rekap tabel (satu daftar ringkas)</span>
                </label>
                <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-gray-200 p-2.5">
                    <input type="radio" name="format" value="surat">
                    <span>Surat pengajuan lengkap (satu halaman per RAB)</span>
                </label>
            </fieldset>

            <div class="flex justify-end gap-2 pt-1">
                <button type="button" onclick="closePdf()"
                    class="rounded-md border border-gray-300 px-4 py-2 text-xs font-medium text-gray-700 hover:bg-gray-100">Batal</button>
                <button type="submit"
                    class="rounded-md bg-indigo-600 px-5 py-2 text-xs font-semibold text-white hover:bg-indigo-700">
                    <i class="fas fa-download mr-1"></i> Unduh
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const rowChecks = () => Array.from(document.querySelectorAll('.row-check'));

function updateSelection() {
    const rows = rowChecks();
    const n = rows.filter(c => c.checked).length;
    document.getElementById('selCount').textContent = n;
    const all = document.getElementById('checkAll');
    if (all) all.checked = rows.length > 0 && n === rows.length;
}

function toggleAll(el) {
    rowChecks().forEach(c => c.checked = el.checked);
    updateSelection();
}

function openPdf() {
    const ids = rowChecks().filter(c => c.checked).map(c => c.value);

    document.getElementById('pdfCount').textContent = ids.length;
    document.getElementById('pdfIds').innerHTML = ids.map(id => `<input type="hidden" name="ids[]" value="${id}">`).join('');

    const pilih = document.getElementById('scopePilih');
    const semua = document.getElementById('scopeSemua');
    pilih.disabled = ids.length === 0;
    (ids.length > 0 ? pilih : semua).checked = true;

    const m = document.getElementById('pdfModal');
    m.classList.remove('hidden');
    m.classList.add('flex');
}

function closePdf() {
    const m = document.getElementById('pdfModal');
    m.classList.add('hidden');
    m.classList.remove('flex');
}

document.getElementById('pdfModal').addEventListener('click', e => {
    if (e.target.id === 'pdfModal') closePdf();
});

function confirmDelete(e, form) {
    e.preventDefault();
    Swal.fire({
        title: 'Hapus RAB ini?',
        text: 'RAB akan dihapus dari daftar.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Hapus',
        cancelButtonText: 'Batal'
    }).then(r => { if (r.isConfirmed) form.submit(); });
    return false;
}
</script>
@endsection