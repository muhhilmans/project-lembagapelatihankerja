<!-- Modal -->
<div class="modal fade" id="showModal-{{ $data->id }}" tabindex="-1" role="dialog" aria-labelledby="showModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="showModalLabel">Detail Blog</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-left">
                <div class="card shadow">
                    <img src="{{ route('getImage', ['path' => 'blogs', 'imageName' => $data->image]) }}" alt="Foto"
                        class="img-fluid rounded mx-auto d-block zoomable-image pt-2" style="max-height: 100px;">
                    <div class="card-body">
                        <h3 class="font-weight-bold text-center">{{ $data->title }}</h3>
                        <hr>
                        <div class="row d-flex flex-row align-items-baseline justify-content-between px-3 mb-3 mb-lg-0">
                            <p class="card-text"><i class="fas fa-fw fa-user"></i> {{ $data->user->name }}</p>
                            <p class="card-text"><i class="fas fa-fw fa-layer-group"></i> {{ $data->category }}</p>
                            <p class="card-text"><i class="fas fa-fw fa-clock"></i>
                                {{ \Carbon\Carbon::parse($data->created_at)->format('d F Y') }}</p>
                        </div>
                        <p class="card-text px-2"><i class="fas fa-fw fa-tags"></i>
                            @foreach (json_decode($data->tags, true) as $tag)
                                <span class="badge bg-secondary text-light p-2">{{ $tag['value'] }}</span>
                            @endforeach
                        </p>
                        <p class="card-text text-justify">{!! $data->content !!}</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
