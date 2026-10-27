<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\RabMasterItem;
use App\Models\RabPengajuan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class RabController extends Controller
{
    private const STATUSES = ['Menunggu', 'Disetujui', 'Ditolak'];

    /**
     * Daftar bank / e-wallet di popup pembayaran.
     * 'logo' = nama file TANPA ekstensi di public/images/bank/ (png/jpg/jpeg/webp/svg terbaca otomatis).
     */
    private const BANKS = [
        ['nama' => 'BCA',        'kode' => 'BCA',  'logo' => 'bca'],
        ['nama' => 'Mandiri',    'kode' => 'MDR',  'logo' => 'mandiri'],
        ['nama' => 'BRI',        'kode' => 'BRI',  'logo' => 'bri'],
        ['nama' => 'BNI',        'kode' => 'BNI',  'logo' => 'bni'],
        ['nama' => 'BSI',        'kode' => 'BSI',  'logo' => 'bsi'],
        ['nama' => 'CIMB Niaga', 'kode' => 'CIMB', 'logo' => 'cimb'],
        ['nama' => 'Danamon',    'kode' => 'DNM',  'logo' => 'danamon'],
        ['nama' => 'SeaBank',    'kode' => 'SEA',  'logo' => 'seabank'],
        ['nama' => 'DANA',       'kode' => 'DANA', 'logo' => 'dana'],
        ['nama' => 'GoPay',      'kode' => 'GP',   'logo' => 'gopay'],
        ['nama' => 'OVO',        'kode' => 'OVO',  'logo' => 'ovo'],
        ['nama' => 'ShopeePay',  'kode' => 'SPAY', 'logo' => 'shopeepay'],
    ];

    // ==========================================================
    // LIST RAB + UNDUH PDF
    // ==========================================================

    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $status = (string) $request->input('status', '');

        $rabs = $this->listQuery($search, $status)->paginate(10)->withQueryString();

        return view('pages.rab-list', [
            'rabs'          => $rabs,
            'search'        => $search,
            'status'        => $status,
            'statuses'      => self::STATUSES,
            'currentMenuId' => $this->menuId(),
        ]);
    }

    /** PDF daftar RAB (mengikuti pencarian / filter yang sedang aktif) */
    public function pdfList(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $status = (string) $request->input('status', '');

        $rabs = $this->listQuery($search, $status)->get();

        return Pdf::loadView('pages.rab-pdf-list', [
            'rabs'    => $rabs,
            'search'  => $search,
            'status'  => $status,
            'dicetak' => now(),
        ])->setPaper('a4', 'landscape')
          ->download('List-RAB-' . now()->format('Ymd-His') . '.pdf');
    }

    // ==========================================================
    // FORM (TAMBAH / EDIT), LIHAT, HAPUS
    // ==========================================================

    public function create()
    {
        return view('pages.rab', $this->formData(null));
    }

    public function edit($id)
    {
        $rab = RabPengajuan::with('items')->findOrFail($id);

        return view('pages.rab', $this->formData($rab));
    }

    /** Lihat = halaman form yang sama dengan edit, tapi semua isian terkunci (read-only) */
    public function show($id)
    {
        $rab = RabPengajuan::with('items')->findOrFail($id);

        return view('pages.rab', $this->formData($rab) + [
            'readonly'      => true,
            'currentMenuId' => $this->menuId(),
        ]);
    }

    /** PDF surat pengajuan satu RAB */
    public function pdfShow($id)
    {
        $rab = RabPengajuan::with('items')->findOrFail($id);

        return Pdf::loadView('pages.rab-pdf-surat', ['rab' => $rab])
            ->setPaper('a4', 'portrait')
            ->download('Surat-' . $rab->kode_rab . '.pdf');
    }

    public function destroy($id)
    {
        try {
            $rab  = RabPengajuan::findOrFail($id);
            $kode = $rab->kode_rab;
            $rab->delete(); // soft delete: baris tetap ada di database, hanya ditandai terhapus

            return redirect()->route('rab')->with('success', "RAB {$kode} berhasil dihapus!");
        } catch (\Throwable $e) {
            Log::error('Error menghapus RAB: ' . $e->getMessage());

            return back()->with('error', 'Terjadi kesalahan saat menghapus RAB.');
        }
    }

    // ==========================================================
    // SIMPAN (TAMBAH) DAN UPDATE (EDIT)
    // ==========================================================

    public function store(Request $request)
    {
        try {
            $rab = $this->simpan($request);

            return redirect()->route('rab')->with('success', "RAB {$rab->kode_rab} ({$rab->kode_transaksi}) berhasil diajukan!");
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('Error menyimpan RAB: ' . $e->getMessage());

            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $rab = $this->simpan($request, RabPengajuan::findOrFail($id));

            return redirect()->route('rab')->with('success', "RAB {$rab->kode_rab} berhasil diperbarui!");
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('Error memperbarui RAB: ' . $e->getMessage());

            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // ==========================================================
    // HELPER
    // ==========================================================

    private function menuId()
    {
        return Menu::where('route', 'rab')->value('menu_id');
    }

    private function formData(?RabPengajuan $rab): array
    {
        return [
            'rab'         => $rab,
            'masterItems' => RabMasterItem::where('is_active', true)
                ->orderBy('nama')
                ->get(['id', 'nama', 'keterangan', 'harga', 'satuan_unit', 'kali_per_hari', 'per_hari']),
            'banks'       => self::BANKS,
        ];
    }

    private function listQuery(string $search, string $status)
    {
        return RabPengajuan::query()
            ->when($search !== '', function ($q) use ($search) {
                $s = '%' . mb_strtolower($search) . '%';
                $q->where(function ($w) use ($s) {
                    $w->whereRaw('LOWER(kode_rab) LIKE ?', [$s])
                        ->orWhereRaw('LOWER(kode_transaksi) LIKE ?', [$s])
                        ->orWhereRaw('LOWER(lokasi_tujuan) LIKE ?', [$s])
                        ->orWhereRaw('LOWER(keperluan) LIKE ?', [$s])
                        ->orWhereRaw('LOWER(nama_pemohon) LIKE ?', [$s]);
                });
            })
            ->when(in_array($status, self::STATUSES, true), fn ($q) => $q->where('status', $status))
            ->orderByDesc('id');
    }

    private function validated(Request $request): array
    {
        $bankNames = array_column(self::BANKS, 'nama');

        return $request->validate([
            // Data pemohon (diisi manual)
            'nama_pemohon'           => 'required|string|max:100',
            'jabatan'                => 'required|string|max:100',
            'telepon'                => ['required', 'string', 'max:30', 'regex:/^[0-9+\-\s()]+$/'],
            'email'                  => 'required|email|max:255',

            // Detail perjalanan
            'lokasi_tujuan'          => 'required|string|max:255',
            'keperluan'              => 'required|string|max:1000',
            'tanggal_berangkat'      => 'required|date',
            'tanggal_pulang'         => 'required|date|after:tanggal_berangkat',

            // Pembayaran
            'metode_pembayaran'      => 'required|in:Transfer,Cash,Split',
            'bank_nama'              => ['required_if:metode_pembayaran,Transfer,Split', 'nullable', Rule::in($bankNames)],
            'no_rekening'            => ['required_if:metode_pembayaran,Transfer,Split', 'nullable', 'string', 'max:50'],
            'nama_pemilik_rekening'  => ['required_if:metode_pembayaran,Transfer,Split', 'nullable', 'string', 'max:100'],
            'penerima_cash'          => ['required_if:metode_pembayaran,Cash', 'nullable', 'string', 'max:100'],
            'nominal_cash'           => 'nullable|numeric|min:0',

            // Item biaya: unit (mobil/orang/kamar) dan kali per hari
            'items'                  => 'required|array|min:1',
            'items.*.master_item_id' => ['required', 'integer', Rule::exists('rab_master_items', 'id')->where('is_active', true)],
            'items.*.unit'           => 'required|integer|min:1|max:10000',
            'items.*.kali'           => 'required|integer|min:1|max:24',
        ], [
            'nama_pemohon.required'             => 'Nama pemohon wajib diisi.',
            'jabatan.required'                  => 'Jabatan wajib diisi.',
            'telepon.required'                  => 'Telepon wajib diisi.',
            'telepon.regex'                     => 'Format telepon tidak valid.',
            'email.required'                    => 'Email wajib diisi.',
            'email.email'                       => 'Format email tidak valid.',
            'lokasi_tujuan.required'            => 'Lokasi tujuan wajib diisi.',
            'keperluan.required'                => 'Keperluan wajib diisi.',
            'tanggal_berangkat.required'        => 'Tanggal & jam berangkat wajib diisi.',
            'tanggal_pulang.required'           => 'Tanggal & jam pulang wajib diisi.',
            'tanggal_pulang.after'              => 'Tanggal & jam pulang harus setelah tanggal & jam berangkat.',
            'items.required'                    => 'Pilih minimal satu item biaya.',
            'items.min'                         => 'Pilih minimal satu item biaya.',
            'bank_nama.required_if'             => 'Pilih bank / e-wallet untuk pembayaran.',
            'no_rekening.required_if'           => 'No rekening / e-wallet penerima wajib diisi.',
            'nama_pemilik_rekening.required_if' => 'Nama pemilik rekening wajib diisi.',
            'penerima_cash.required_if'         => 'Nama penerima cash wajib diisi.',
        ]);
    }

    /**
     * Simpan RAB baru ($rab = null) atau update RAB yang sudah ada.
     * Durasi, qty, harga, total, dan pembagian cash/transfer SELALU dihitung di server.
     * Catatan: saat diedit, harga memakai harga katalog terbaru.
     */
    private function simpan(Request $request, ?RabPengajuan $rab = null): RabPengajuan
    {
        $data = $this->validated($request);

        // Durasi: selisih waktu / 24 jam, dibulatkan ke atas, minimal 1 hari
        $berangkat = Carbon::parse($data['tanggal_berangkat']);
        $pulang    = Carbon::parse($data['tanggal_pulang']);
        $hari      = max(1, (int) ceil(($pulang->getTimestamp() - $berangkat->getTimestamp()) / 86400));

        if ($hari > 365) {
            throw ValidationException::withMessages([
                'tanggal_pulang' => 'Durasi perjalanan maksimal 365 hari.',
            ]);
        }

        $master = RabMasterItem::whereIn('id', collect($data['items'])->pluck('master_item_id'))
            ->get()
            ->keyBy('id');

        // qty = unit x kali per hari x durasi (kalau item dihitung per hari)
        $lines = collect($data['items'])
            ->values()
            ->map(function ($row, $n) use ($master, $hari) {
                $m      = $master[$row['master_item_id']];
                $unit   = (int) $row['unit'];
                $kali   = $m->per_hari ? (int) $row['kali'] : 1;
                $durasi = $m->per_hari ? $hari : 1;
                $qty    = $unit * $kali * $durasi;

                return [
                    'master_item_id' => $m->id,
                    'nama_item'      => $m->nama,
                    'keterangan'     => $m->keterangan,
                    'satuan_unit'    => $m->satuan_unit,
                    'jumlah_unit'    => $unit,
                    'kali_per_hari'  => $kali,
                    'durasi_hari'    => $durasi,
                    'qty'            => $qty,
                    'harga'          => $m->harga,
                    'jumlah'         => round($qty * $m->harga, 2),
                    'urutan'         => $n + 1,
                ];
            });

        // Pembagian pembayaran
        $total    = round($lines->sum('jumlah'), 2);
        $metode   = $data['metode_pembayaran'];
        $cash     = 0.0;
        $transfer = 0.0;

        if ($metode === 'Cash') {
            $cash = $total;
        } elseif ($metode === 'Transfer') {
            $transfer = $total;
        } else { // Split
            $cash = round((float) ($data['nominal_cash'] ?? 0), 2);

            if ($cash <= 0 || $cash >= $total) {
                throw ValidationException::withMessages([
                    'nominal_cash' => 'Nominal cash harus lebih dari 0 dan kurang dari total tagihan.',
                ]);
            }

            $transfer = round($total - $cash, 2);
        }

        $user = auth()->user();

        return DB::transaction(function () use ($data, $lines, $user, $total, $cash, $transfer, $metode, $hari, $berangkat, $pulang, $rab) {
            $payload = [
                'nama_pemohon'          => $data['nama_pemohon'],
                'jabatan'               => $data['jabatan'],
                'telepon'               => $data['telepon'],
                'email'                 => $data['email'],
                'lokasi_tujuan'         => $data['lokasi_tujuan'],
                'keperluan'             => $data['keperluan'],
                'jumlah_hari'           => $hari,
                'tanggal_berangkat'     => $berangkat,
                'tanggal_pulang'        => $pulang,
                'metode_pembayaran'     => $metode,
                'bank_nama'             => $metode === 'Cash' ? null : $data['bank_nama'],
                'no_rekening'           => $metode === 'Cash' ? null : $data['no_rekening'],
                'nama_pemilik_rekening' => $metode === 'Cash' ? null : $data['nama_pemilik_rekening'],
                'penerima_cash'         => $metode === 'Cash' ? $data['penerima_cash'] : null,
                'total_anggaran'        => $total,
                'nominal_cash'          => $cash,
                'nominal_transfer'      => $transfer,
            ];

            if ($rab) {
                $rab->update($payload);
                $rab->items()->delete();
            } else {
                $rab = RabPengajuan::create($payload + [
                    'kode_rab'          => $this->generateKode(),
                    'kode_transaksi'    => $this->generateKodeTransaksi(),
                    'user_id'           => $user->user_id,
                    'tanggal_pengajuan' => now(),
                    'status'            => 'Menunggu',
                ]);
            }

            $rab->items()->createMany($lines->all());

            return $rab;
        });
    }

    /** Format: RAB-202610-0001 (urutan reset tiap bulan). */
    private function generateKode(): string
    {
        $prefix = 'RAB-' . now()->format('Ym') . '-';

        $last = RabPengajuan::withTrashed()
            ->where('kode_rab', 'like', $prefix . '%')
            ->orderByDesc('kode_rab')
            ->value('kode_rab');

        $next = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    /** Format: TRX-001, TRX-002, ... (urutan terus naik, tidak pernah dipakai ulang). */
    private function generateKodeTransaksi(): string
    {
        $max = RabPengajuan::withTrashed()
            ->whereNotNull('kode_transaksi')
            ->pluck('kode_transaksi')
            ->map(fn ($k) => (int) preg_replace('/\D/', '', $k))
            ->max() ?? 0;

        return 'TRX-' . str_pad((string) ($max + 1), 3, '0', STR_PAD_LEFT);
    }
}