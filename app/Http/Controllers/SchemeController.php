<?php

namespace App\Http\Controllers;

use App\Models\Scheme;
use Illuminate\Http\Request;

class SchemeController extends Controller
{
    public function index()
    {
        $schemes = Scheme::orderBy('created_at', 'desc')->get();
        return view('admin.schemes.index', compact('schemes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'scheme_name' => 'required|string|max:255',
            'client' => 'array',
            'mitra' => 'array',
        ]);

        Scheme::create([
            'name' => $request->scheme_name,
            'client_data' => array_values($request->client ?? []),
            'mitra_data' => array_values($request->mitra ?? []),
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Skema pembayaran berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'scheme_name' => 'required|string|max:255',
            'client' => 'array',
            'mitra' => 'array',
        ]);

        $scheme = Scheme::findOrFail($id);
        $scheme->update([
            'name' => $request->scheme_name,
            'client_data' => array_values($request->client ?? []),
            'mitra_data' => array_values($request->mitra ?? []),
        ]);

        return redirect()->back()->with('success', 'Skema pembayaran berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $scheme = Scheme::findOrFail($id);
        $scheme->delete();

        return redirect()->back()->with('success', 'Skema pembayaran berhasil dihapus.');
    }
}
