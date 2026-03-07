@extends('cms.layouts.main', ['title' => 'Tambah Blog'])

@section('content')
    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-baseline">
        <h1 class="h3 mb-4 text-gray-800">Tambah Blog</h1>
        <div class="d-flex">
            <a href="{{ route('blogs.index') }}" class="btn btn-sm btn-secondary shadow"><i
                    class="fas fa-fw fa-arrow-left"></i></a>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <form method="POST" action="{{ route('blogs.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="title">Judul <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="category">Kategori <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="category" name="category" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="tags">Tags <span class="text-danger">*</span></label><br>
                        <input type="text" id="tags" name="tags" class="tags-look" placeholder="Tambahkan tag"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="content">Konten <span class="text-danger">*</span></label>
                        <textarea id="content-editor" name="content" class="form-control" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="image">Foto <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inpoImage">Upload</span>
                            </div>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="image" name="image"
                                    aria-describedby="inpoImage" accept="image/*" required>
                                <label class="custom-file-label" for="image">Choose file</label>
                            </div>
                        </div>
                        <div class="mt-3" id="imagePreviewContainer"></div>
                    </div>

                    {{-- Schedule Publish Section --}}
                    <div class="card bg-light border mt-3 mb-3">
                        <div class="card-body">
                            <h6 class="card-title font-weight-bold"><i class="fas fa-clock"></i> Jadwal Publish</h6>
                            <div class="form-group mb-2">
                                <label for="publish_type">Tipe Publish <span class="text-danger">*</span></label>
                                <select class="form-control" id="publish_type" name="publish_type" required>
                                    <option value="now" selected>Publish Sekarang</option>
                                    <option value="schedule">Jadwalkan</option>
                                </select>
                            </div>
                            <div id="schedule-fields" style="display: none;">
                                <div class="form-row">
                                    <div class="form-group col-md-6 mb-0">
                                        <label for="published_date">Tanggal Publish <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="published_date" name="published_date">
                                    </div>
                                    <div class="form-group col-md-6 mb-0">
                                        <label for="published_time">Jam Publish <span class="text-danger">*</span></label>
                                        <input type="time" class="form-control" id="published_time" name="published_time">
                                    </div>
                                </div>
                                <small class="text-muted mt-1 d-block">Blog akan otomatis ter-publish pada waktu yang ditentukan.</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('blogs.index') }}" class="btn btn-secondary">Batal</a>
                    <button class="btn btn-primary" type="submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('custom-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/summernote/summernote-bs4.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css" rel="stylesheet">
    <style>
        .tags-look {
            display: flex;
            flex-wrap: wrap;
            gap: 2px;
            border-radius: 5px;
            padding: 8px;
            min-height: 40px;
            max-width: 100%;
        }
    </style>
@endpush

@push('custom-script')
    <script src="{{ asset('assets/vendor/summernote/summernote-bs4.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
    <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.polyfills.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#content-editor').summernote({
                placeholder: 'Tulis deskripsi di sini...',
                tabsize: 2,
                height: 150,
                toolbar: [
                    ['style', ['bold', 'italic', 'underline']],
                    ['font', ['strikethrough', 'superscript', 'subscript']],
                    ['para', ['ul', 'ol']],
                    ['table', ['table']],
                    ['insert', ['link']],
                    ['view', ['fullscreen', 'help']],
                ]
            });

            var input = document.querySelector('#tags');
            if (!input.tagify) {
                var tagify = new Tagify(input, {
                    delimiters: ",",
                    maxTags: 10,
                    dropdown: {
                        maxItems: 20,
                        classname: 'tags-look',
                        enabled: 0,
                        closeOnSelect: false
                    }
                });
                tagify.addTags(["Pembantu", "Blog", "Sipembantu"]);
            }

            function updatePreview(inputId, previewContainerId) {
                const inputFile = document.getElementById(inputId);
                const previewContainer = document.getElementById(previewContainerId);

                inputFile.addEventListener('change', function() {
                    const file = this.files[0];

                    const label = this.nextElementSibling;
                    label.textContent = file ? file.name : 'Choose file';

                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            let previewImage = previewContainer.querySelector('img');
                            if (!previewImage) {
                                previewImage = document.createElement('img');
                                previewImage.className = 'img-fluid rounded';
                                previewImage.style.maxWidth = '100px';
                                previewContainer.appendChild(previewImage);
                            }
                            previewImage.src = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    } else {
                        previewContainer.innerHTML = '';
                    }
                });
            }

            updatePreview('image', 'imagePreviewContainer');

            // Toggle schedule fields
            $('#publish_type').on('change', function() {
                if ($(this).val() === 'schedule') {
                    $('#schedule-fields').slideDown(200);
                    $('#published_date').attr('required', true);
                    $('#published_time').attr('required', true);
                } else {
                    $('#schedule-fields').slideUp(200);
                    $('#published_date').removeAttr('required');
                    $('#published_time').removeAttr('required');
                }
            });
        });
    </script>
@endpush
