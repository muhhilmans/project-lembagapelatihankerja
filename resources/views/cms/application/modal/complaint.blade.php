<div class="modal fade" id="complaintModal-{{ $data->id }}" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Pengaduan Majikan</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form method="POST" action="{{ route('complaints.store') }}">
                @csrf
                <div class="modal-body text-left">
                    <input type="text" name="application_id" value="{{ $data->id }}" hidden>
                    <input type="text" name="servant_id" value="{{ $data->servant_id }}" hidden>
                    @if ($data->employe_id != null)
                        <input type="text" name="employe_id" value="{{ $data->employe_id }}" hidden>
                    @else
                        <input type="text" name="employe_id" value="{{ $data->vacancy->user->id }}" hidden>
                    @endif

                    <div class="form-group">
                        <label for="message">Pesan Aduan <span class="text-danger">*</span></label>
                        <textarea id="complaint-message-editor" name="message" class="form-control" required></textarea>
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
            $('#complaintModal-{{ $data->id }}').on('shown.bs.modal', function() {
                $('#complaint-message-editor').summernote({
                    placeholder: 'Tulis deskripsi di sini...',
                    tabsize: 2,
                    height: 150,
                    toolbar: [
                        ['font', ['bold', 'italic', 'underline']],
                        ['para', ['ul']],
                    ]
                });
            });

            $('#complaintModal-{{ $data->id }}').on('hidden.bs.modal', function() {
                $('#complaint-message-editor').summernote('destroy');
            });
        });
    </script>
@endpush
