<div class="modal fade" id="acceptModal-{{ $d->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Proses Pelamar</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form method="POST" action="{{ route('vacancies.change', ['vacancy' => $d->vacancy_id, 'user' => $d->servant_id]) }}">
                @csrf
                @method('PUT')
                <div class="modal-body text-left">
                    <input type="text" name="status" value="interview" hidden>

                    <div class="form-group">
                        <label for="interview_date">Tanggal Interview <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="interview_date" name="interview_date" required>
                    </div>

                    <div class="form-group">
                        <label for="notes">Catatan</label>
                        <textarea id="notes-editor" name="notes" class="form-control"></textarea>
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
            $('#acceptModal-{{ $d->id }}').on('shown.bs.modal', function () {
                $('#notes-editor').summernote({
                    placeholder: 'Tulis deskripsi di sini...',
                    tabsize: 2,
                    height: 150,
                    toolbar: [
                        ['font', ['bold', 'italic', 'underline', 'clear']],
                        ['para', ['ul']],
                    ]
                });
            });

            $('#acceptModal-{{ $d->id }}').on('hidden.bs.modal', function () {
                $('#notes-editor').summernote('destroy');
            });
        });
    </script>
@endpush
