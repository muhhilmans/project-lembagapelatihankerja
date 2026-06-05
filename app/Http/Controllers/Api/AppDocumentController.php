<?php

namespace App\Http\Controllers\Api;

use App\Models\AppDocument;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Log;

class AppDocumentController extends Controller
{
    use ApiResponse;

    public function show(string $type)
    {
        $allowed = ['keamanan', 'bantuan', 'tentang'];

        if (!in_array($type, $allowed)) {
            return $this->errorResponse('Tipe dokumen tidak valid.', [], 422);
        }

        try {
            $doc = AppDocument::where('type', $type)->first();

            if (!$doc || !$doc->file_path) {
                return $this->errorResponse('Dokumen belum tersedia.', [], 404);
            }

            return $this->successResponse(['file_path' => $doc->file_path], 'Dokumen berhasil dimuat.');
        } catch (\Throwable $th) {
            Log::error("Error AppDocument show [{$type}]: {$th->getMessage()}");
            return $this->errorResponse('Gagal memuat dokumen.', [], 500);
        }
    }
}
