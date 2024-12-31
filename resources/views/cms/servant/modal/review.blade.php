<div class="modal fade" id="reviewModal-{{ $data->id }}" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Berhentikan Pekerja</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            @if ($data->employe_id != null)
                <form method="POST" action="{{ route('applicant-hire.change', $data->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-body text-left">
                        <input type="text" name="status" value="review" hidden>

                        <div class="form-group">
                            <label for="notes">Catatan <span class="text-danger">*Isi dengan alasan
                                    diberhentikan</span></label>
                            <textarea id="review-notes-editor" name="notes" class="form-control" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                        <button class="btn btn-primary" type="submit">Simpan</button>
                    </div>
                </form>
            @else
                <form method="POST"
                    action="{{ route('applicant-indie.change', ['vacancy' => $data->vacancy_id, 'user' => $data->servant_id]) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-body text-left">
                        <input type="text" name="status" value="review" hidden>

                        <div class="form-group">
                            <label for="notes">Catatan <span class="text-danger">*Isi dengan alasan
                                diberhentikan</span></label>
                            <textarea id="review-notes-editor" name="notes" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                        <button class="btn btn-primary" type="submit">Simpan</button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>

@push('custom-script')
    <script>
        $(document).ready(function() {
            $('#reviewModal-{{ $data->id }}').on('shown.bs.modal', function() {
                $('#review-notes-editor').summernote({
                    placeholder: 'Tulis deskripsi di sini...',
                    tabsize: 2,
                    height: 150,
                    toolbar: [
                        ['font', ['bold', 'italic', 'underline']],
                        ['para', ['ul']],
                    ]
                });
            });

            $('#reviewModal-{{ $data->id }}').on('hidden.bs.modal', function() {
                $('#review-notes-editor').summernote('destroy');
            });
        });
    </script>
@endpush
