<div class="modal fade" id="applyModal-{{ $data->id }}" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Lamar Lowongan</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">Apakah anda yakin untuk melamar <b>{{ $data->title }}</b> di {{ $data->user->name }}?</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                <form action="{{ route('apply-job') }}" method="POST">
                    @csrf
                    <input type="text" id="servant_id" name="servant_id" value="{{ auth()->user()->id }}" hidden />
                    <input type="text" id="vacancy_id" name="vacancy_id" value="{{ $data->id }}" hidden />
                    <button class="btn btn-primary" type="submit">Yakin</button>
                </form>
            </div>
        </div>
    </div>
</div>
