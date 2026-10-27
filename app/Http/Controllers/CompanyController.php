<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyType;
use App\Models\CompanyPic;
use App\Models\Province;
use App\Models\Regency;
use App\Models\District;
use App\Models\Village;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CompanyPivotExport;

class CompanyController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $companiesQuery = Company::with('companyType', 'user');

        if ($user->role_id == 1) {
            // superadmin - all data
        } elseif (in_array($user->role_id, [7, 11])) {
            $companiesQuery->whereHas('user', function ($q) {
                $q->where('role_id', 12);
            });
        } elseif ($user->role_id == 12) {
            $companiesQuery->where('user_id', $user->user_id);
        } else {
            $companiesQuery->whereNull('company_id');
        }

        $totalCompanies = (clone $companiesQuery)->count();
        $jenisCompanies = (clone $companiesQuery)->distinct('company_type_id')->count('company_type_id');
        $tierCompanies = (clone $companiesQuery)->distinct('tier')->count('tier');
        $activeCompanies = (clone $companiesQuery)->where('status', 'active')->count();

        $companies = $companiesQuery->paginate(10);
        $types = CompanyType::where('is_active', true)->get();
        $provinces = Province::all(); 

        return view('pages.company', compact(
            'companies',
            'types',
            'totalCompanies',
            'jenisCompanies',
            'tierCompanies',
            'activeCompanies',
            'provinces'
        ));
    }

     public function getRegencies($province_id)
    {
        try {
            $regencies = Regency::where('province_id', $province_id)
                ->orderBy('name', 'asc')
                ->get(['id', 'name']);
            
            return response()->json([
                'success' => true,
                'data' => $regencies
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching regencies: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getDistricts($regency_id)
    {
        try {
            $districts = District::where('regency_id', $regency_id)
                ->orderBy('name', 'asc')
                ->get(['id', 'name']);
            
            return response()->json([
                'success' => true,
                'data' => $districts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching districts: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getVillages($district_id)
    {
        try {
            $villages = Village::where('district_id', $district_id)
                ->orderBy('name', 'asc')
                ->get(['id', 'name']);
            
            return response()->json([
                'success' => true,
                'data' => $villages
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching villages: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getCompanyPics($id)
    {
        try {
            $pics = CompanyPIC::where('company_id', $id)
                ->orderBy('pic_name', 'asc')
                ->get();
            
            return response()->json([
                'success' => true,
                'pics' => $pics->map(function($pic) {
                    return [
                        'pic_id' => $pic->pic_id,
                        'pic_name' => $pic->pic_name,
                        'position' => $pic->position ?? '-',
                        'phone' => $pic->phone ?? '-',
                        'email' => $pic->email ?? '-'
                    ];
                })
            ]);
        } catch (\Exception $e) {
            \Log::error('Error loading PICs: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading PICs: ' . $e->getMessage()
            ], 500);
        }
    }

    public function search(Request $request)
    {
        $query = Company::with('companyType');
        $search = $request->input('search') ?? $request->input('query');

        if ($search) {
            $searchLower = strtolower($search);
            $query->where(function($q) use ($searchLower) {
                $q->whereRaw('LOWER(company_name) LIKE ?', ["%{$searchLower}%"])
                  ->orWhereRaw('LOWER(description) LIKE ?', ["%{$searchLower}%"])
                  ->orWhereHas('companyType', function($qt) use ($searchLower) {
                      $qt->whereRaw('LOWER(type_name) LIKE ?', ["%{$searchLower}%"]);
                  });
            });
        }

        if ($request->filled('type')) {
            $type = $request->type;
            if (is_numeric($type)) {
                $query->where('company_type_id', $type);
            } else {
                $query->whereHas('companyType', function($q) use ($type) {
                    $q->whereRaw('LOWER(type_name) LIKE ?', ['%' . strtolower($type) . '%']);
                });
            }
        }

        if ($request->filled('tier')) {
            $query->whereRaw('LOWER(tier) = ?', [strtolower($request->tier)]);
        }

        if ($request->filled('status')) {
            $query->whereRaw('LOWER(status) = ?', [strtolower($request->status)]);
        }

        $companies = $query->orderBy('company_name', 'asc')->paginate(10);

        return response()->json([
            'items' => $companies->map(function($company, $index) use ($companies) {
                return [
                    'number' => $companies->firstItem() + $index,
                    'company_id' => $company->company_id,
                    'company_name' => $company->company_name ?? '-',
                    'company_type' => $company->companyType->type_name ?? '-',
                    'tier' => $company->tier ?? '-',
                    'description' => $company->description ?? '-',
                    'status' => ucfirst($company->status ?? 'inactive'),
                    'actions' => $this->getCompanyActions($company)
                ];
            })->toArray(),
            'pagination' => [
                'current_page' => $companies->currentPage(),
                'last_page' => $companies->lastPage(),
                'from' => $companies->firstItem(),
                'to' => $companies->lastItem(),
                'total' => $companies->total()
            ]
        ]);
    }

    public function show($id)
    {
        try {
            $company = Company::with([
                'companyType', 
                'user', 
                'province', 
                'regency', 
                'district', 
                'village'
            ])->findOrFail($id);
            
            $pics = CompanyPic::where('company_id', $id)
                ->orderBy('pic_name')
                ->get();

            return response()->json([
                'success' => true,
                'company' => [
                    'company_id' => $company->company_id,
                    'company_name' => $company->company_name,
                    'company_type' => $company->companyType->type_name ?? '-',
                    'company_type_id' => $company->company_type_id,
                    'tier' => $company->tier ?? '-',
                    'description' => $company->description ?? '-',
                    'status' => $company->status,
                    'created_by' => $company->user->name ?? '-',
                    'province_id' => $company->province_id,
                    'regency_id' => $company->regency_id,
                    'district_id' => $company->district_id,
                    'village_id' => $company->village_id,
                    'province' => $company->province->name ?? '-',
                    'regency' => $company->regency->name ?? '-',
                    'district' => $company->district->name ?? '-',
                    'village' => $company->village->name ?? '-',
                    'full_address' => $company->address ?? '-',
                    'company_phone' => $company->phone ?? '-',
                    'company_email' => $company->email ?? '-',
                    'company_website' => $company->website ?? null,
                    'company_linkedin' => $company->linkedin ?? null,
                    'company_instagram' => $company->instagram ?? null,
                    'company_logo' => $company->logo ? asset('storage/' . $company->logo) : null,
                ],
                'pics' => $pics->map(function($pic) {
                    return [
                        'pic_id' => $pic->pic_id,
                        'pic_name' => $pic->pic_name,
                        'position' => $pic->position ?? '-',
                        'phone' => $pic->phone ?? '-',
                        'email' => $pic->email ?? '-'
                    ];
                })
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching company detail: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat detail perusahaan'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        \Log::info('📥 Store Request:', $request->all());
        
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_type_id' => 'required|exists:company_type,company_type_id',
            'tier' => 'nullable|string|in:A,B,C,D',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'province_id' => 'nullable|string|max:255',
            'regency_id' => 'nullable|string|max:255',
            'district_id' => 'nullable|string|max:255',
            'village_id' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'company_phone' => 'nullable|string|max:255',
            'company_email' => 'nullable|email|max:255',
            'company_website' => 'nullable|url|max:255',
            'company_linkedin' => 'nullable|url|max:255',
            'company_instagram' => 'nullable|string|max:255',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'pics' => 'nullable|array',
            'pics.*.pic_name' => 'required_with:pics|string|max:255',
            'pics.*.position' => 'nullable|string|max:255',
            'pics.*.phone' => 'nullable|string|max:20',
            'pics.*.email' => 'nullable|email|max:255',
        ]);

        try {
            DB::beginTransaction();

            $logoPath = null;
            if ($request->hasFile('company_logo')) {
                $logoPath = $request->file('company_logo')->store('company_logos', 'public');
                \Log::info('✅ Logo uploaded:', ['path' => $logoPath]);
            }

            $company = Company::create([
                'company_name' => $validated['company_name'],
                'company_type_id' => $validated['company_type_id'],
                'tier' => $validated['tier'] ?? null,
                'description' => $validated['description'] ?? null,
                'status' => $validated['status'],
                'user_id' => auth()->id(),
                'address' => $validated['address'] ?? null,
                'province_id' => $validated['province_id'] ?? null,
                'regency_id' => $validated['regency_id'] ?? null,
                'district_id' => $validated['district_id'] ?? null,
                'village_id' => $validated['village_id'] ?? null,
                'phone' => $validated['company_phone'] ?? null,
                'email' => $validated['company_email'] ?? null,
                'website' => $validated['company_website'] ?? null,
                'linkedin' => $validated['company_linkedin'] ?? null,
                'instagram' => $validated['company_instagram'] ?? null,
                'logo' => $logoPath,
            ]);
            
            \Log::info('✅ Company created:', ['company_id' => $company->company_id]);
            
            if ($request->has('pics') && is_array($request->pics)) {
                foreach ($request->pics as $index => $picData) {
                    if (empty($picData['pic_name'])) {
                        \Log::warning("⚠️ Skipping empty PIC at index {$index}");
                        continue;
                    }
                    
                    CompanyPic::create([
                        'company_id' => $company->company_id,
                        'pic_name' => $picData['pic_name'],
                        'position' => $picData['position'] ?? null,
                        'phone' => $picData['phone'] ?? null,
                        'email' => $picData['email'] ?? null,
                    ]);
                }
            }

            DB::commit();
            \Log::info('✅ Transaction committed');

            return redirect()->back()->with('success', 'Company berhasil ditambahkan');
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('❌ Error storing company: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Gagal menambahkan company: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        \Log::info('📝 Update Request:', $request->all());
        
        $company = Company::findOrFail($id);
        
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_type_id' => 'required|exists:company_type,company_type_id',
            'tier' => 'nullable|string|in:A,B,C,D',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'province_id' => 'nullable|string|max:255',
            'regency_id' => 'nullable|string|max:255',
            'district_id' => 'nullable|string|max:255',
            'village_id' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'company_phone' => 'nullable|string|max:255',
            'company_email' => 'nullable|email|max:255',
            'company_website' => 'nullable|url|max:255',
            'company_linkedin' => 'nullable|url|max:255',
            'company_instagram' => 'nullable|string|max:255',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'pics' => 'nullable|array',
            'pics.*.pic_name' => 'required_with:pics|string|max:255',
            'pics.*.position' => 'nullable|string|max:255',
            'pics.*.phone' => 'nullable|string|max:20',
            'pics.*.email' => 'nullable|email|max:255',
        ]);

        try {
            DB::beginTransaction();

            $logoPath = $company->logo;
            if ($request->hasFile('company_logo')) {
                if ($company->logo) {
                    Storage::disk('public')->delete($company->logo);
                }
                $logoPath = $request->file('company_logo')->store('company_logos', 'public');
                \Log::info('✅ Logo updated:', ['path' => $logoPath]);
            }

            $company->update([
                'company_name' => $validated['company_name'],
                'company_type_id' => $validated['company_type_id'],
                'tier' => $validated['tier'] ?? null,
                'description' => $validated['description'] ?? null,
                'status' => $validated['status'],
                'address' => $validated['address'] ?? null,
                'province_id' => $validated['province_id'] ?? null,
                'regency_id' => $validated['regency_id'] ?? null,
                'district_id' => $validated['district_id'] ?? null,
                'village_id' => $validated['village_id'] ?? null,
                'phone' => $validated['company_phone'] ?? null,
                'email' => $validated['company_email'] ?? null,
                'website' => $validated['company_website'] ?? null,
                'linkedin' => $validated['company_linkedin'] ?? null,
                'instagram' => $validated['company_instagram'] ?? null,
                'logo' => $logoPath,
            ]);
            
            \Log::info('✅ Company updated:', ['company_id' => $company->company_id]);
            
            CompanyPic::where('company_id', $company->company_id)->delete();
            
            if ($request->has('pics') && is_array($request->pics)) {
                foreach ($request->pics as $index => $picData) {
                    if (empty($picData['pic_name'])) continue;
                    
                    CompanyPic::create([
                        'company_id' => $company->company_id,
                        'pic_name' => $picData['pic_name'],
                        'position' => $picData['position'] ?? null,
                        'phone' => $picData['phone'] ?? null,
                        'email' => $picData['email'] ?? null,
                    ]);
                }
            }

            DB::commit();
            \Log::info('✅ Update committed');

            return redirect()->back()->with('success', 'Company berhasil diupdate');
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('❌ Error updating company: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Gagal mengupdate company: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function getPICsByCompany($companyId)
    {
        try {
            $pics = CompanyPic::where('company_id', $companyId)
                ->select('pic_id as id', 'pic_name as name', 'position', 'phone', 'email')
                ->orderBy('pic_name', 'asc')
                ->get();
            
            return response()->json([
                'success' => true,
                'pics' => $pics
            ]);
        } catch (\Exception $e) {
            \Log::error("Error fetching PICs: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to load PICs'
            ], 500);
        }
    }

    public function getCompaniesForDropdown()
    {
        try {
            $user = Auth::user();
            $companiesQuery = Company::query();
            
            if ($user->role_id == 1) {
                // superadmin
            } elseif (in_array($user->role_id, [7, 11])) {
                $companiesQuery->whereHas('user', function ($q) {
                    $q->where('role_id', 12);
                });
            } elseif ($user->role_id == 12) {
                $companiesQuery->where('user_id', $user->user_id);
            } else {
                $companiesQuery->whereNull('company_id');
            }
            
            $companies = $companiesQuery
                ->where('status', 'active')
                ->select('company_id as id', 'company_name as name')
                ->orderBy('company_name', 'asc')
                ->get();
            
            return response()->json([
                'success' => true,
                'companies' => $companies
            ]);
        } catch (\Exception $e) {
            \Log::error("Error fetching companies: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to load companies'
            ], 500);
        }
    }

    private function getCompanyActions($company)
    {
        $currentMenuId = view()->shared('currentMenuId', null);
        
        $canEdit = auth()->check() && auth()->user()->canAccess($currentMenuId ?? 1, 'edit');
        $canDelete = auth()->check() && auth()->user()->canAccess($currentMenuId ?? 1, 'delete');
        $canView = auth()->check() && auth()->user()->canAccess($currentMenuId ?? 1, 'view');

        $actions = [];

        if ($canView) {
            $actions[] = [
                'type' => 'view',
                'onclick' => "showCompanyDetail('{$company->company_id}')",
                'title' => 'Show Detail'
            ];
        }

        if ($canEdit) {
            $actions[] = [
                'type' => 'edit',
                'onclick' => "openEditCompanyModal('{$company->company_id}', '" . addslashes($company->company_name) . "', '{$company->company_type_id}', '{$company->tier}', '" . addslashes($company->description ?? '') . "', '{$company->status}')",
                'title' => 'Edit Company'
            ];
        }

        if ($canDelete) {
            $csrfToken = csrf_token();
            $deleteRoute = route('company.destroy', $company->company_id);
            
            $actions[] = [
                'type' => 'delete',
                'onclick' => "deleteCompany('{$company->company_id}', '{$deleteRoute}', '{$csrfToken}')",
                'title' => 'Delete Company'
            ];
        }

        return $actions;
    }

    public function storeCompanyAjax(Request $request)
    {
        $request->headers->set('Accept', 'application/json');
        
        \Log::info('🔥 AJAX Company Store Request:', $request->all());
        
        try {
            $validated = $request->validate([
                'company_name' => 'required|string|max:255',
                'company_type_id' => 'required|exists:company_type,company_type_id',
                'tier' => 'nullable|in:A,B,C,D',
                'status' => 'nullable|in:active,inactive',
                'description' => 'nullable|string',
            ]);

            $company = Company::create([
                'company_name' => $validated['company_name'],
                'company_type_id' => $validated['company_type_id'],
                'tier' => $validated['tier'] ?? null,
                'status' => $validated['status'] ?? 'active',
                'description' => $validated['description'] ?? null,
                'user_id' => auth()->id(),
            ]);

            \Log::info('✅ Company created via AJAX:', ['company_id' => $company->company_id]);

            return response()->json([
                'success' => true,
                'message' => 'Company berhasil ditambahkan',
                'company' => [
                    'id' => $company->company_id,
                    'name' => $company->company_name
                ]
            ], 200, ['Content-Type' => 'application/json']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('❌ Validation error:', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422, ['Content-Type' => 'application/json']);

        } catch (\Exception $e) {
            \Log::error('❌ Error creating company via AJAX:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500, ['Content-Type' => 'application/json']);
        }
    }

    public function exportPdf($id)
    {
        $company = Company::with(['companyType', 'province', 'regency', 'district', 'village'])->findOrFail($id);
        $pics = CompanyPic::where('company_id', $id)->orderBy('pic_name')->get();

        $logoPath = null;
        if ($company->logo) {
            $fullPath = storage_path('app/public/' . $company->logo);
            if (file_exists($fullPath)) {
                $logoPath = $fullPath;
            }
        }

        $inotalLogoPath = public_path('img/LOGO INOTAL UNTUK BG TERANG.png');
        if (!file_exists($inotalLogoPath)) {
            $inotalLogoPath = null;
        }

        $documentNumber = 'INOTAL/RS/' . now()->format('Y') . '/' . str_pad($company->company_id, 4, '0', STR_PAD_LEFT);

        $pdf = Pdf::loadView('pages.company-pdf', compact('company', 'pics', 'logoPath', 'inotalLogoPath', 'documentNumber'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('Informasi-RS-' . str_replace(' ', '-', $company->company_name) . '.pdf');
    }

    public function exportPdfList(Request $request)
    {
        $query = Company::with('companyType');

        if ($request->filled('type_id')) {
            $query->where('company_type_id', $request->type_id);
        }

        if ($request->filled('tier')) {
            $query->where('tier', $request->tier);
        }

        $companies = $query->orderBy('company_name')->get();

        $typeLabel = 'Semua Tipe';
        if ($request->filled('type_id')) {
            $type = CompanyType::find($request->type_id);
            $typeLabel = $type->type_name ?? 'Semua Tipe';
        }

        $tierLabel = $request->filled('tier') ? 'Tier ' . $request->tier : 'Semua Tier';

        $inotalLogoPath = public_path('img/LOGO INOTAL UNTUK BG TERANG.png');
        if (!file_exists($inotalLogoPath)) {
            $inotalLogoPath = null;
        }

        $documentNumber = 'INOTAL/RS-LIST/' . now()->format('Y') . '/' . now()->format('mdHis');

        $pdf = Pdf::loadView('pages.company-pdf-list', compact('companies', 'typeLabel', 'tierLabel', 'inotalLogoPath', 'documentNumber'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('Daftar-Rumah-Sakit-' . now()->format('Ymd') . '.pdf');
    }

    public function exportPdfSelected(Request $request)
    {
        $ids = explode(',', $request->query('ids', ''));
        $ids = array_filter($ids);

        $companies = Company::with('companyType')
            ->whereIn('company_id', $ids)
            ->orderBy('company_name')
            ->get();

        $typeLabel = 'Pilihan Manual';
        $tierLabel = $companies->count() . ' Rumah Sakit Terpilih';

        $inotalLogoPath = public_path('img/LOGO INOTAL UNTUK BG TERANG.png');
        if (!file_exists($inotalLogoPath)) {
            $inotalLogoPath = null;
        }

        $documentNumber = 'INOTAL/RS-LIST/' . now()->format('Y') . '/' . now()->format('mdHis');

        $pdf = Pdf::loadView('pages.company-pdf-list', compact('companies', 'typeLabel', 'tierLabel', 'inotalLogoPath', 'documentNumber'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('Daftar-RS-Pilihan-' . now()->format('Ymd') . '.pdf');
    }

    public function exportPivotExcel()
    {
        $tiers = Company::select('tier')
            ->whereNotNull('tier')
            ->distinct()
            ->orderBy('tier')
            ->pluck('tier')
            ->toArray();

        $data = [];
        $data[] = array_merge(['Keterangan'], $tiers, ['Grand Total']);

        $rowTotal = ['Jumlah Perusahaan'];
        $rowActive = ['active'];
        $rowInactive = ['inactive'];
        $rowPic = ['Total PIC'];

        $grandTotal = 0;
        $grandActive = 0;
        $grandInactive = 0;
        $grandPic = 0;

        foreach ($tiers as $tier) {
            $companyIds = Company::where('tier', $tier)->pluck('company_id');

            $total = $companyIds->count();
            $active = Company::where('tier', $tier)->where('status', 'active')->count();
            $inactive = Company::where('tier', $tier)->where('status', 'inactive')->count();
            $pic = CompanyPic::whereIn('company_id', $companyIds)->count();

            $rowTotal[] = $total;
            $rowActive[] = $active;
            $rowInactive[] = $inactive;
            $rowPic[] = $pic;

            $grandTotal += $total;
            $grandActive += $active;
            $grandInactive += $inactive;
            $grandPic += $pic;
        }

        $rowTotal[] = $grandTotal;
        $rowActive[] = $grandActive;
        $rowInactive[] = $grandInactive;
        $rowPic[] = $grandPic;

        $data[] = $rowTotal;
        $data[] = $rowActive;
        $data[] = $rowInactive;
        $data[] = $rowPic;

        return Excel::download(new CompanyPivotExport($data), 'Pivot-Company-' . now()->format('Ymd') . '.xlsx');
    }
}