<?php

namespace App\Http\Controllers;

use App\Models\Profession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;

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
        ]);

        if ($validator->fails()) {
            return redirect()->route('professions.index')->with('toast_error', $validator->messages()->all()[0])->withInput();
        }

        $data = $validator->validated();

        try {
            DB::transaction(function () use ($data) {
                Profession::create([
                    'name' => $data['name'],
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
        ]);

        if ($validator->fails()) {
            return redirect()->route('professions.index')->with('toast_error', $validator->messages()->all()[0])->withInput();
        }

        $data = $validator->validated();

        try {
            DB::transaction(function () use ($data, $dataUpdate) {
                $dataUpdate->update([
                    'name' => $data['name'],
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

        $data->delete();

        return redirect()->route('professions.index')->with('success', 'Profesi berhasil dihapus!');
    }
}
