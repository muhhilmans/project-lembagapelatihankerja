<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;

class ComplaintController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $datas = Complaint::all();

        return view('cms.complaint.index', compact('datas'));
    }

    public function changeStatus(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => ['required'],
            'file' => 'sometimes|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('toast_error', $validator->messages()->all()[0])->withInput();
        }

        $data = $validator->validated();

        $update = Complaint::findOrFail($id);

        try {
            DB::transaction(function () use ($data, $update, $request) {
                if ($data['status'] == 'accepted') {
                    $directory = "complaints/vacancy_{$update->servant->name}";
                    $fileName = "memorandum_{$update->servant->name}." . $request->file('file')->getClientOriginalExtension();

                    if (!Storage::exists($directory)) {
                        Storage::makeDirectory($directory);
                    }

                    if ($update->file && Storage::exists($update->file)) {
                        Storage::delete($update->file);
                    }

                    $path = $request->file('file')->storeAs($directory, $fileName);

                    $update->update([
                        'status' => $data['status'],
                        'file' => $path,
                    ]);
                } else {
                    $update->update([
                        'status' => $data['status'],
                    ]);
                }
            });

            Alert::success('Berhasil', 'Status keluhan berhasil diubah!');
            return redirect()->back();
        } catch (\Throwable $th) {
            $data = [
                'message' => $th->getMessage(),
                'status' => 400
            ];

            return redirect()->back()->with('toast_error', $data);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'application_id' => ['sometimes', 'exists:applications,id'],
            'servant_id' => ['required', 'exists:users,id'],
            'employe_id' => ['required', 'exists:users,id'],
            'message' => ['required'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('toast_error', $validator->messages()->all()[0])->withInput();
        }

        $data = $validator->validated();

        try {
            DB::transaction(function () use ($data) {
                Complaint::create([
                    'application_id' => $data['application_id'],
                    'servant_id' => $data['servant_id'],
                    'employe_id' => $data['employe_id'],
                    'message' => $data['message'],
                    'status' => 'pending',
                ]);
            });

            Alert::success('Berhasil', 'Berhasil mengirimkan keluhan!');
            return redirect()->back();
        } catch (\Throwable $th) {
            $data = [
                'message' => $th->getMessage(),
                'status' => 400
            ];

            return redirect()->back()->with('toast_error', $data);
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
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
