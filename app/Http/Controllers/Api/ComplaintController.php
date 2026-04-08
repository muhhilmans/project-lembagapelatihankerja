<?php

namespace App\Http\Controllers\Api;

use App\Models\Pengaduan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponse;

class ComplaintController extends Controller
{
    use ApiResponse;

    /**
     * Mengambil daftar semua pengaduan untuk Super Admin dengan Eager Loading & Urut Urgensi.
     */
    public function index(Request $request)
    {
        try {
            // Eager Loading: Mengatasi N+1 Query dengan memuat relasi sekaligus
            $query = Pengaduan::with([
                'reporter.roles', // Relasi ke pelapor beserta role-nya
                'reportedUser.roles', // Relasi ke terlapor beserta role-nya
            ]);

            // Filter pencarian berdasarkan nama pelapor atau deskripsi (opsional)
            $search = $request->input('search');
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('reporter', function($subQ) use ($search) {
                        $subQ->where('name', 'like', "%{$search}%");
                    })->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Filter spesifik status jika dikirim dari frontend
            $status = $request->input('status');
            if ($status) {
                $query->where('status', $status);
            }

            // Sortir Otomatis: Urgensi Tertinggi di atas, lalu status Open, lalu terbaru
            $query->orderByRaw("
                    CASE urgency_level
                        WHEN 'CRITICAL' THEN 1
                        WHEN 'HIGH' THEN 2
                        WHEN 'MEDIUM' THEN 3
                        WHEN 'LOW' THEN 4
                        ELSE 5
                    END ASC
                ")
                ->orderByRaw("
                    CASE status
                        WHEN 'open' THEN 1
                        WHEN 'investigating' THEN 2
                        WHEN 'resolved' THEN 3
                        ELSE 4
                    END ASC
                ")
                ->latest();

            $complaints = $query->paginate(10);

            if ($complaints->isEmpty()) {
                return $this->successResponse([], 'Belum ada data pengaduan.');
            }

            return $this->successResponse([
                'data' => $complaints->items(),
                'pagination' => [
                    'current_page' => $complaints->currentPage(),
                    'per_page' => $complaints->perPage(),
                    'total' => $complaints->total(),
                    'last_page' => $complaints->lastPage(),
                ]
            ], 'Data pengaduan berhasil dimuat.');

        } catch (\Throwable $th) {
            Log::error("Error get complaints: {$th->getMessage()}");
            return $this->errorResponse('Terjadi kesalahan saat memuat data pengaduan.', [], 500);
        }
    }

    /**
     * Menampilkan detail satu pengaduan.
     */
    public function show($id)
    {
        try {
            $complaint = Pengaduan::with([
                'reporter.roles',
                'reportedUser.roles',
            ])->find($id);

            if (!$complaint) {
                return $this->errorResponse('Data pengaduan tidak ditemukan.', [], 404);
            }

            return $this->successResponse($complaint, 'Detail pengaduan.');
        } catch (\Throwable $th) {
            Log::error("Error show complaint: {$th->getMessage()}");
            return $this->errorResponse('Terjadi kesalahan saat memuat detail pengaduan.', [], 500);
        }
    }

    /**
     * Mengeksekusi penyelesaian laporan pengaduan (Akses Admin/Super Admin).
     */
    public function resolve(Request $request, $id)
    {
        $complaint = Pengaduan::find($id);

        if (!$complaint) {
            return $this->errorResponse('Data pengaduan tidak ditemukan.', [], 404);
        }

        if ($complaint->status === 'resolved') {
            return $this->errorResponse('Pengaduan ini sudah diselesaikan sebelumnya.', [], 400);
        }

        // Validasi Ketat Catatan Penyelesaian (Mandatory > 10 Karakter)
        $validator = Validator::make($request->all(), [
            'resolution_notes' => 'required|string|min:10',
        ], [
            'resolution_notes.required' => 'Catatan penyelesaian wajib diisi.',
            'resolution_notes.min' => 'Catatan penyelesaian minimal harus 10 karakter untuk transparansi kepada pelapor.'
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        try {
            DB::beginTransaction();

            $complaint->update([
                'status' => 'resolved',
                'resolution_notes' => $request->resolution_notes,
                'resolved_by' => auth()->id(), // Men-track admin yang menyelesaikan
                'resolved_at' => now(),
            ]);

            DB::commit();

            return $this->successResponse($complaint, 'Pengaduan berhasil diselesaikan dan dicatat dalam sistem.');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error("Error resolve complaint: {$th->getMessage()}");
            return $this->errorResponse('Terjadi kesalahan saat menyelesaikan pengaduan.', [], 500);
        }
    }
}
