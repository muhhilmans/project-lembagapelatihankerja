@php
    $profession = $data->vacancy ? $data->vacancy->profession : optional($data->servant->servantDetails)->profession;
    $fileDraft = $profession ? $profession->file_draft : null;
@endphp

<div class="modal fade" id="draftModal-{{ $data->id }}" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Perjanjian Kerja {{ $profession ? $profession->name : '' }}</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body text-left">
                @if ($fileDraft)
                    @php
                        $filePath = storage_path('app/public/professions/' . $fileDraft);
                    @endphp

                    @if (file_exists($filePath))
                        @if (Str::endsWith($fileDraft, ['.jpg', '.jpeg', '.png', '.gif']))
                            <img src="{{ route('getFile', ['path' => 'professions', 'fileName' => $fileDraft]) }}"
                                alt="Preview" class="img-fluid">
                        @elseif (Str::endsWith($fileDraft, ['.pdf']))
                            <iframe src="{{ route('getFile', ['path' => 'professions', 'fileName' => $fileDraft]) }}"
                                width="100%" height="400px"></iframe>
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
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
