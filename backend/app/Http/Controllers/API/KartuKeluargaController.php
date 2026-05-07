<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\KartuKeluarga;
use App\Services\GoogleDriveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class KartuKeluargaController extends Controller
{
    protected $driveService;

    public function __construct()
    {
        $this->driveService = new GoogleDriveService();
    }

    public function index()
    {
        $kk = KartuKeluarga::latest()->paginate(12);
        return response()->json(['success' => true, 'data' => $kk]);
    }

    public function show($id)
    {
        $kk = KartuKeluarga::findOrFail($id);
        return response()->json(['success' => true, 'data' => $kk]);
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'no_kk' => 'required|digits:16|unique:kartu_keluarga',
                'nama_kepala' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            $data = $request->except('foto');
            $data['anggota'] = $request->anggota ?? [];
            
            if (is_string($data['anggota'])) {
                $data['anggota'] = json_decode($data['anggota'], true);
            }

            // Upload foto ke Google Drive
            if ($request->hasFile('foto')) {
                $foto = $request->file('foto');
                $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9.]/', '_', $foto->getClientOriginalName());
                $uploadResult = $this->driveService->uploadFoto($foto, $filename);
                $data['foto_url'] = $uploadResult['url'];
                $data['foto_drive_id'] = $uploadResult['drive_id'];
            }

            $kk = KartuKeluarga::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Data KK berhasil ditambahkan',
                'data' => $kk
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $kk = KartuKeluarga::findOrFail($id);
            
            $validator = Validator::make($request->all(), [
                'no_kk' => 'required|digits:16|unique:kartu_keluarga,no_kk,' . $id,
                'nama_kepala' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            $data = $request->except('foto');
            $data['anggota'] = $request->anggota ?? [];
            
            if (is_string($data['anggota'])) {
                $data['anggota'] = json_decode($data['anggota'], true);
            }

            if ($request->hasFile('foto')) {
                // Hapus foto lama dari Drive
                if ($kk->foto_drive_id) {
                    $this->driveService->deleteFoto($kk->foto_drive_id);
                }
                
                $foto = $request->file('foto');
                $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9.]/', '_', $foto->getClientOriginalName());
                $uploadResult = $this->driveService->uploadFoto($foto, $filename);
                $data['foto_url'] = $uploadResult['url'];
                $data['foto_drive_id'] = $uploadResult['drive_id'];
            }

            $kk->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Data KK berhasil diupdate',
                'data' => $kk
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $kk = KartuKeluarga::findOrFail($id);
        
        if ($kk->foto_drive_id) {
            $this->driveService->deleteFoto($kk->foto_drive_id);
        }
        
        $kk->delete();
        
        return response()->json(['success' => true, 'message' => 'Data KK berhasil dihapus']);
    }

    public function stats()
    {
        $totalKK = KartuKeluarga::count();
        $totalJiwa = KartuKeluarga::all()->sum(function($kk) {
            return count($kk->anggota ?? []) + 1;
        });
        $totalFoto = KartuKeluarga::whereNotNull('foto_url')->count();
        $totalRT = KartuKeluarga::whereNotNull('rt')->distinct('rt')->count('rt');
        $byRT = KartuKeluarga::whereNotNull('rt')
                ->selectRaw('rt, count(*) as total')
                ->groupBy('rt')
                ->get();
        $recent = KartuKeluarga::latest()->take(5)->get();

        return response()->json([
            'success' => true,
            'data' => [
                'totalKK' => $totalKK,
                'totalJiwa' => $totalJiwa,
                'totalFoto' => $totalFoto,
                'totalRT' => $totalRT,
                'byRT' => $byRT,
                'recent' => $recent
            ]
        ]);
    }
}