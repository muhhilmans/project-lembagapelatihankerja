<div class="modal fade" id="chooseModal-{{ $data->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Pilih Majikan</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form method="POST" action="{{ $data->vacancy_id ? route('applicant-indie.change', ['vacancy' => $data->vacancy_id, 'user' => $data->servant_id]) : route('applicant-hire.change', $data->id) }}">
                @csrf
                @method('PUT')
                <div class="modal-body text-left">
                    <input type="text" name="status" value="choose" hidden>
                    <input type="text" name="notes" value="" hidden>

                    Apakah anda yakin untuk memilih bekerja di <b>{{ $data->vacancy ? $data->vacancy->user->name : $data->employe->name }}</b> ini?
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <button class="btn btn-primary" type="submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
