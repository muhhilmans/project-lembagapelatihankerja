<div class="modal fade" id="complaintModal-{{ $data->id }}" tabindex="-1" role="dialog"
    aria-labelledby="complaintModalLabel-{{ $data->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="complaintModalLabel-{{ $data->id }}">Buat Pengaduan</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route('complaints.store') }}">
                @csrf
                {{-- contract_id = application id --}}
                <input type="hidden" name="contract_id" value="{{ $data->id }}">

                {{-- reported_user_id: majikan melaporkan pembantu --}}
                <input type="hidden" name="reported_user_id" value="{{ $data->servant_id }}">

                <div class="modal-body">
                    <div class="form-group">
                        <label for="complaint_type_id_{{ $data->id }}"><strong>Jenis Pengaduan</strong> <span class="text-danger">*</span></label>
                        <select name="complaint_type_id" id="complaint_type_id_{{ $data->id }}" class="form-control" required>
                            <option value="">-- Pilih Jenis Pengaduan --</option>
                            @foreach($urgencies as $urgency)
                                <option value="{{ $urgency->id }}">{{ $urgency->name }} ({{ $urgency->default_urgency }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="message_{{ $data->id }}">Pesan Pengaduan <span class="text-danger">*</span></label>
                        <textarea name="message" id="message_{{ $data->id }}" class="form-control" rows="4" required
                            placeholder="Jelaskan masalah Anda secara detail..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <button class="btn btn-danger" type="submit"><i class="fas fa-bullhorn mr-1"></i> Kirim Pengaduan</button>
                </div>
            </form>
        </div>
    </div>
</div>
