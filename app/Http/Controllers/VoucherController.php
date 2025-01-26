<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;

class VoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $datas = Voucher::all();

        return view('cms.voucher.index', compact('datas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => ['required', 'string', 'max:255'],
            'people_used' => ['nullable', 'integer'],
            'time_used' => ['required', 'integer'],
            'expired_date' => 'nullable|date|after:today',
            'discount' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('toast_error', $validator->messages()->all()[0]);
        }

        $data = $validator->validated();

        try {
            DB::transaction(function () use ($data) {
                Voucher::create([
                    'code' => $data['code'],
                    'people_used' => $data['people_used'],
                    'time_used' => $data['time_used'],
                    'expired_date' => $data['expired_date'],
                    'discount' => $data['discount'],
                    'is_active' => true
                ]);
            });

            Alert::success('Berhasil', 'Berhasil membuat voucher!');
            return redirect()->route('vouchers.index');
        } catch (\Throwable $th) {
            $data = [
                'message' => $th->getMessage(),
                'status' => 400
            ];

            return view('cms.error', compact('data'));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'code' => ['required', 'string', 'max:255'],
            'people_used' => ['nullable', 'integer'],
            'time_used' => ['required', 'integer'],
            'expired_date' => 'nullable|date|after:today',
            'discount' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('toast_error', $validator->messages()->all()[0]);
        }

        $data = $validator->validated();

        try {
            DB::transaction(function () use ($data, $id) {
                $voucher = Voucher::findOrFail($id);

                $voucher->update([
                    'code' => $data['code'],
                    'people_used' => $data['people_used'],
                    'time_used' => $data['time_used'],
                    'expired_date' => $data['expired_date'],
                    'discount' => $data['discount'],
                ]);
            });

            Alert::success('Berhasil', 'Berhasil mengubah voucher!');
            return redirect()->route('vouchers.index');
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
        try {
            $voucher = Voucher::findOrFail($id);

            if ($voucher->workerSalary()->where('voucher_id', $id)->exists()) {
                return redirect()->route('vouchers.index')->with('toast_error', 'Voucher telah digunakan dan tidak dapat dihapus!');
            }

            $voucher->delete();

            Alert::success('Berhasil', 'Berhasil menghapus voucher!');
            return redirect()->route('vouchers.index');
        } catch (\Throwable $th) {
            $data = [
                'message' => $th->getMessage(),
                'status' => 400
            ];

            return view('cms.error', compact('data'));
        }
    }

    public function changeStatus(Request $request, $id)
    {
        $data = Voucher::findOrFail($id);

        try {
            DB::transaction(function () use ($data) {
                $data->update([
                    'is_active' => !$data->is_active
                ]);
            });

            Alert::success('Berhasil', 'Berhasil mengubah status voucher!');
            return redirect()->route('vouchers.index');
        } catch (\Throwable $th) {
            $data = [
                'message' => $th->getMessage(),
                'status' => 400
            ];

            return view('cms.error', compact('data'));
        }
    }
}
