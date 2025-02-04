<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Blog</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
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
                        <label for="tags">Tags <span class="text-danger">*</span></label>
                        <input type="text" id="tags" name="tags" class="form-control"
                            placeholder="Tambahkan tag" required>
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
            $('#createModal').on('shown.bs.modal', function() {
                $('#content-editor').summernote({
                    placeholder: 'Tulis deskripsi di sini...',
                    tabsize: 2,
                    height: 150,
                    toolbar: [
                        ['font', ['bold', 'italic', 'underline']],
                        ['para', ['ul', 'ol']],
                    ]
                });

                var input = document.querySelector('#tags');
                if (!input.tagify) {
                    var tagify = new Tagify(input, {
                        delimiters: ",",
                        maxTags: 10,
                        dropdown: {
                            enabled: 0
                        }
                    });
                    tagify.addTags(["Pembantu", "Blog", "Sipembantu"]);
                }
            });

            $('#createModal').on('hidden.bs.modal', function() {
                $('#content-editor').summernote('destroy');
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

            updatePreview('image', 'imagePreviewContainer');
        });
    </script>
@endpush
