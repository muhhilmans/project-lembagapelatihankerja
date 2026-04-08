<?php

namespace App\Http\Controllers\Api;

use App\Models\Scheme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponse;

class SchemeController extends Controller
{
    use ApiResponse;

    public function index()
    {
        try {
            $schemes = Scheme::latest()->get();
            return $this->successResponse($schemes, 'Data master skema tarif berhasil dimuat.');
        } catch (\Throwable $th) {
            Log::error("Error Scheme index: {$th->getMessage()}");
            return $this->errorResponse('Gagal memuat data skema.', [], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'rules' => 'required|array', // Menerima payload JSON dari frontend
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        try {
            $data = $validator->validated();
            $data['is_active'] = $request->input('is_active', true);

            $scheme = Scheme::create($data);
            return $this->successResponse($scheme, 'Skema tarif baru berhasil ditambahkan.', 201);
        } catch (\Throwable $th) {
            Log::error("Error Scheme store: {$th->getMessage()}");
            return $this->errorResponse('Terjadi kesalahan saat menyimpan skema.', [], 500);
        }
    }

    public function show($id)
    {
        $scheme = Scheme::find($id);

        if (!$scheme) {
            return $this->errorResponse('Data skema tidak ditemukan.', [], 404);
        }

        return $this->successResponse($scheme, 'Detail skema tarif.');
    }

    public function update(Request $request, $id)
    {
        $scheme = Scheme::find($id);

        if (!$scheme) {
            return $this->errorResponse('Data skema tidak ditemukan.', [], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'rules' => 'required|array',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        try {
            $scheme->update($validator->validated());
            return $this->successResponse($scheme, 'Skema tarif berhasil diperbarui.');
        } catch (\Throwable $th) {
            Log::error("Error Scheme update: {$th->getMessage()}");
            return $this->errorResponse('Terjadi kesalahan saat memperbarui skema.', [], 500);
        }
    }

    public function destroy($id)
    {
        $scheme = Scheme::find($id);

        if (!$scheme) {
            return $this->errorResponse('Data skema tidak ditemukan.', [], 404);
        }

        try {
            $scheme->delete();
            return $this->successResponse(null, 'Skema tarif berhasil dihapus.');
        } catch (\Throwable $th) {
            Log::error("Error Scheme destroy: {$th->getMessage()}");
            return $this->errorResponse('Gagal menghapus skema. Pastikan data tidak sedang digunakan.', [], 500);
        }
    }
}
