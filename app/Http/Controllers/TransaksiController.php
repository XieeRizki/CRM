<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\SalesVisit;
use App\Models\User;
use App\Models\Company;
use App\Models\CompanyPic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class TransaksiController extends Controller
{
    public function index()
    {
        $salesVisits = SalesVisit::with(['company', 'sales', 'user', 'pic'])
            ->orderBy('id', 'desc')->get();

        $transaksi = Transaksi::with(['sales', 'company', 'salesVisit', 'pic'])
            ->orderBy('created_at', 'desc')->get();
        
        $sales = User::where('role_id', 12)
            ->select('user_id', 'username', 'email')
            ->orderBy('username')
            ->get();

        $companies = Company::with(['province', 'regency', 'district', 'village', 'companyType'])
            ->orderBy('company_name', 'asc')->get();

        $currentMenuId = 17;
        
        return view('pages.transaksi', compact('transaksi', 'sales', 'companies', 'salesVisits', 'currentMenuId'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'sales_visit_id' => 'nullable|integer|exists:sales_visits,id',
                'sales_id' => 'required|integer|exists:users,user_id',
                'company_id' => 'required|integer|exists:company,company_id',
                'pic_id' => 'nullable|integer|exists:company_pics,pic_id',
                'nama_sales' => 'required|string|max:255',
                'nama_perusahaan' => 'required|string|max:255',
                'pic_name' => 'nullable|string|max:255',
                'nilai_proyek' => 'required|numeric|min:0',
                'status' => 'required|in:Deals,Fails',
                'bukti_spk' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
                'bukti_dp' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
                'tanggal_mulai_kerja' => 'nullable|date_format:d M Y',
                'tanggal_selesai_kerja' => 'nullable|date_format:d M Y|after_or_equal:tanggal_mulai_kerja',
                'keterangan' => 'nullable|string|max:1000',
            ]);

            if ($request->hasFile('bukti_spk')) {
                $validated['bukti_spk'] = $request->file('bukti_spk')->store('transaksi/spk', 'public');
            }

            if ($request->hasFile('bukti_dp')) {
                $validated['bukti_dp'] = $request->file('bukti_dp')->store('transaksi/dp', 'public');
            }

            Transaksi::create($validated);

            return redirect()->route('transaksi')
                ->with('success', 'Transaksi berhasil ditambahkan! ');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Validasi gagal.  Periksa kembali data Anda.');
        } catch (\Exception $e) {
            \Log::error('Error creating transaksi: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $item = Transaksi::with(['sales', 'company', 'salesVisit', 'pic'])->findOrFail($id);

        // Format tanggal ke format d M Y (BERSIH, tanpa timestamp)
        $response = $item->toArray();

        if ($item->tanggal_mulai_kerja) {
            $response['tanggal_mulai_kerja'] = Carbon::parse($item->tanggal_mulai_kerja)->format('d M Y');
        }

        if ($item->tanggal_selesai_kerja) {
            $response['tanggal_selesai_kerja'] = Carbon::parse($item->tanggal_selesai_kerja)->format('d M Y');
        }

        return response()->json($response);
    }

    public function edit($id)
    {
        try {
            $item = Transaksi::with(['sales', 'company', 'salesVisit', 'pic'])->findOrFail($id);
            return response()->json($item);
        } catch (\Exception $e) {
            \Log::error('Error fetching transaksi for edit: ' . $e->getMessage());
            return response()->json(['error' => 'Transaksi tidak ditemukan'], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $transaksi = Transaksi::findOrFail($id);

            $validated = $request->validate([
                'sales_visit_id' => 'nullable|integer|exists:sales_visits,id',
                'sales_id' => 'required|integer|exists:users,user_id',
                'company_id' => 'required|integer|exists:company,company_id',
                'pic_id' => 'nullable|integer|exists:company_pics,pic_id',
                'nama_sales' => 'required|string|max:255',
                'nama_perusahaan' => 'required|string|max:255',
                'pic_name' => 'nullable|string|max:255',
                'nilai_proyek' => 'required|numeric|min:0',
                'status' => 'required|in:Deals,Fails',
                'bukti_spk' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
                'bukti_dp' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
                'tanggal_mulai_kerja' => 'nullable|date_format:d M Y',
                'tanggal_selesai_kerja' => 'nullable|date_format:d M Y|after_or_equal:tanggal_mulai_kerja',
                'keterangan' => 'nullable|string|max:1000',
            ]);

            if ($request->hasFile('bukti_spk')) {
                if ($transaksi->bukti_spk) {
                    Storage::disk('public')->delete($transaksi->bukti_spk);
                }
                $validated['bukti_spk'] = $request->file('bukti_spk')->store('transaksi/spk', 'public');
            }

            if ($request->hasFile('bukti_dp')) {
                if ($transaksi->bukti_dp) {
                    Storage::disk('public')->delete($transaksi->bukti_dp);
                }
                $validated['bukti_dp'] = $request->file('bukti_dp')->store('transaksi/dp', 'public');
            }

            $transaksi->update($validated);

            return redirect()->route('transaksi')
                ->with('success', 'Transaksi berhasil diupdate!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Validasi gagal. Periksa kembali data Anda.');
        } catch (\Exception $e) {
            \Log::error('Error updating transaksi: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $transaksi = Transaksi::findOrFail($id);

            if ($transaksi->bukti_spk) {
                Storage::disk('public')->delete($transaksi->bukti_spk);
            }
            if ($transaksi->bukti_dp) {
                Storage::disk('public')->delete($transaksi->bukti_dp);
            }

            $transaksi->delete();

            return redirect()->route('transaksi')
                ->with('success', 'Transaksi berhasil dihapus!');
        } catch (\Exception $e) {
            \Log::error('Error deleting transaksi: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menghapus transaksi');
        }
    }

    public function search(Request $request)
    {
        try {
            $search = $request->input('search', '');

            $transaksi = Transaksi::with(['sales', 'company', 'salesVisit', 'pic'])
                ->where('nama_sales', 'like', "%{$search}%")
                ->orWhere('nama_perusahaan', 'like', "%{$search}%")
                ->orWhere('status', 'like', "%{$search}%")
                ->orderBy('created_at', 'desc')
                ->get();

            $salesVisits = SalesVisit::with(['company', 'sales', 'user', 'pic'])
                ->orderBy('id', 'desc')
                ->get();

            $sales = User::where('role_id', 12)
                ->select('user_id', 'username', 'email')
                ->orderBy('username')
                ->get();
            
            $companies = Company::with(['province', 'regency', 'district', 'village', 'companyType'])
                ->orderBy('company_name', 'asc')->get();

            $currentMenuId = 17;

            return view('pages.transaksi', compact('transaksi', 'sales', 'companies', 'salesVisits', 'currentMenuId'));
        } catch (\Exception $e) {
            \Log::error('Error searching transaksi: ' . $e->getMessage());
            return redirect()->route('transaksi')
                ->with('error', 'Terjadi kesalahan saat mencari transaksi');
        }
    }

    public function getPicsByCompany($companyId)
    {
        try {
            $pics = CompanyPic::where('company_id', $companyId)
                ->select('pic_id', 'pic_name', 'position', 'email', 'phone')
                ->orderBy('pic_name')
                ->get();
            
            return response()->json([
                'success' => true,
                'pics' => $pics
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Error fetching PICs: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to load PICs',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getSalesUsers()
    {
        try {
            $sales = User::where('role_id', 12)
                ->select('user_id', 'username', 'email')
                ->orderBy('username')
                ->get();
            
            return response()->json([
                'success' => true,
                'sales' => $sales
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Error fetching sales users: ' .  $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to load sales',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}