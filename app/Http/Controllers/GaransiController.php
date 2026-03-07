<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Garansi;

class GaransiController extends Controller
{
    public function index()
    {
        $garansis = Garansi::latest()->get();
        return view('admin.garansi.index', compact('garansis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'max_replacements' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
        ]);

        Garansi::create([
            'name' => $request->name,
            'max_replacements' => $request->max_replacements,
            'price' => $request->price,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('garansis.index')->with('success', 'Skema garansi berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'max_replacements' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
        ]);

        $garansi = Garansi::findOrFail($id);
        $garansi->update([
            'name' => $request->name,
            'max_replacements' => $request->max_replacements,
            'price' => $request->price,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('garansis.index')->with('success', 'Skema garansi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $garansi = Garansi::findOrFail($id);
        $garansi->delete();

        return redirect()->route('garansis.index')->with('success', 'Skema garansi berhasil dihapus.');
    }
}
