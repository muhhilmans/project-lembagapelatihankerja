<div class="modal fade" id="interviewModal-{{ $data->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Jadwalkan Interview</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form method="POST" action="{{ $data->vacancy_id ? route('vacancies.change', ['vacancy' => $data->vacancy_id, 'user' => $data->servant_id]) : route('applicant-hire.change', $data->id) }}">
                @csrf
                @method('PUT')
                <div class="modal-body text-left">
                    <input type="text" name="status" value="interview" hidden>

                    <div class="form-group">
                        <label for="interview_link">Link Interview</label>
                        <input type="text" id="interview_link" name="interview_link" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="notes">Catatan <span class="text-danger">*Berikan waktu pasti interview</span></label>
                        <textarea id="notes-editor-interview-{{ $data->id }}" name="notes" class="form-control" required></textarea>
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
            $('#interviewModal-{{ $data->id }}').on('shown.bs.modal', function () {
                $('#notes-editor-interview-{{ $data->id }}').summernote({
                    placeholder: 'Tulis deskripsi di sini...',
                    tabsize: 2,
                    height: 150,
                    toolbar: [
                        ['font', ['bold', 'italic', 'underline']],
                        ['para', ['ul']],
                    ]
                });
            });

            $('#interviewModal-{{ $data->id }}').on('hidden.bs.modal', function () {
                $('#notes-editor-interview-{{ $data->id }}').summernote('destroy');
            });
        });
    </script>
@endpush
