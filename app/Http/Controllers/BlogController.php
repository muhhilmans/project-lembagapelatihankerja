<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Blog;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use RealRashid\SweetAlert\Facades\Alert;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $datas = Blog::orderBy('created_at', 'desc')->get();

        return view('cms.blog.index', compact('datas'));
    }

    public function create()
    {
        return view('cms.blog.partial.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'tags' => 'required|string',
            'category' => 'required|string',
            'publish_type' => 'required|in:now,schedule',
            'published_date' => 'required_if:publish_type,schedule|nullable|date',
            'published_time' => 'required_if:publish_type,schedule|nullable|date_format:H:i',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('toast_error', $validator->messages()->all()[0]);
        }

        $data = $validator->validated();

        // Determine status and published_at
        if ($data['publish_type'] === 'schedule') {
            $status = 'scheduled';
            $publishedAt = Carbon::parse($data['published_date'] . ' ' . $data['published_time']);
        } else {
            $status = 'published';
            $publishedAt = now();
        }

        try {
            DB::transaction(function () use ($data, $status, $publishedAt) {
                $photo = $data['image'] ?? null;
                $photoName = null;
                if (isset($photo)) {
                    $newFile = $photo;
                    $newFileName = "blog_" . Str::slug($data['title']) . "." . $photo->getClientOriginalExtension();
                    Storage::putFileAs("public/img/blogs", $newFile, $newFileName);
                    $photoName = $newFileName;
                }

                Blog::create([
                    'user_id' => auth()->user()->id,
                    'title' => $data['title'],
                    'slug' => Str::slug($data['title']),
                    'category' => $data['category'],
                    'content' => $data['content'],
                    'image' => $photoName,
                    'tags' => $data['tags'],
                    'status' => $status,
                    'published_at' => $publishedAt,
                ]);
            });

            Alert::success('Berhasil', 'Data berhasil ditambahkan!');
            return redirect()->route('blogs.index');
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
        $data = Blog::findOrFail($id);

        return view('cms.blog.partial.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $blog = Blog::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'tags' => 'required|string',
            'category' => 'required|string',
            'publish_type' => 'required|in:now,schedule',
            'published_date' => 'required_if:publish_type,schedule|nullable|date',
            'published_time' => 'required_if:publish_type,schedule|nullable|date_format:H:i',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('toast_error', $validator->messages()->all()[0]);
        }

        $data = $validator->validated();

        // Determine status and published_at
        if ($data['publish_type'] === 'schedule') {
            $status = 'scheduled';
            $publishedAt = Carbon::parse($data['published_date'] . ' ' . $data['published_time']);
        } else {
            $status = 'published';
            $publishedAt = now();
        }

        try {
            DB::transaction(function () use ($data, $blog, $status, $publishedAt) {
                $photoName = $blog->image;

                if (isset($data['image'])) {
                    $photo = $data['image'];
                    if ($blog->image && Storage::exists("public/img/blogs/{$blog->image}")) {
                        Storage::delete("public/img/blogs/{$blog->image}");
                    }

                    $newFileName = "blog_" . Str::slug($data['title']) . "." . $photo->getClientOriginalExtension();
                    Storage::putFileAs("public/img/blogs", $photo, $newFileName);
                    $photoName = $newFileName;
                }

                $blog->update([
                    'user_id' => auth()->user()->id,
                    'title' => $data['title'],
                    'slug' => Str::slug($data['title']),
                    'category' => $data['category'],
                    'content' => $data['content'],
                    'image' => $photoName,
                    'tags' => $data['tags'],
                    'status' => $status,
                    'published_at' => $publishedAt,
                ]);
            });

            Alert::success('Berhasil', 'Data berhasil diubah!');
            return redirect()->route('blogs.index');
        } catch (\Throwable $th) {
            $data = [
                'message' => $th->getMessage(),
                'status' => 400
            ];

            return redirect()->back()->with('toast_error', $data);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $data = Blog::findOrFail($request->data_id);

        try {
            DB::transaction(function () use ($data) {
                if ($data->image) {
                    $filePath = "public/img/blogs/" . $data->image;

                    if (Storage::exists($filePath)) {
                        Storage::delete($filePath);
                    }
                }

                $data->delete();
            });

            Alert::success('Berhasil', 'Data berhasil dihapus!');
            return redirect()->route('blogs.index');
        } catch (\Throwable $th) {
            $data = [
                'message' => $th->getMessage(),
                'status' => 400
            ];

            return view('cms.error', compact('data'));
        }
    }
}
