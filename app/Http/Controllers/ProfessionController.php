<?php

namespace App\Http\Controllers;

use App\Models\Profession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;

class ProfessionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $professions = Profession::all();

        return view('cms.profession.index', compact('professions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'file_draft' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->route('professions.index')->with('toast_error', $validator->messages()->all()[0])->withInput();
        }

        $data = $validator->validated();

        try {
            $directory = "professions";
            $fileName = "tc_{$data['name']}." . $request->file('file_draft')->getClientOriginalExtension();
            $storagePath = "public/{$directory}";

            if (!Storage::exists($storagePath)) {
                Storage::makeDirectory($storagePath);
            }

            $path = $request->file('file_draft')->storeAs($storagePath, $fileName);

            DB::transaction(function () use ($data, $fileName) {
                Profession::create([
                    'name' => $data['name'],
                    'file_draft' => $fileName
                ]);
            });

            Alert::success('Berhasil', 'Profesi berhasil ditambahkan!');
            return redirect()->route('professions.index');
        } catch (\Throwable $th) {
            $data = [
                'message' => $th->getMessage(),
                'status' => 400
            ];

            return view('cms.error', compact('data'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $dataUpdate = Profession::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'file_draft' => 'sometimes|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->route('professions.index')->with('toast_error', $validator->messages()->all()[0])->withInput();
        }

        $data = $validator->validated();

        try {
            $directory = "professions";
            $fileName = "tc_{$data['name']}." . $request->file('file_draft')->getClientOriginalExtension();
            $storagePath = "public/{$directory}";

            if (!Storage::exists($storagePath)) {
                Storage::makeDirectory($storagePath);
            }

            if ($dataUpdate->file_draft && Storage::exists("{$storagePath}{$dataUpdate->file_draft}")) {
                Storage::delete("{$storagePath}{$dataUpdate->file_draft}");
            }

            $path = $request->file('file_draft')->storeAs($storagePath, $fileName);

            DB::transaction(function () use ($data, $dataUpdate, $fileName) {
                $dataUpdate->update([
                    'name' => $data['name'],
                    'file_draft' => $fileName
                ]);
            });

            Alert::success('Berhasil', 'Profesi berhasil diperbarui!');
            return redirect()->route('professions.index');
        } catch (\Throwable $th) {
            $data = [
                'message' => $th->getMessage(),
                'status' => 400
            ];

            return view('cms.error', compact('data'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $data = Profession::findOrFail($request->data_id);

        if ($data->servant()->count() > 0) {
            return redirect()->route('professions.index')->with('toast_error', 'Profesi masih digunakan oleh pembantu!');
        }

        if ($data->file_draft) {
            $filePath = "public/professions/" . $data->file_draft;

            if (Storage::exists($filePath)) {
                Storage::delete($filePath);
            }
        }

        $data->delete();

        return redirect()->route('professions.index')->with('success', 'Profesi berhasil dihapus!');
    }
}
