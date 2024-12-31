<div class="modal fade" id="draftModal-{{ $data->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Draft {{ $data->name }}</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                @if ($data->file_draft)
                    @php
                        $filePath = storage_path('app/public/' . $data->file_draft);
                    @endphp

                    @if (file_exists($filePath))
                        @if (Str::endsWith($data->file_draft, ['.jpg', '.jpeg', '.png', '.gif']))
                            <img src="{{ asset('storage/public/' . $data->file_draft) }}" alt="Preview" class="img-fluid">
                        @elseif (Str::endsWith($data->file_draft, ['.pdf']))
                            <iframe src="{{ asset('storage/public/' . $data->file_draft) }}" width="100%"
                                height="400px"></iframe>
                        @else
                            <p>Format file tidak didukung untuk preview.</p>
                        @endif
                    @else
                        <p>File tidak ditemukan di server.</p>
                    @endif
                @else
                    <p>Belum ada file yang diunggah.</p>
                @endif
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
            </div>
        </div>
    </div>
</div>
