@extends('layout.main')

@section('content')
@php
    $readonly = !empty($readonly);          // true = mode lihat (terkunci)
    $isEdit   = !empty($rab);
    $canEdit  = empty($currentMenuId) ? true : auth()->user()->canAccess($currentMenuId, 'edit');

    $statusBadge = [
        'Menunggu'  => 'bg-amber-100 text-amber-800',
        'Disetujui' => 'bg-green-100 text-green-700',
        'Ditolak'   => 'bg-red-100 text-red-700',
    ];

    // Nilai isian: dari input sebelumnya (kalau validasi gagal), atau dari data RAB (kalau edit / lihat)
    $val = fn ($key, $default = '') => old($key, $isEdit ? ($rab->{$key} ?? $default) : $default) ?? $default;

    $dtBerangkat = old('tanggal_berangkat', $isEdit ? optional($rab->tanggal_berangkat)->format('Y-m-d\TH:i') : '');
    $dtPulang    = old('tanggal_pulang', $isEdit ? optional($rab->tanggal_pulang)->format('Y-m-d\TH:i') : '');

    $initialItems = old('items', $isEdit
        ? $rab->items->map(fn ($i) => ['master_item_id' => $i->master_item_id, 'unit' => $i->jumlah_unit, 'kali' => $i->kali_per_hari])->values()->all()
        : []);

    $metodeInit = $val('metode_pembayaran', 'Transfer');
    $cashInit   = $metodeInit === 'Split' ? (float) $val('nominal_cash', 0) : 0;

    $inp = 'w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:border-indigo-500';
    $ro  = 'w-full bg-gray-100 border border-gray-300 rounded-md px-3 py-2 text-gray-500';
@endphp

<style>
    /* Mode lihat: isian terkunci tapi tetap terbaca jelas */
    .rab-view input:disabled, .rab-view textarea:disabled, .rab-view select:disabled {
        background-color: #f9fafb; color: #111827; -webkit-text-fill-color: #111827; opacity: 1; cursor: default;
    }
</style>

<form id="rabForm" method="POST"
    action="{{ $readonly ? '#' : ($isEdit ? route('rab.update', $rab->id) : route('rab.store')) }}"
    class="mt-12 p-4 {{ $readonly ? 'rab-view' : '' }}"
    @if ($readonly) onsubmit="return false" @endif>
    @csrf
    @if ($isEdit && !$readonly)
        @method('PUT')
    @endif

    {{-- Data pembayaran (diisi lewat popup, dikirim lewat input tersembunyi ini) --}}
    <input type="hidden" name="metode_pembayaran" id="hMetode" value="{{ $metodeInit }}">
    <input type="hidden" name="bank_nama" id="hBank" value="{{ $val('bank_nama') }}">
    <input type="hidden" name="no_rekening" id="hRek" value="{{ $val('no_rekening') }}">
    <input type="hidden" name="nama_pemilik_rekening" id="hNama" value="{{ $val('nama_pemilik_rekening') }}">
    <input type="hidden" name="penerima_cash" id="hPenerimaCash" value="{{ $val('penerima_cash') }}">
    <input type="hidden" name="nominal_cash" id="hCash" value="{{ $cashInit ?: '' }}">

    {{-- Kembali ke list --}}
    <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
        <a href="{{ route('rab') }}" class="inline-flex items-center gap-2 text-xs font-medium text-indigo-700 hover:underline">
            <i class="fas fa-arrow-left"></i> Kembali ke List RAB
        </a>
        <p class="text-xs font-semibold text-gray-700">
            @if ($readonly)
                Lihat RAB {{ $rab->kode_rab }} ({{ $rab->kode_transaksi }})
            @elseif ($isEdit)
                Edit RAB {{ $rab->kode_rab }} ({{ $rab->kode_transaksi }})
            @else
                Tambah RAB
            @endif
        </p>
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3 text-xs text-red-800">
            <p class="font-semibold mb-1">RAB belum tersimpan. Periksa isian berikut:</p>
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_340px] gap-4 items-start">

        {{-- ================= KOLOM KIRI ================= --}}
        <div class="space-y-4">

            {{-- Pilih item + tabel item biaya --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div id="itemPicker" class="relative mb-4 {{ $readonly ? 'hidden' : '' }}">
                    <input id="itemSearch" type="text" autocomplete="off"
                        placeholder="Klik untuk pilih atau cari item..."
                        onkeydown="if (event.key === 'Enter') event.preventDefault()"
                        class="w-full border-2 border-indigo-600 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-200">
                    <div id="itemDropdown" class="hidden absolute z-20 mt-1 w-full max-h-60 overflow-y-auto bg-white border border-gray-200 rounded-lg shadow-lg"></div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-xs border-collapse">
                        <thead class="bg-violet-100 text-gray-800">
                            <tr>
                                <th class="border border-violet-300 px-3 py-2 text-left font-semibold">Item biaya</th>
                                <th class="border border-violet-300 px-2 py-2 text-center font-semibold w-24">Unit</th>
                                <th class="border border-violet-300 px-2 py-2 text-center font-semibold w-24">Kali / hari</th>
                                <th class="border border-violet-300 px-2 py-2 text-center font-semibold w-28">Qty<span class="block text-[10px] font-normal text-gray-500">otomatis</span></th>
                                <th class="border border-violet-300 px-2 py-2 text-center font-semibold w-28">Harga</th>
                                <th class="border border-violet-300 px-2 py-2 text-center font-semibold w-40">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody id="itemBody"></tbody>
                    </table>
                    <p id="itemEmpty" class="border border-t-0 border-violet-300 px-3 py-8 text-center text-xs text-gray-500">
                        Belum ada item. Klik kotak pencarian di atas untuk memilih item biaya.
                    </p>
                </div>
                <p class="mt-2 text-[11px] text-gray-500">
                    Qty = Unit × Kali per hari × Durasi perjalanan. Durasi dihitung otomatis dari tanggal &amp; jam berangkat dan pulang.
                </p>
            </div>

            {{-- Data Pemohon + Detail Perjalanan --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <h3 class="text-base font-semibold text-gray-900 mb-3">Data Pemohon</h3>
                    <div class="space-y-3 text-xs">
                        <div>
                            <label class="block text-gray-700 mb-1">Nama <span class="text-red-500">*</span></label>
                            <input type="text" id="namaPemohon" name="nama_pemohon" value="{{ $val('nama_pemohon') }}" required maxlength="100"
                                oninput="renderPay()" placeholder="Nama lengkap pemohon" class="{{ $inp }}">
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-1">Jabatan <span class="text-red-500">*</span></label>
                            <input type="text" id="jabatan" name="jabatan" value="{{ $val('jabatan') }}" required maxlength="100"
                                placeholder="Contoh: Sales Executive" class="{{ $inp }}">
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-1">Telepon <span class="text-red-500">*</span></label>
                            <input type="text" id="telepon" name="telepon" value="{{ $val('telepon') }}" required maxlength="30" inputmode="tel"
                                placeholder="Contoh: 0831-0000-0000" class="{{ $inp }}">
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                            <input type="email" id="email" name="email" value="{{ $val('email') }}" required maxlength="255"
                                placeholder="nama@email.com" class="{{ $inp }}">
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-1">Tanggal &amp; Jam Pengajuan</label>
                            <input type="text" value="{{ $readonly ? optional($rab->tanggal_pengajuan)->format('d/m/Y H:i') : 'otomatis' }}" readonly class="{{ $ro }}">
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <h3 class="text-base font-semibold text-gray-900 mb-3">Detail Perjalanan</h3>
                    <div class="space-y-3 text-xs">
                        <div>
                            <label class="block text-gray-700 mb-1">Lokasi Tujuan <span class="text-red-500">*</span></label>
                            <input type="text" id="lokasiTujuan" name="lokasi_tujuan" value="{{ $val('lokasi_tujuan') }}" required maxlength="255"
                                oninput="renderPay()" class="{{ $inp }}">
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-1">Keperluan <span class="text-red-500">*</span></label>
                            <textarea id="keperluan" name="keperluan" rows="3" required maxlength="1000"
                                placeholder="Contoh : Demo aplikasi ke manajemen rumah sakit"
                                class="{{ $inp }}">{{ $val('keperluan') }}</textarea>
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-1">Tanggal &amp; Jam Berangkat <span class="text-red-500">*</span></label>
                            <input type="datetime-local" id="tanggalBerangkat" name="tanggal_berangkat" value="{{ $dtBerangkat }}" required
                                class="{{ $inp }}">
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-1">Tanggal &amp; Jam Pulang <span class="text-red-500">*</span></label>
                            <input type="datetime-local" id="tanggalPulang" name="tanggal_pulang" value="{{ $dtPulang }}" required
                                class="{{ $inp }}">
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-1">Durasi Perjalanan</label>
                            <div id="durasiInfo" class="rounded-md border border-amber-200 bg-amber-50 px-3 py-2 text-[11px] text-amber-800"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= KOLOM KANAN ================= --}}
        <aside class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 lg:sticky lg:top-20 lg:max-h-[calc(100vh-6rem)] lg:overflow-y-auto">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">Ringkasan biaya</h3>
            <div class="space-y-1 text-xs pb-3">
                <div class="flex justify-between"><span class="text-gray-600">Jumlah item</span><span id="sumItem" class="font-bold">0</span></div>
                <div class="flex justify-between"><span class="text-gray-600">Total qty</span><span id="sumQty" class="font-bold">0</span></div>
                <div class="flex justify-between"><span class="text-gray-600">Durasi</span><span id="sumDurasi" class="font-bold">-</span></div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Status</span>
                    <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $isEdit ? ($statusBadge[$rab->status] ?? 'bg-amber-100 text-amber-800') : 'bg-amber-100 text-amber-800' }}">{{ $isEdit ? $rab->status : 'Menunggu' }}</span>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-3">
                <div class="rounded-lg bg-indigo-700 text-white p-3">
                    <p class="text-[11px] opacity-90">Total Anggaran</p>
                    <p id="sumTotal" class="text-xl font-bold">Rp 0</p>
                    <p id="sumTerbilang" class="text-[11px] italic opacity-90">nol rupiah</p>
                </div>
            </div>

            <div class="mt-4 border-b border-gray-200 pb-4">
                <p class="text-sm font-semibold text-gray-700 mb-2">Metode Pembayaran</p>
                <div class="grid grid-cols-3 gap-2">
                    <button type="button" data-metode="Transfer" onclick="choosePay('Transfer')" class="metode-btn rounded-md border px-2 py-2 text-xs font-medium">Transfer</button>
                    <button type="button" data-metode="Cash" onclick="choosePay('Cash')" class="metode-btn rounded-md border px-2 py-2 text-xs font-medium">Cash</button>
                    <button type="button" data-metode="Split" onclick="choosePay('Split')" class="metode-btn rounded-md border px-2 py-2 text-xs font-medium"><i class="fas fa-right-left mr-1"></i>Split</button>
                </div>

                {{-- Detail Bill (diisi oleh JavaScript) --}}
                <div id="payInfo" class="mt-3"></div>
            </div>

            @if ($readonly)
                <div class="mt-4 space-y-2">
                    @if ($canEdit)
                        <a href="{{ route('rab.edit', $rab->id) }}"
                            class="block w-full rounded-lg bg-amber-400 hover:bg-amber-500 py-3 text-center text-sm font-semibold text-white">
                            <i class="fas fa-pen-to-square mr-1"></i> Edit RAB
                        </a>
                    @endif
                    <a href="{{ route('rab.pdf', $rab->id) }}"
                        class="block w-full rounded-lg border-2 border-indigo-700 py-3 text-center text-sm font-semibold text-indigo-700 hover:bg-indigo-50">
                        <i class="fas fa-file-pdf mr-1"></i> Unduh PDF
                    </a>
                </div>
            @else
                <div class="mt-4 space-y-2">
                    <button type="submit" class="w-full rounded-lg bg-indigo-700 hover:bg-indigo-800 text-white py-3 text-sm font-semibold">{{ $isEdit ? 'Simpan Perubahan' : 'Ajukan RAB' }}</button>
                    <button type="button" onclick="clearAll()" class="w-full rounded-lg border-2 border-indigo-700 text-indigo-700 hover:bg-indigo-50 py-3 text-sm font-semibold">Clear</button>
                </div>
            @endif
        </aside>
    </div>
</form>

{{-- ================= POPUP PEMBAYARAN ================= --}}
<div id="payModal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/50 p-4">
    <div class="w-full max-w-md max-h-[90vh] flex flex-col rounded-xl border-4 border-indigo-600 bg-white">
        <div class="px-5 pt-4">
            <h3 id="payTitle" class="text-base font-bold text-gray-900"></h3>
        </div>

        <div class="px-5 py-3 overflow-y-auto">

            {{-- Langkah: Cash --}}
            <div id="pane-cash" class="hidden space-y-3 text-xs">
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Total Tagihan</label>
                    <input type="text" id="cashTotal" readonly
                        class="w-full bg-gray-100 border border-gray-300 rounded-lg px-3 py-2 text-gray-700">
                    <p class="mt-1 text-[11px] text-gray-500">Otomatis dari sistem. Pembayaran dilakukan tunai sebesar total tagihan.</p>
                </div>
                <div>
                    <label class="block text-gray-700 mb-1">Nama Penerima Cash</label>
                    <input type="text" id="dCashNama" autocomplete="off" oninput="onCashRecvInput()"
                        placeholder="Masukkan nama penerima uang tunai"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-indigo-500">
                </div>
            </div>

            {{-- Langkah: Split Payment --}}
            <div id="pane-split" class="hidden space-y-3 text-xs">
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Total Tagihan</label>
                    <input type="text" id="splitTotal" readonly
                        class="w-full bg-gray-100 border border-gray-300 rounded-lg px-3 py-2 text-gray-700">
                </div>
                <div>
                    <label class="block text-gray-700 mb-1">Pembayaran Cash (Rp)</label>
                    <input type="text" id="splitCash" inputmode="numeric" autocomplete="off" oninput="onCashInput(this)"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-gray-700 mb-1">Pembayaran Transfer (Rp)</label>
                    <input type="text" id="splitTransfer" readonly
                        class="w-full bg-gray-100 border border-gray-300 rounded-lg px-3 py-2 text-gray-700">
                    <p class="mt-1 text-[11px] text-gray-500">Otomatis dari sistem: total tagihan dikurangi cash.</p>
                </div>
                <p id="splitWarn" class="hidden text-[11px] text-red-600">Nominal cash harus lebih kecil dari total tagihan.</p>
            </div>

            {{-- Langkah: Pilih Bank / E-Wallet --}}
            <div id="pane-bank" class="hidden">
                <div id="bankGrid" class="grid grid-cols-4 gap-3"></div>
            </div>

            {{-- Langkah: Data Penerima --}}
            <div id="pane-penerima" class="hidden space-y-3 text-xs">
                <p id="recvBank" class="font-semibold text-indigo-700"></p>
                <div>
                    <label class="block text-gray-700 mb-1">No Rekening / E-Wallet</label>
                    <input type="text" id="dRek" inputmode="numeric" autocomplete="off" oninput="onRecvInput()"
                        placeholder="Masukkan nomor rekening atau e-wallet"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-gray-700 mb-1">Nama Pemilik Rekening</label>
                    <input type="text" id="dNama" autocomplete="off" oninput="onRecvInput()"
                        placeholder="Masukkan nama pemilik rekening"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:border-indigo-500">
                </div>
            </div>
        </div>

        <div class="px-5 pb-4 pt-2 flex items-center justify-between">
            <button type="button" id="payBack" onclick="prevStep()"
                class="rounded-md border border-gray-300 px-4 py-2 text-xs font-medium text-gray-700 hover:bg-gray-100">Batal</button>
            <button type="button" id="payNext" onclick="nextStep()"
                class="rounded-md border px-5 py-2 text-xs font-semibold">Next</button>
        </div>
    </div>
</div>

<script>
const READONLY = @json($readonly);
const master = @json($masterItems);
const banks = @json($banks);
const bankBase = "{{ asset('img/bank') }}";
const logoExts = ['png', 'jpg', 'jpeg', 'webp', 'svg'];
const initialItems = @json($initialItems);
let cart = []; // {id, nama, keterangan, harga, satuan, perHari, unit, kali}

// Data pembayaran yang sudah dikonfirmasi lewat popup
let pay = {
    metode:   @json($metodeInit),
    bank:     @json($val('bank_nama')),
    rek:      @json($val('no_rekening')),
    nama:     @json($val('nama_pemilik_rekening')),
    cashNama: @json($val('penerima_cash')),
    cash:     {{ $cashInit }}
};
let draft = {};      // data sementara di dalam popup
let steps = [];      // urutan langkah popup
let stepIdx = 0;

const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const fmt = n => new Intl.NumberFormat('id-ID').format(Math.round(n || 0));
const rp  = n => 'Rp ' + fmt(n);
const rpH = n => 'Rp. ' + fmt(n);
const nowLocal = () => new Date(Date.now() - new Date().getTimezoneOffset() * 60000).toISOString().slice(0, 16);
const val = id => (document.getElementById(id)?.value || '').trim();

// ---------- Durasi (dari tanggal & jam berangkat dan pulang) ----------
function durasiMenit() {
    const b = val('tanggalBerangkat'), p = val('tanggalPulang');
    if (!b || !p) return null;
    return Math.round((new Date(p) - new Date(b)) / 60000);
}

// Selisih / 24 jam, dibulatkan ke atas, minimal 1 hari
function getHari() {
    const m = durasiMenit();
    return (m && m > 0) ? Math.max(1, Math.ceil(m / 1440)) : 1;
}

function durasiInfo() {
    const m = durasiMenit();
    if (m === null) return { ok: false, text: 'Isi tanggal & jam berangkat dan pulang. Sementara dihitung 1 hari.' };
    if (m <= 0)     return { ok: false, text: 'Tanggal & jam pulang harus setelah tanggal & jam berangkat.' };
    const jam = Math.floor(m / 60), sisa = m % 60;
    return { ok: true, text: getHari() + ' hari (' + jam + ' jam' + (sisa ? ' ' + sisa + ' menit' : '') + ')' };
}

// ---------- Hitung qty & total ----------
const qtyOf    = it => it.unit * (it.perHari ? it.kali * getHari() : 1);
const getTotal = () => cart.reduce((a, c) => a + c.harga * qtyOf(c), 0);
const rumusOf  = it => it.perHari
    ? it.unit + ' ' + (it.satuan || '') + ' × ' + it.kali + ' kali × ' + getHari() + ' hari'
    : it.unit + ' ' + (it.satuan || '');

// ---------- Terbilang (angka -> kata) ----------
function terbilang(n) {
    n = Math.round(Math.abs(n));
    if (n === 0) return 'nol rupiah';
    const s = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];
    const t = x => {
        if (x < 12)   return s[x];
        if (x < 20)   return t(x - 10) + ' belas';
        if (x < 100)  return t(Math.floor(x / 10)) + ' puluh' + (x % 10 ? ' ' + t(x % 10) : '');
        if (x < 200)  return 'seratus' + (x - 100 ? ' ' + t(x - 100) : '');
        if (x < 1000) return t(Math.floor(x / 100)) + ' ratus' + (x % 100 ? ' ' + t(x % 100) : '');
        if (x < 2000) return 'seribu' + (x - 1000 ? ' ' + t(x - 1000) : '');
        if (x < 1e6)  return t(Math.floor(x / 1e3)) + ' ribu' + (x % 1e3 ? ' ' + t(x % 1e3) : '');
        if (x < 1e9)  return t(Math.floor(x / 1e6)) + ' juta' + (x % 1e6 ? ' ' + t(x % 1e6) : '');
        if (x < 1e12) return t(Math.floor(x / 1e9)) + ' miliar' + (x % 1e9 ? ' ' + t(x % 1e9) : '');
        return t(Math.floor(x / 1e12)) + ' triliun' + (x % 1e12 ? ' ' + t(x % 1e12) : '');
    };
    return t(n).trim() + ' rupiah';
}

// ---------- Pilih item ----------
function renderDropdown() {
    const q = document.getElementById('itemSearch').value.trim().toLowerCase();
    const dd = document.getElementById('itemDropdown');
    const list = master.filter(m => (m.nama + ' ' + (m.keterangan || '')).toLowerCase().includes(q));

    dd.innerHTML = list.length
        ? list.map(m => `
            <button type="button" onclick="pickItem(${m.id})" class="w-full text-left px-3 py-2 hover:bg-indigo-50 border-b border-gray-100 last:border-0">
                <span class="block text-xs font-medium text-gray-900">${esc(m.nama)}</span>
                <span class="flex justify-between text-[11px] text-gray-500">
                    <span>${esc(m.keterangan)}</span><span>${rpH(m.harga)}</span>
                </span>
            </button>`).join('')
        : '<p class="px-3 py-3 text-xs text-gray-500">Item tidak ditemukan.</p>';

    dd.classList.remove('hidden');
}

function closeDropdown() { document.getElementById('itemDropdown').classList.add('hidden'); }

function pickItem(id) {
    const m = master.find(x => x.id === id);
    if (!m) return;
    addItem(m);
    document.getElementById('itemSearch').value = '';
    closeDropdown();
}

// Klik item yang sama lagi = unit + 1
function addItem(m, opt = {}) {
    const found = cart.find(c => c.id === m.id);
    if (found) {
        found.unit += 1;
    } else {
        cart.push({
            id: m.id, nama: m.nama, keterangan: m.keterangan, harga: Number(m.harga),
            satuan: m.satuan_unit || '', perHari: !!m.per_hari,
            unit: opt.unit || 1, kali: opt.kali || m.kali_per_hari || 1
        });
    }
    render();
}

function removeItem(i) { cart.splice(i, 1); render(); }

function setField(i, key, v) {
    const n = parseInt(v, 10);
    if (n >= 1) {
        cart[i][key] = Math.min(n, key === 'kali' ? 24 : 10000);
        updateRow(i);
        summary();
    }
}

function fixField(i, key, el) { el.value = cart[i][key]; }

// ---------- Tabel item ----------
function updateRow(i) {
    const it = cart[i];
    const qty = qtyOf(it);
    document.getElementById('qty-' + i).textContent = fmt(qty);
    document.getElementById('rumus-' + i).textContent = it.perHari ? rumusOf(it) : '';
    document.getElementById('sub-' + i).textContent = rp(it.harga * qty);
}

function render() {
    document.getElementById('itemBody').innerHTML = cart.map((it, i) => `
        <tr>
            <td class="border border-violet-300 px-3 py-2">
                <input type="hidden" name="items[${i}][master_item_id]" value="${it.id}">
                <p class="font-semibold text-gray-900">${esc(it.nama)}</p>
                <p class="text-[11px] text-gray-500">${esc(it.keterangan)}</p>
            </td>
            <td class="border border-violet-300 px-2 py-2 text-center">
                <input type="number" min="1" max="10000" step="1" name="items[${i}][unit]" value="${it.unit}"
                    oninput="setField(${i}, 'unit', this.value)" onchange="fixField(${i}, 'unit', this)"
                    class="w-16 border border-violet-300 rounded px-1 py-1 text-center text-xs focus:outline-none focus:border-indigo-500">
                ${it.satuan ? `<p class="mt-0.5 text-[10px] text-gray-500">${esc(it.satuan)}</p>` : ''}
            </td>
            <td class="border border-violet-300 px-2 py-2 text-center">
                ${it.perHari
                    ? `<input type="number" min="1" max="24" step="1" name="items[${i}][kali]" value="${it.kali}"
                            oninput="setField(${i}, 'kali', this.value)" onchange="fixField(${i}, 'kali', this)"
                            class="w-14 border border-violet-300 rounded px-1 py-1 text-center text-xs focus:outline-none focus:border-indigo-500">
                       <p class="mt-0.5 text-[10px] text-gray-500">kali / hari</p>`
                    : `<input type="hidden" name="items[${i}][kali]" value="1"><span class="text-gray-400">—</span>`}
            </td>
            <td class="border border-violet-300 px-2 py-2 text-center">
                <p id="qty-${i}" class="font-semibold text-gray-900"></p>
                <p id="rumus-${i}" class="text-[10px] text-gray-500"></p>
            </td>
            <td class="border border-violet-300 px-2 py-2 text-center">${rpH(it.harga)}</td>
            <td class="border border-violet-300 px-3 py-2">
                <div class="flex items-center justify-between gap-2">
                    <span id="sub-${i}" class="font-semibold"></span>
                    <button type="button" onclick="removeItem(${i})" class="text-gray-700 hover:text-red-600" title="Hapus item"><i class="fas fa-times"></i></button>
                </div>
            </td>
        </tr>`).join('');

    cart.forEach((_, i) => updateRow(i));
    document.getElementById('itemEmpty').classList.toggle('hidden', cart.length > 0);
    summary();
    if (READONLY) lockView();
}

// Mode lihat: kunci semua isian, sembunyikan tombol hapus item
function lockView() {
    document.querySelectorAll('#rabForm input:not([type=hidden]), #rabForm textarea, #rabForm select, .metode-btn')
        .forEach(el => el.disabled = true);
    document.querySelectorAll('#itemBody button').forEach(b => b.classList.add('hidden'));
}

function summary() {
    const tot = getTotal();
    const info = durasiInfo();
    document.getElementById('sumItem').textContent = cart.length;
    document.getElementById('sumQty').textContent = fmt(cart.reduce((a, c) => a + qtyOf(c), 0));
    document.getElementById('sumDurasi').textContent = info.ok ? getHari() + ' hari' : '-';
    document.getElementById('sumTotal').textContent = rp(tot);
    document.getElementById('sumTerbilang').textContent = terbilang(tot);
    renderPay();
}

// Dipanggil tiap tanggal berangkat / pulang berubah
function refreshDates() {
    const info = durasiInfo();
    const box = document.getElementById('durasiInfo');
    box.textContent = info.text;
    box.className = 'rounded-md border px-3 py-2 text-[11px] ' +
        (info.ok ? 'border-indigo-200 bg-indigo-50 text-indigo-800 font-semibold'
                 : 'border-amber-200 bg-amber-50 text-amber-800');
    document.getElementById('tanggalPulang').min = val('tanggalBerangkat');
    cart.forEach((_, i) => updateRow(i));
    summary();
}

// ---------- Pembayaran: status + Detail Bill ----------
function payReady() {
    if (pay.metode === 'Cash') {
        return pay.cashNama.trim() ? { ok: true } : { ok: false, msg: 'Nama penerima cash belum diisi.' };
    }
    if (!pay.bank || !pay.rek.trim() || !pay.nama.trim()) {
        return { ok: false, msg: 'Data rekening / e-wallet penerima belum diisi.' };
    }
    if (pay.metode === 'Split' && !(pay.cash > 0 && pay.cash < getTotal())) {
        return { ok: false, msg: 'Pembagian Cash dan Transfer belum sesuai dengan total tagihan.' };
    }
    return { ok: true };
}

function renderPay() {
    const m = pay.metode;
    const isCash = m === 'Cash';

    // Input tersembunyi yang ikut terkirim ke server
    document.getElementById('hMetode').value       = m;
    document.getElementById('hBank').value         = isCash ? '' : pay.bank;
    document.getElementById('hRek').value          = isCash ? '' : pay.rek;
    document.getElementById('hNama').value         = isCash ? '' : pay.nama;
    document.getElementById('hPenerimaCash').value = isCash ? pay.cashNama : '';
    document.getElementById('hCash').value         = m === 'Split' ? pay.cash : '';

    // Tombol metode
    document.querySelectorAll('.metode-btn').forEach(b => {
        const on = b.dataset.metode === m;
        b.classList.toggle('bg-indigo-700', on);
        b.classList.toggle('text-white', on);
        b.classList.toggle('border-indigo-700', on);
        b.classList.toggle('bg-gray-100', !on);
        b.classList.toggle('text-gray-700', !on);
        b.classList.toggle('border-gray-300', !on);
    });

    document.getElementById('payInfo').innerHTML = billHtml();
}

function billHtml() {
    const m = pay.metode;
    const tot = getTotal();
    const divider = '<div class="my-2 border-t border-dashed border-gray-300"></div>';
    const meta = (label, value) => `
        <div class="flex justify-between gap-3">
            <span>${label}</span>
            <span class="truncate text-right font-medium text-gray-800">${esc(value)}</span>
        </div>`;

    // Tombol "Ubah" tidak ditampilkan di mode lihat
    const ubahBtn = READONLY ? '' :
        `<button type="button" onclick="openPay('${esc(m)}')" class="mt-1 text-[11px] font-medium text-indigo-700 underline">Ubah</button>`;

    // Rincian item
    const rows = cart.length
        ? cart.map(it => `
            <div class="flex items-start justify-between gap-2 py-1.5">
                <div class="min-w-0">
                    <p class="truncate font-medium text-gray-800">${esc(it.nama)}</p>
                    <p class="text-[11px] text-gray-500">${esc(rumusOf(it))}</p>
                    <p class="text-[11px] text-gray-500">${fmt(qtyOf(it))} × ${rpH(it.harga)}</p>
                </div>
                <p class="shrink-0 font-semibold text-gray-900">${rp(it.harga * qtyOf(it))}</p>
            </div>`).join('')
        : '<p class="py-3 text-center text-[11px] text-gray-400">Belum ada item.</p>';

    // Rincian pembayaran
    let paySection;
    if (m === 'Split') {
        const bad = !(pay.cash > 0 && pay.cash < tot);
        paySection = `
            <p class="text-[11px] font-semibold text-gray-600">Cash</p>
            <div class="mb-1 rounded-md border-2 border-indigo-600 py-1.5 text-center font-bold text-gray-900">${rp(pay.cash)}</div>
            <p class="text-[11px] font-semibold text-gray-600">Transfer</p>
            <div class="rounded-md border-2 border-indigo-600 py-1.5 text-center font-bold text-gray-900">${rp(Math.max(tot - pay.cash, 0))}</div>
            ${bad ? '<p class="mt-1 text-[11px] text-red-600">Total berubah atau cash belum diisi. Klik tombol Split untuk mengatur ulang.</p>' : ''}`;
    } else {
        paySection = `
            <div class="flex items-center justify-between">
                <span class="text-gray-600">${esc(m)}</span>
                <span class="font-bold text-gray-900">${rp(tot)}</span>
            </div>`;
    }

    // Data penerima
    const hasBank = pay.bank && pay.rek.trim() && pay.nama.trim();
    const missing = m === 'Cash' ? !pay.cashNama.trim() : !hasBank;
    let recv;
    if (missing) {
        recv = `<p class="rounded-lg border border-amber-200 bg-amber-50 p-2 text-[11px] text-amber-800">
                    Data penerima belum diisi.${READONLY ? '' : ' Klik tombol ' + esc(m) + ' di atas untuk mengisi.'}
                </p>`;
    } else if (m === 'Cash') {
        recv = `
            <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">Penerima</p>
            <p class="font-semibold text-gray-900">${esc(pay.cashNama)}</p>
            ${ubahBtn}`;
    } else {
        recv = `
            <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">Penerima</p>
            <div class="space-y-0.5">
                <p><span class="text-gray-500">Bank / E-Wallet:</span> <b>${esc(pay.bank)}</b></p>
                <p><span class="text-gray-500">No. rekening:</span> <b>${esc(pay.rek)}</b></p>
                <p><span class="text-gray-500">Atas nama:</span> <b>${esc(pay.nama)}</b></p>
            </div>
            ${ubahBtn}`;
    }

    const info = durasiInfo();

    return `
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="flex items-center justify-between bg-indigo-50 px-3 py-2">
            <p class="text-xs font-semibold text-indigo-800"><i class="fas fa-receipt mr-1"></i>Detail Bill</p>
            <span class="rounded-full bg-indigo-700 px-2 py-0.5 text-[10px] font-semibold text-white">${esc(m)}</span>
        </div>
        <div class="p-3 text-xs">
            <div class="space-y-0.5 text-[11px] text-gray-600">
                ${meta('Pemohon', val('namaPemohon') || '-')}
                ${meta('Tujuan', val('lokasiTujuan') || '-')}
                ${meta('Durasi', info.ok ? getHari() + ' hari' : '-')}
            </div>
            ${divider}
            <div class="max-h-48 divide-y divide-gray-100 overflow-y-auto pr-1">${rows}</div>
            ${divider}
            <div class="flex items-center justify-between">
                <span class="font-semibold text-gray-700">Total Tagihan</span>
                <span class="text-sm font-bold text-indigo-700">${rp(tot)}</span>
            </div>
            ${divider}
            <p class="mb-1 text-[11px] font-semibold uppercase tracking-wide text-gray-500">Pembayaran</p>
            ${paySection}
            ${divider}
            ${recv}
        </div>
    </div>`;
}

function choosePay(m) {
    if (READONLY) return;
    openPay(m);
}

// ---------- Popup pembayaran ----------
const STEP_TITLE = { cash: 'Pembayaran Cash', split: 'Split Payment', bank: 'Pilih Bank / E-Wallet', penerima: 'Data Penerima' };
const ALL_PANES = ['cash', 'split', 'bank', 'penerima'];

function openPay(m) {
    if (READONLY) return;
    const tot = getTotal();
    if (m === 'Split' && tot <= 0) {
        Swal.fire({ icon: 'info', title: 'Pilih item dulu', text: 'Split Payment dihitung dari total anggaran. Pilih item biaya terlebih dahulu.', confirmButtonColor: '#4338ca' });
        return;
    }
    draft = { metode: m, bank: pay.bank, rek: pay.rek, nama: pay.nama, cash: pay.cash, cashNama: pay.cashNama };
    if (m === 'Split' && !(draft.cash > 0 && draft.cash < tot)) draft.cash = Math.round(tot / 2);

    steps = m === 'Split' ? ['split', 'bank', 'penerima'] : (m === 'Cash' ? ['cash'] : ['bank', 'penerima']);
    stepIdx = 0;

    document.getElementById('dRek').value = draft.rek;
    document.getElementById('dNama').value = draft.nama;
    document.getElementById('dCashNama').value = draft.cashNama;
    markBank();
    showStep();

    const mo = document.getElementById('payModal');
    mo.classList.remove('hidden');
    mo.classList.add('flex');
}

function closePay() {
    const mo = document.getElementById('payModal');
    mo.classList.add('hidden');
    mo.classList.remove('flex');
}

function showStep() {
    const cur = steps[stepIdx];
    ALL_PANES.forEach(s => document.getElementById('pane-' + s).classList.toggle('hidden', s !== cur));
    document.getElementById('payTitle').textContent = STEP_TITLE[cur];
    document.getElementById('payBack').textContent = stepIdx === 0 ? 'Batal' : 'Kembali';
    document.getElementById('payNext').textContent = stepIdx === steps.length - 1 ? 'Konfirmasi' : 'Next';

    if (cur === 'cash') {
        document.getElementById('cashTotal').value = rp(getTotal());
    }
    if (cur === 'split') {
        document.getElementById('splitTotal').value = rp(getTotal());
        document.getElementById('splitCash').value = draft.cash ? fmt(draft.cash) : '';
        updateSplit();
    }
    if (cur === 'penerima') {
        document.getElementById('recvBank').textContent = draft.bank ? 'Tujuan: ' + draft.bank : '';
    }
    updateNext();
}

function stepValid() {
    const cur = steps[stepIdx];
    if (cur === 'cash') return draft.cashNama.trim() !== '';
    if (cur === 'split') return draft.cash > 0 && draft.cash < getTotal();
    if (cur === 'bank') return !!draft.bank;
    return draft.rek.trim() !== '' && draft.nama.trim() !== '';
}

function updateNext() {
    const btn = document.getElementById('payNext');
    const ok = stepValid();
    btn.disabled = !ok;
    btn.className = 'rounded-md border px-5 py-2 text-xs font-semibold ' +
        (ok ? 'bg-indigo-700 border-indigo-700 text-white hover:bg-indigo-800'
            : 'bg-gray-100 border-gray-300 text-gray-400 cursor-not-allowed');
}

function nextStep() {
    if (!stepValid()) return;
    if (stepIdx < steps.length - 1) { stepIdx++; showStep(); return; }

    const isCash = draft.metode === 'Cash';
    pay = {
        metode:   draft.metode,
        bank:     isCash ? '' : draft.bank,
        rek:      isCash ? '' : draft.rek.trim(),
        nama:     isCash ? '' : draft.nama.trim(),
        cashNama: isCash ? draft.cashNama.trim() : '',
        cash:     draft.metode === 'Split' ? draft.cash : 0
    };
    closePay();
    renderPay();
}

function prevStep() {
    if (stepIdx === 0) { closePay(); return; }
    stepIdx--;
    showStep();
}

function onCashInput(el) {
    const v = parseInt(el.value.replace(/\D/g, ''), 10) || 0;
    draft.cash = v;
    el.value = v ? fmt(v) : '';
    updateSplit();
    updateNext();
}

function updateSplit() {
    const tot = getTotal();
    document.getElementById('splitTransfer').value = rp(Math.max(tot - draft.cash, 0));
    document.getElementById('splitWarn').classList.toggle('hidden', !(draft.cash >= tot));
}

function onRecvInput() {
    draft.rek  = document.getElementById('dRek').value;
    draft.nama = document.getElementById('dNama').value;
    updateNext();
}

function onCashRecvInput() {
    draft.cashNama = document.getElementById('dCashNama').value;
    updateNext();
}

// ---------- Daftar bank / e-wallet (logo dimuat dari public/images/bank) ----------
function renderBankGrid() {
    document.getElementById('bankGrid').innerHTML = banks.map((b, i) => `
        <button type="button" data-bank="${i}" onclick="pickBank(${i})" class="bank-tile flex flex-col items-center gap-1 text-[11px] text-gray-600">
            <span class="bank-box flex h-14 w-full items-center justify-center rounded-lg border border-gray-200 bg-gray-50 px-1 hover:border-indigo-300">
                <span class="text-xs font-bold text-gray-700">${esc(b.kode)}</span>
                <img data-logo="${esc(b.logo)}" alt="${esc(b.nama)}" class="hidden max-h-9 max-w-full object-contain">
            </span>
            <span>${esc(b.nama)}</span>
        </button>`).join('');

    document.querySelectorAll('#bankGrid img[data-logo]').forEach(img => loadLogo(img, img.dataset.logo, 0));
}

// Coba png, lalu jpg, jpeg, webp, svg. Kalau semuanya tidak ada, tetap tampil singkatan.
function loadLogo(img, base, i) {
    if (i >= logoExts.length) return;
    img.onload  = () => { img.classList.remove('hidden'); img.previousElementSibling.classList.add('hidden'); };
    img.onerror = () => loadLogo(img, base, i + 1);
    img.src = bankBase + '/' + base + '.' + logoExts[i];
}

function markBank() {
    document.querySelectorAll('#bankGrid .bank-tile').forEach(t => {
        const on = banks[parseInt(t.dataset.bank, 10)].nama === draft.bank;
        const box = t.querySelector('.bank-box');
        box.classList.toggle('border-indigo-600', on);
        box.classList.toggle('ring-2', on);
        box.classList.toggle('ring-indigo-600', on);
        box.classList.toggle('bg-indigo-50', on);
        box.classList.toggle('border-gray-200', !on);
        box.classList.toggle('bg-gray-50', !on);
    });
}

function pickBank(i) {
    draft.bank = banks[i].nama;
    markBank();
    updateNext();
}

// ---------- Clear ----------
function clearAll() {
    cart = [];
    pay = { metode: 'Transfer', bank: '', rek: '', nama: '', cashNama: '', cash: 0 };
    ['namaPemohon', 'jabatan', 'telepon', 'email', 'lokasiTujuan', 'keperluan', 'itemSearch', 'tanggalPulang']
        .forEach(id => document.getElementById(id).value = '');
    document.getElementById('tanggalBerangkat').value = nowLocal();
    render();
    refreshDates();
}

// ---------- Event ----------
const searchEl = document.getElementById('itemSearch');
searchEl.addEventListener('focus', renderDropdown);
searchEl.addEventListener('click', renderDropdown);
searchEl.addEventListener('input', renderDropdown);
document.addEventListener('click', e => { if (!e.target.closest('#itemPicker')) closeDropdown(); });

document.getElementById('tanggalBerangkat').addEventListener('input', refreshDates);
document.getElementById('tanggalPulang').addEventListener('input', refreshDates);

document.getElementById('payModal').addEventListener('click', e => {
    if (e.target.id === 'payModal') closePay();
});

document.getElementById('rabForm').addEventListener('submit', e => {
    if (READONLY) { e.preventDefault(); return; }
    if (cart.length === 0) {
        e.preventDefault();
        Swal.fire({ icon: 'warning', title: 'Belum ada item', text: 'Pilih minimal satu item biaya.', confirmButtonColor: '#4338ca' });
        return;
    }
    if (!durasiInfo().ok) {
        e.preventDefault();
        Swal.fire({ icon: 'warning', title: 'Tanggal perjalanan belum benar', text: durasiInfo().text, confirmButtonColor: '#4338ca' });
        return;
    }
    const st = payReady();
    if (!st.ok) {
        e.preventDefault();
        Swal.fire({ icon: 'warning', title: 'Data pembayaran belum lengkap', text: st.msg, confirmButtonColor: '#4338ca' })
            .then(() => openPay(pay.metode));
    }
});

// ---------- Awal ----------
renderBankGrid();
if (!document.getElementById('tanggalBerangkat').value) {
    document.getElementById('tanggalBerangkat').value = nowLocal();
}
initialItems.forEach(r => {
    const m = master.find(x => x.id === parseInt(r.master_item_id, 10));
    if (m) addItem(m, { unit: parseInt(r.unit, 10) || 1, kali: parseInt(r.kali, 10) || 1 });
});
render();
refreshDates();
if (READONLY) lockView();
</script>
@endsection