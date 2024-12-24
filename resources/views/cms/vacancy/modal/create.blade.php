<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Lowongan</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form method="POST" action="{{ route('vacancies.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="title">Judul <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="closing_date">Batas Lamaran <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="closing_date" name="closing_date" required>
                            </div>
                        </div>
                    </div>

                    @hasrole('superadmin|admin')
                        <div class="form-group">
                            <label for="user_id">Majikan <span class="text-danger">*</span></label>
                            <select name="user_id" id="user_id" class="form-control" required>
                                <option value="">Pilih Majikan...</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endhasrole

                    @hasrole('majikan')
                        <input type="text" name="user_id" value="{{ $users->id }}" hidden>
                    @endhasrole

                    <div class="form-group">
                        <label for="description">Deskripsi <span class="text-danger">*</span></label>
                        <textarea id="description-editor" name="description" class="form-control" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="requirements">Spesifikasi <span class="text-danger">*</span></label>
                        <textarea id="requirements-editor" name="requirements" class="form-control" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="benefits">Keuntungan</label>
                        <textarea id="benefits-editor" name="benefits" class="form-control"></textarea>
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
            $('#createModal').on('shown.bs.modal', function () {
                $('#description-editor').summernote({
                    placeholder: 'Tulis deskripsi di sini...',
                    tabsize: 2,
                    height: 150,
                    toolbar: [
                        ['font', ['bold', 'italic', 'underline']],
                        ['para', ['ul']],
                    ]
                });

                $('#requirements-editor').summernote({
                    placeholder: 'Tulis spesifikasi di sini...',
                    tabsize: 2,
                    height: 150,
                    toolbar: [
                        ['font', ['bold', 'italic', 'underline']],
                        ['para', ['ul']],
                    ]
                });

                $('#benefits-editor').summernote({
                    placeholder: 'Tulis keuntungan di sini...',
                    tabsize: 2,
                    height: 150,
                    toolbar: [
                        ['font', ['bold', 'italic', 'underline']],
                        ['para', ['ul']],
                    ]
                });
            });

            $('#createModal').on('hidden.bs.modal', function () {
                $('#description-editor').summernote('destroy');
                $('#requirements-editor').summernote('destroy');
                $('#benefits-editor').summernote('destroy');
            });
        });
    </script>
@endpush
