<div class="modal fade" id="interviewModal-{{ $d->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Jadwalkan Interview</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form method="POST" action="{{ route('applicant-hire.change', $d->id) }}">
                @csrf
                @method('PUT')
                <div class="modal-body text-left">
                    <input type="text" name="status" value="interview" hidden>

                    <div class="form-group">
                        <label for="interview_link">Link Interview</label>
                        <input type="text" id="interview_link" name="interview_link" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="notes">Catatan <span class="text-danger">*Berikan waktu pasti interview</label>
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
        document.addEventListener('DOMContentLoaded', () => {
            const closingDateInput = document.getElementById('interview_date');
            const today = new Date();

            // Konversi ke timezone Indonesia (UTC+7)
            const utcOffset = 7 * 60 * 60 * 1000;
            const indonesiaTime = new Date(today.getTime() + (today.getTimezoneOffset() * 60 * 1000) + utcOffset);

            const year = indonesiaTime.getFullYear();
            const month = String(indonesiaTime.getMonth() + 1).padStart(2, '0');
            const date = String(indonesiaTime.getDate()).padStart(2, '0');
            const formattedDate = `${year}-${month}-${date}`;

            closingDateInput.setAttribute('min', formattedDate);
        });
        
        $(document).ready(function() {
            $('#interviewModal-{{ $d->id }}').on('shown.bs.modal', function () {
                $('#notes-editor').summernote({
                    placeholder: 'Tulis deskripsi di sini...',
                    tabsize: 2,
                    height: 150,
                    toolbar: [
                        ['font', ['bold', 'italic', 'underline']],
                        ['para', ['ul']],
                        // ['insert', ['link']],
                    ]
                });
            });

            $('#interviewModal-{{ $d->id }}').on('hidden.bs.modal', function () {
                $('#notes-editor').summernote('destroy');
            });
        });
    </script>
@endpush
