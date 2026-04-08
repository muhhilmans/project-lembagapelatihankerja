<?php

namespace App\Http\Controllers\Api;

use App\Models\Garansi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponse;

class GaransiController extends Controller
{
    use ApiResponse;

    public function index()
    {
        try {
            $garansis = Garansi::latest()->get();
            return $this->successResponse($garansis, 'Data master garansi berhasil dimuat.');
        } catch (\Throwable $th) {
            Log::error("Error Garansi index: {$th->getMessage()}");
            return $this->errorResponse('Gagal memuat data garansi.', [], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'duration' => 'required|integer|min:1', // durasi dalam bulan
            'max_replacements' => 'required|integer|min:0', // batas penukaran
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        try {
            $data = $validator->validated();
            $data['is_active'] = $request->input('is_active', true);

            $garansi = Garansi::create($data);
            return $this->successResponse($garansi, 'Opsi garansi baru berhasil ditambahkan.', 201);
        } catch (\Throwable $th) {
            Log::error("Error Garansi store: {$th->getMessage()}");
            return $this->errorResponse('Terjadi kesalahan saat menyimpan opsi garansi.', [], 500);
        }
    }

    public function show($id)
    {
        $garansi = Garansi::find($id);

        if (!$garansi) {
            return $this->errorResponse('Data garansi tidak ditemukan.', [], 404);
        }

        return $this->successResponse($garansi, 'Detail opsi garansi.');
    }

    public function update(Request $request, $id)
    {
        $garansi = Garansi::find($id);

        if (!$garansi) {
            return $this->errorResponse('Data garansi tidak ditemukan.', [], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'duration' => 'required|integer|min:1',
            'max_replacements' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        try {
            $garansi->update($validator->validated());
            return $this->successResponse($garansi, 'Opsi garansi berhasil diperbarui.');
        } catch (\Throwable $th) {
            Log::error("Error Garansi update: {$th->getMessage()}");
            return $this->errorResponse('Terjadi kesalahan saat memperbarui opsi garansi.', [], 500);
        }
    }

    public function destroy($id)
    {
        $garansi = Garansi::find($id);

        if (!$garansi) {
            return $this->errorResponse('Data garansi tidak ditemukan.', [], 404);
        }

        try {
            $garansi->delete();
            return $this->successResponse(null, 'Opsi garansi berhasil dihapus.');
        } catch (\Throwable $th) {
            Log::error("Error Garansi destroy: {$th->getMessage()}");
            return $this->errorResponse('Gagal menghapus opsi garansi.', [], 500);
        }
    }
}
