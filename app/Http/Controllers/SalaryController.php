<?php

namespace App\Http\Controllers;

use App\Models\Salary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;

class SalaryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $datas = Salary::all();

        return view('cms.salary.index', compact('datas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'adds_client' => ['required', 'string'],
            'bpjs_client' => ['required', 'boolean'],
            'adds_mitra' => ['required', 'string'],
            'bpjs_mitra' => ['required', 'boolean'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('toast_error', $validator->messages()->all()[0]);
        }

        $datas = $validator->validated();

        try {
            DB::transaction(function () use ($datas) {
                Salary::create([
                    'adds_client' => $datas['adds_client'],
                    'bpjs_client' => $datas['bpjs_client'],
                    'adds_mitra' => $datas['adds_mitra'],
                    'bpjs_mitra' => $datas['bpjs_mitra'],
                ]);
            });

            Alert::success('Berhasil', 'Pengaturan gaji berhasil ditambahkan!');
            return redirect()->route('salaries.index');
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
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'adds_client' => ['required', 'string'],
            'bpjs_client' => ['required', 'boolean'],
            'adds_mitra' => ['required', 'string'],
            'bpjs_mitra' => ['required', 'boolean'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('toast_error', $validator->messages()->all()[0]);
        }

        $datas = $validator->validated();
        $update = Salary::findOrFail($id);

        try {
            DB::transaction(function () use ($datas, $update) {
                $update->update([
                    'adds_client' => $datas['adds_client'],
                    'bpjs_client' => $datas['bpjs_client'],
                    'adds_mitra' => $datas['adds_mitra'],
                    'bpjs_mitra' => $datas['bpjs_mitra'],
                ]);
            });

            Alert::success('Berhasil', 'Pengaturan gaji berhasil dirubah!');
            return redirect()->route('salaries.index');
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
    public function destroy(string $id)
    {
        $data = Salary::findOrFail($id);

        if (optional($data->application)->exists()) {
            return redirect()->route('salaries.index')->with('toast_error', 'Pengaturan gaji sedang digunakan!');
        }

        try {
            DB::transaction(function () use ($data) {
                $data->delete();
            });

            Alert::success('Berhasil', 'Pengaturan gaji berhasil dihapus!');
            return redirect()->route('salaries.index');
        } catch (\Throwable $th) {
            $data = [
                'message' => $th->getMessage(),
                'status' => 400
            ];

            return view('cms.error', compact('data'));
        }
    }
}
