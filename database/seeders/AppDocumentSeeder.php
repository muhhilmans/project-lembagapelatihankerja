<?php

namespace Database\Seeders;

use App\Models\AppDocument;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class AppDocumentSeeder extends Seeder
{
    public function run(): void
    {
        $files = [
            'keamanan' => 'Invoice-ORD-GX5P9ZB7.pdf',
            'bantuan'  => 'Invoice-ORD-GX5P9ZB7 (1).pdf',
            'tentang'  => 'Invoice-ORD-GX5P9ZB7 (2).pdf',
        ];

        foreach ($files as $type => $filename) {
            $src  = public_path("doc/{$filename}");
            $dest = "doc/{$filename}";

            if (file_exists($src)) {
                Storage::disk('public')->put($dest, file_get_contents($src));
            }

            AppDocument::updateOrCreate(
                ['type' => $type],
                ['file_path' => $dest]
            );
        }
    }
}
