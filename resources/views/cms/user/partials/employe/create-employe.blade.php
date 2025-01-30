<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Tambah Majikan</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form method="POST" action="{{ route('users-employe.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="name">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="username">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="email">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="password">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                    </div>
                    {{-- <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="bank_name">Nama Bank <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="bank_name" name="bank_name" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="account_number">Nomor Rekening <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="account_number" name="account_number"
                                required>
                        </div>
                    </div> --}}
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="phone">Nomor Telepon <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="phone" name="phone" maxlength="13"
                                required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="address">Alamat <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="address" id="address" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="identity_card">Kartu Tanda Penduduk <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inpoIdentityCard">Upload</span>
                            </div>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="identity_card" name="identity_card"
                                    aria-describedby="inpoIdentityCard" accept="image/*">
                                <label class="custom-file-label" for="identity_card">Choose file</label>
                            </div>
                        </div>
                        <div class="mt-3" id="identityCardPreviewContainer">
                            <img id="identityCardPreview" src="" alt="KTP" class="img-fluid rounded"
                                style="max-width: 100px;">
                        </div>
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
            function updatePreview(inputId, previewContainerId) {
                const inputFile = document.getElementById(inputId);
                const previewContainer = document.getElementById(previewContainerId);

                inputFile.addEventListener('change', function() {
                    const file = this.files[0];

                    const label = this.nextElementSibling;
                    label.textContent = file ? file.name : 'Choose file';

                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            let previewImage = previewContainer.querySelector('img');
                            if (!previewImage) {
                                previewImage = document.createElement('img');
                                previewImage.className = 'img-fluid rounded';
                                previewImage.style.maxWidth = '100px';
                                previewContainer.appendChild(previewImage);
                            }
                            previewImage.src = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    } else {
                        previewContainer.innerHTML = '';
                    }
                });
            }

            updatePreview('identity_card', 'identityCardPreviewContainer');
        });
    </script>
@endpush