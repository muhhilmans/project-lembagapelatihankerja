<div class="modal fade" id="hireModal-{{ $data->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Kerjakan Pembantu</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form method="POST" action="{{ route('hire-servant', $data->id) }}">
                @csrf
                <div class="modal-body text-left">
                    <input type="text" name="status" value="interview" hidden>

                    @if (auth()->user()->hasRole('majikan'))
                        <input type="text" name="employe_id" value="{{ auth()->user()->id }}" hidden>
                    @else
                        <div class="form-group">
                            <label for="employe_id">Majikan <span class="text-danger">*</span></label>
                            <select class="form-control" id="employe_id" name="employe_id" required>
                                <option selected disabled>Pilih Majikan...</option>
                                @foreach ($employes as $employe)
                                    @php
                                        $applicationExists = \App\Models\Application::where('servant_id', $data->id)
                                            ->where('employe_id', $employe->id)
                                            ->where('status', 'interview')
                                            ->exists();
                                    @endphp
                                    @if (!$applicationExists)
                                        <option value="{{ $employe->id }}">{{ $employe->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    @endif

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
            $('#hireModal-{{ $data->id }}').on('shown.bs.modal', function() {
                $('#notes-editor').summernote({
                    placeholder: 'Tulis deskripsi di sini...',
                    tabsize: 2,
                    height: 150,
                    toolbar: [
                        ['font', ['bold', 'italic', 'underline']],
                        ['para', ['ul']],
                    ]
                });
            });

            $('#hireModal-{{ $data->id }}').on('hidden.bs.modal', function() {
                $('#notes-editor').summernote('destroy');
            });
        });
    </script>
@endpush
