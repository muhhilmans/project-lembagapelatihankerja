<div class="modal fade" id="editModal-{{ $data->id }}" tabindex="-1" role="dialog"
    aria-labelledby="editModalLabel-{{ $data->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel-{{ $data->id }}">Ubah Lowongan</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form method="POST" action="{{ route('vacancies.update', $data->id) }}">
                @csrf
                @method('PUT')
                <div class="modal-body text-left">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="title-{{ $data->id }}">Judul <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title-{{ $data->id }}" name="title"
                                    value="{{ $data->title }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="closing_date-{{ $data->id }}">Batas Lamaran <span
                                        class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="closing_date-{{ $data->id }}"
                                    name="closing_date" value="{{ $data->closing_date }}" required>
                            </div>
                        </div>
                    </div>

                    @hasrole('superadmin|admin')
                        <div class="form-group">
                            <label for="user_id-{{ $data->id }}">Majikan <span class="text-danger">*</span></label>
                            <select name="user_id" id="user_id-{{ $data->id }}" class="form-control" required>
                                <option value="">Pilih Majikan...</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}"
                                        {{ $user->id == $data->user_id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endhasrole

                    @hasrole('majikan')
                        <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                    @endhasrole

                    <div class="form-group">
                        <label for="limit">Batas Pelamar <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="limit" name="limit" value="{{ $data->limit }}" required>
                    </div>

                    <div class="form-group">
                        <label for="description-editor-{{ $data->id }}">Deskripsi <span
                                class="text-danger">*</span></label>
                        <textarea id="description-editor-{{ $data->id }}" name="description" class="form-control" required>{{ $data->description }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="requirements-editor-{{ $data->id }}">Spesifikasi <span
                                class="text-danger">*</span></label>
                        <textarea id="requirements-editor-{{ $data->id }}" name="requirements" class="form-control" required>{{ $data->requirements }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="benefits-editor-{{ $data->id }}">Keuntungan</label>
                        <textarea id="benefits-editor-{{ $data->id }}" name="benefits" class="form-control">{{ $data->benefits }}</textarea>
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
            // Inisialisasi Summernote pada saat modal dibuka
            $('#editModal-{{ $data->id }}').on('shown.bs.modal', function() {
                $('#description-editor-{{ $data->id }}').summernote({
                    placeholder: 'Tulis deskripsi di sini...',
                    tabsize: 2,
                    height: 150,
                    toolbar: [
                        ['font', ['bold', 'italic', 'underline']],
                        ['para', ['ul']],
                    ]
                });

                $('#requirements-editor-{{ $data->id }}').summernote({
                    placeholder: 'Tulis spesifikasi di sini...',
                    tabsize: 2,
                    height: 150,
                    toolbar: [
                        ['font', ['bold', 'italic', 'underline']],
                        ['para', ['ul']],
                    ]
                });

                $('#benefits-editor-{{ $data->id }}').summernote({
                    placeholder: 'Tulis keuntungan di sini...',
                    tabsize: 2,
                    height: 150,
                    toolbar: [
                        ['font', ['bold', 'italic', 'underline']],
                        ['para', ['ul']],
                    ]
                });
            });

            // Hapus Summernote ketika modal ditutup untuk mencegah konflik
            $('#editModal-{{ $data->id }}').on('hidden.bs.modal', function() {
                $('#description-editor-{{ $data->id }}').summernote('destroy');
                $('#requirements-editor-{{ $data->id }}').summernote('destroy');
                $('#benefits-editor-{{ $data->id }}').summernote('destroy');
            });
        });
    </script>
@endpush
