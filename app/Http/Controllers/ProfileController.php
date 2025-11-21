<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Halaman utama Profile
     */
    public function index()
    {
        return view('pages.profile');
    }

    /**
     * Halaman About Us
     */
    public function aboutUs()
    {
        try {
            // Ambil data profile terbaru
            $profile = Profile::latest()->first();
            
            return view('pages.public', compact('profile'));
        } catch (\Exception $e) {
            // Jika tidak ada data, kirim null
            return view('pages.public', ['profile' => null]);
        }
    }

    /**
     * Ambil semua data profile (API)
     */
    public function getProfiles()
    {
        try {
            $profiles = Profile::orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $profiles
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Simpan base64 image ke storage
     */
    private function saveBase64Image($base64String, $path = 'logos')
    {
        if (!$base64String) {
            return null;
        }

        try {
            // Ambil data dan extension dari base64
            if (preg_match('/^data:image\/(\w+);base64,/', $base64String, $type)) {
                $base64String = substr($base64String, strpos($base64String, ',') + 1);
                $type = strtolower($type[1]); // jpg, png, gif

                // Decode base64
                $imageData = base64_decode($base64String);

                if ($imageData === false) {
                    throw new \Exception('Base64 decode failed');
                }

                // Generate unique filename
                $fileName = uniqid() . '_' . time() . '.' . $type;
                $filePath = $path . '/' . $fileName;

                // Simpan ke storage/app/public
                Storage::disk('public')->put($filePath, $imageData);

                return $filePath;
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Tambah profile baru
     */
    public function store(Request $request)
    {
        // Validasi sesuai dengan struktur database
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:255',
            'company_website' => 'required|url|max:255',
            'phone_number' => 'required|string|max:20',
            'support_email' => 'required|email|max:255',
            'description' => 'nullable|string',
            'logo_path' => 'nullable|string',
            'favicon_path' => 'nullable|string',
            'version' => 'nullable|string|max:50',
            'app_name' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $data = $validator->validated();

            // Simpan logo jika ada
            if ($request->logo_path) {
                $logoPath = $this->saveBase64Image($request->logo_path, 'company/logos');
                $data['logo_path'] = $logoPath;
            }

            // Simpan favicon jika ada
            if ($request->favicon_path) {
                $faviconPath = $this->saveBase64Image($request->favicon_path, 'company/favicons');
                $data['favicon_path'] = $faviconPath;
            }

            $profile = Profile::create($data);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Profile berhasil ditambahkan',
                'data' => $profile
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ambil satu profile berdasarkan ID
     */
    public function show($id)
    {
        try {
            $profile = Profile::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $profile
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Profile tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Update profile
     */
    public function update(Request $request, $id)
    {
        // Validasi
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:255',
            'company_website' => 'required|url|max:255',
            'phone_number' => 'required|string|max:20',
            'support_email' => 'required|email|max:255',
            'description' => 'nullable|string',
            'logo_path' => 'nullable|string',
            'favicon_path' => 'nullable|string',
            'version' => 'nullable|string|max:50',
            'app_name' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $profile = Profile::findOrFail($id);
            $data = $validator->validated();

            // Update logo jika ada yang baru
            if ($request->logo_path && strpos($request->logo_path, 'data:image') === 0) {
                // Hapus logo lama jika ada
                if ($profile->logo_path) {
                    Storage::disk('public')->delete($profile->logo_path);
                }
                $logoPath = $this->saveBase64Image($request->logo_path, 'company/logos');
                $data['logo_path'] = $logoPath;
            } else {
                $data['logo_path'] = $profile->logo_path;
            }

            // Update favicon jika ada yang baru
            if ($request->favicon_path && strpos($request->favicon_path, 'data:image') === 0) {
                // Hapus favicon lama jika ada
                if ($profile->favicon_path) {
                    Storage::disk('public')->delete($profile->favicon_path);
                }
                $faviconPath = $this->saveBase64Image($request->favicon_path, 'company/favicons');
                $data['favicon_path'] = $faviconPath;
            } else {
                $data['favicon_path'] = $profile->favicon_path;
            }

            $profile->update($data);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Profile berhasil diupdate',
                'data' => $profile->fresh()
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hapus profile berdasarkan ID
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $profile = Profile::findOrFail($id);

            // Hapus file logo dan favicon jika ada
            if ($profile->logo_path) {
                Storage::disk('public')->delete($profile->logo_path);
            }
            if ($profile->favicon_path) {
                Storage::disk('public')->delete($profile->favicon_path);
            }

            $profile->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Profile berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset seluruh data profile
     */
    public function reset()
    {
        try {
            DB::beginTransaction();

            // Hapus semua file logo dan favicon
            $profiles = Profile::all();
            foreach ($profiles as $profile) {
                if ($profile->logo_path) {
                    Storage::disk('public')->delete($profile->logo_path);
                }
                if ($profile->favicon_path) {
                    Storage::disk('public')->delete($profile->favicon_path);
                }
            }

            Profile::truncate();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Semua profile berhasil direset'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal mereset data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Statistik profile
     */
    public function stats()
    {
        try {
            $stats = [
                'total' => Profile::count(),
                'with_logo' => Profile::whereNotNull('logo_path')->count(),
                'with_favicon' => Profile::whereNotNull('favicon_path')->count(),
                'with_description' => Profile::whereNotNull('description')->count()
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik: ' . $e->getMessage()
            ], 500);
        }
    }
}