<div class="modal fade" id="recommendModal-{{ $data->id }}" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Rekomendasi Pembantu</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form action="{{ route('vacancy.recommendation', $data->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="profession" class="col-form-label">Profesi <span
                                class="text-danger">*</span></label>
                        <select class="form-control" id="profession" name="profession" required>
                            <option value="">Pilih Profesi</option>
                            @foreach ($professions as $data)
                                <option value="{{ $data->id }}">{{ $data->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group" id="servant_fields">
                        <label for="servant_id" class="col-form-label">Pembantu <span
                                class="text-danger">*</span></label>
                        <select class="form-control" id="servant_id" name="servant_id" required>
                            <option value="">Pilih Pembantu</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <button class="btn btn-primary" type="submit">Yakin</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('custom-script')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const professionSelect = document.getElementById('profession');
            const servantSelect = document.getElementById('servant_id');

            professionSelect.addEventListener('change', (event) => {
                const professionId = event.target.value;

                if (professionId) {
                    populateServants(professionId);
                } else {
                    clearServantSelection();
                }
            });

            function populateServants(professionId) {
                servantSelect.innerHTML = '<option value="">Pilih Pembantu</option>';

                @foreach ($servants as $data)
                    if ('{{ $data->servantDetails->profession_id }}' == professionId) {
                        const option = document.createElement('option');
                        option.value = '{{ $data->id }}';
                        option.textContent = '{{ $data->name }}';
                        servantSelect.appendChild(option);
                    }
                @endforeach
            }

            function clearServantSelection() {
                servantSelect.innerHTML = '<option value="">Pilih Pembantu</option>';
            }
        });
    </script>
@endpush
