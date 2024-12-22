<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Profession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class UtilityController extends Controller
{
    public function displayImage($path, $imageName)
    {
        $path = storage_path('app/public/images/' . $path . '/' . $imageName);
        if (!File::exists($path)) {
            abort(404);
        }

        $file = File::get($path);
        $type = File::mimeType($path);

        $respose = Response::make($file, 200);
        $respose->header('Content-Type', $type);

        return $respose;
    }

    public function allServant()
    {
        $datas = User::with(['roles', 'servantDetails'])
            ->whereHas('roles', function ($query) {
                $query->where('name', 'pembantu');
            })->where('is_active', true)->whereHas('servantDetails', function ($query) {
                $query->where('working_status', false);
            })->get();

            $professions = Profession::all();

        return view('cms.servant.index', compact(['datas', 'professions']));
    }

    public function showServant(string $id)
    {
        $data = User::findOrFail($id);

        return view('cms.servant.partials.detail', compact('data'));
    }
}
