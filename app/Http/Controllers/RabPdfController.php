<?php

namespace App\Http\Controllers;

use App\Models\RabPengajuan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RabPdfController extends Controller
{
    private const STATUSES  = ['Menunggu', 'Disetujui', 'Ditolak'];
    private const MAKS_DATA = 100;

    /**
     * Unduh PDF dari List RAB.
     * scope  = pilih (RAB yang dicentang) | semua (sesuai pencarian & filter)
     * format = rekap (satu tabel ringkas) | surat (satu halaman surat pengajuan per RAB)
     */
    public function unduh(Request $request)
    {
        $scope  = $request->input('scope') === 'pilih' ? 'pilih' : 'semua';
        $format = $request->input('format') === 'surat' ? 'surat' : 'rekap';
        $search = trim((string) $request->input('search', ''));
        $status = (string) $request->input('status', '');

        if (!in_array($status, self::STATUSES, true)) {
            $status = '';
        }

        try {
            @ini_set('memory_limit', '512M');
            @set_time_limit(120);

            $query = RabPengajuan::query()->with('items');

            if ($scope === 'pilih') {
                $ids = collect((array) $request->input('ids', []))
                    ->map(fn ($i) => (int) $i)
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();

                if (empty($ids)) {
                    return back()->with('error', 'Belum ada RAB yang dicentang. Centang minimal satu RAB, atau pilih "Semua RAB".');
                }

                $query->whereIn('id', $ids);
            } else {
                $query
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
                    ->when($status !== '', fn ($q) => $q->where('status', $status));
            }

            $rabs = $query->orderByDesc('id')->limit(self::MAKS_DATA + 1)->get();

            if ($rabs->isEmpty()) {
                return back()->with('error', 'Tidak ada data RAB untuk diunduh.');
            }

            if ($rabs->count() > self::MAKS_DATA) {
                return back()->with('error', 'Maksimal ' . self::MAKS_DATA . ' RAB per PDF. Persempit dengan pencarian / filter, atau centang RAB yang diperlukan.');
            }

            if ($format === 'surat') {
                $pdf  = Pdf::loadView('pages.rab-pdf-surat', ['rabs' => $rabs])->setPaper('a4', 'portrait');
                $nama = $rabs->count() === 1
                    ? 'Surat-' . $rabs->first()->kode_rab
                    : 'RAB-Surat-' . now()->format('Ymd-His');
            } else {
                $keterangan = $scope === 'pilih'
                    ? 'RAB yang dipilih'
                    : implode(' | ', array_filter([
                        'Semua RAB',
                        $search !== '' ? 'Pencarian: "' . $search . '"' : null,
                        $status !== '' ? 'Status: ' . $status : null,
                    ]));

                $pdf = Pdf::loadView('pages.rab-pdf-list', [
                    'rabs'       => $rabs,
                    'keterangan' => $keterangan,
                    'dicetak'    => now(),
                ])->setPaper('a4', 'landscape');

                $nama = 'RAB-Rekap-' . now()->format('Ymd-His');
            }

            return $pdf->download($nama . '.pdf');
        } catch (\Throwable $e) {
            Log::error('Error membuat PDF RAB: ' . $e->getMessage());

            return back()->with('error', 'Gagal membuat PDF: ' . $e->getMessage());
        }
    }

    /** Surat pengajuan satu RAB (dari ikon PDF di baris list) */
    public function satu($id)
    {
        $rab = RabPengajuan::with('items')->findOrFail($id);

        try {
            @ini_set('memory_limit', '512M');

            return Pdf::loadView('pages.rab-pdf-surat', ['rabs' => collect([$rab])])
                ->setPaper('a4', 'portrait')
                ->download('Surat-' . $rab->kode_rab . '.pdf');
        } catch (\Throwable $e) {
            Log::error('Error membuat PDF surat RAB: ' . $e->getMessage());

            return back()->with('error', 'Gagal membuat PDF: ' . $e->getMessage());
        }
    }
}