<div class="modal fade" id="verifyModal-{{ $data->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Verifikasi Pembantu</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form method="POST" action="{{ $data->vacancy_id ? route('vacancies.change', ['vacancy' => $data->vacancy_id, 'user' => $data->servant_id]) : route('applicant-hire.change', $data->id) }}">
                @csrf
                @method('PUT')
                <div class="modal-body text-left">
                    <input type="text" name="status" value="verify" hidden>
                    <div class="form-group">
                        <label for="notes">Catatan Verifikasi <span class="text-danger">*</span></label>
                        <textarea id="verify-notes-editor-{{ $data->id }}" name="notes" class="form-control" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <button class="btn btn-primary" type="submit">Yakin</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('custom-script')
    <script>
        $(document).ready(function() {
            $('#verifyModal-{{ $data->id }}').on('shown.bs.modal', function () {
                $('#verify-notes-editor-{{ $data->id }}').summernote({
                    placeholder: 'Tulis deskripsi di sini...',
                    tabsize: 2,
                    height: 150,
                    toolbar: [
                        ['font', ['bold', 'italic', 'underline']],
                        ['para', ['ul']],
                    ]
                });
            });

            $('#verifyModal-{{ $data->id }}').on('hidden.bs.modal', function () {
                $('#verify-notes-editor-{{ $data->id }}').summernote('destroy');
            });
        });
    </script>
@endpush