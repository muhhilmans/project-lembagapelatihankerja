<div class="modal fade" id="rejectModal-{{ $d->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Proses Pelamar</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form method="POST" action="{{ route('applicant-hire.change', $d->id) }}">
                @csrf
                @method('PUT')
                <div class="modal-body text-left">
                    <input type="text" name="status" value="rejected" hidden>

                    <div class="form-group">
                        <label for="notes">Catatan</label>
                        <textarea id="reject-notes-editor" name="notes" class="form-control"></textarea>
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
            $('#rejectModal-{{ $d->id }}').on('shown.bs.modal', function () {
                $('#reject-notes-editor').summernote({
                    placeholder: 'Tulis deskripsi di sini...',
                    tabsize: 2,
                    height: 150,
                    toolbar: [
                        ['font', ['bold', 'italic', 'underline']],
                        ['para', ['ul']],
                    ]
                });
            });

            $('#rejectModal-{{ $d->id }}').on('hidden.bs.modal', function () {
                $('#reject-notes-editor').summernote('destroy');
            });
        });
    </script>
@endpush
