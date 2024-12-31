<div class="modal fade" id="laidoffModal-{{ $data->id }}" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Setuju Memberhentikan Pekerja</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            @if ($data->employe_id != null)
                <form method="POST" action="{{ route('applicant-hire.change', $data->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-body text-left">
                        <input type="text" name="status" value="laidoff" hidden>

                        <div class="form-group">
                            <label for="work_end_date">Tanggal Diberhentikan <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="work_end_date" name="work_end_date" required>
                        </div>

                        {{-- <div class="form-group">
                            <label for="notes">Catatan <span class="text-danger">*Isi dengan alasan
                                    diberhentikan</span></label>
                            <textarea id="laidoff-notes-editor" name="notes" class="form-control" required></textarea>
                        </div> --}}
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
                        <input type="text" name="status" value="laidoff" hidden>

                        {{-- <div class="form-group">
                            <label for="notes">Catatan <span class="text-danger">*Isi dengan alasan
                                diberhentikan</span></label>
                            <textarea id="laidoff-notes-editor" name="notes" class="form-control"></textarea>
                        </div> --}}
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
            $('#laidoffModal-{{ $data->id }}').on('shown.bs.modal', function() {
                $('#laidoff-notes-editor').summernote({
                    placeholder: 'Tulis deskripsi di sini...',
                    tabsize: 2,
                    height: 150,
                    toolbar: [
                        ['font', ['bold', 'italic', 'underline']],
                        ['para', ['ul']],
                    ]
                });
            });

            $('#laidoffModal-{{ $data->id }}').on('hidden.bs.modal', function() {
                $('#laidoff-notes-editor').summernote('destroy');
            });
        });
    </script>
@endpush
