<div class="modal fade" id="editModal-{{ $data->id }}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Blog</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form method="POST" action="{{ route('blogs.update', $data->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body text-left">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="title">Judul <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title"
                                value="{{ old('title', $data->title) }}" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="category">Kategori <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="category" name="category"
                                value="{{ old('category', $data->category) }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="tags">Tags <span class="text-danger">*</span></label>
                        <input type="text" id="edit-tags" name="tags" class="form-control"
                            placeholder="Tambahkan tag" value="{{ old('tags', $data->tags) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="content">Konten <span class="text-danger">*</span></label>
                        <textarea id="edit-content-editor-{{ $data->id }}" name="content" class="form-control" required>{!! old('content', $data->content) !!}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="image">Foto <span class="text-danger">*(Kosongkan jika tidak ingin
                                diubah)</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="editInpoImage">Upload</span>
                            </div>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="editImage" name="image"
                                    aria-describedby="editInpoImage" accept="image/*"  {{ $data->image ? null : 'required' }}>
                                <label class="custom-file-label" for="image">Choose file</label>
                            </div>
                        </div>
                        <div class="mt-3" id="editImagePreviewContainer">
                            @if (!empty($data->image))
                                <img id="imagePreview"
                                    src="{{ route('getImage', ['path' => 'blogs', 'imageName' => $data->image]) }}"
                                    alt="Foto" class="img-fluid rounded" style="max-height: 100px;">
                            @endif
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <button class="btn btn-primary" type="submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('custom-script')
    <script>
        $(document).ready(function() {
            $('#editModal-{{ $data->id }}').on('shown.bs.modal', function() {
                $('#edit-content-editor-{{ $data->id }}').summernote({
                    placeholder: 'Tulis deskripsi di sini...',
                    tabsize: 2,
                    height: 150,
                    toolbar: [
                        ['font', ['bold', 'italic', 'underline']],
                        ['para', ['ul', 'ol']],
                    ]
                });

                var input = document.querySelector('#edit-tags');
                if (!input.tagify) {
                    var tagify = new Tagify(input, {
                        delimiters: ",",
                        maxTags: 10,
                        dropdown: {
                            enabled: 0
                        }
                    });
                }
            });

            $('#editModal-{{ $data->id }}').on('hidden.bs.modal', function() {
                $('#edit-content-editor-{{ $data->id }}').summernote('destroy');
            });

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

            updatePreview('editImage', 'editImagePreviewContainer');
        });
    </script>
@endpush
