@extends('auth.layout.main', ['title' => 'Register Majikan'])

@section('main')
    <div class="col-lg-8 text-right">
        <a href="{{ route('select-register') }}" class="btn btn-secondary mb-3 shadow"><i
                class="fas fa-fw fa-arrow-left"></i></a>

        <div class="row text-center">
            <div class="col-lg-12">
                <div class="card o-hidden border-0 shadow-lg mb-3 mb-lg-0 p-3">
                    <div class="card-title">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="defaultCheck1" required>
                            <label class="form-check-label" for="defaultCheck1">
                                Saya telah membaca dan menyetujui <a href="#" data-toggle="modal"
                                    data-target="#termEmployeModal">Syarat</a> dan <a href="#" data-toggle="modal"
                                    data-target="#conditionEmployeModal">Ketentuan</a>
                            </label>
                            @include('auth.modal.employe-term')
                            @include('auth.modal.employe-condition')
                        </div>
                    </div>
                    <div class="card-body text-start">
                        <form action="{{ route('store-employe-register') }}" method="POST" id="registerForm"
                            style="display: none;">
                            @csrf
                            <div class="mb-2 text-left">
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
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="phone">Nomor Telepon <span class="text-danger">*</span></label>
                                        <input type="tel" class="form-control" id="phone" name="phone"
                                            placeholder="Nomor Telepon" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="address">Alamat <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="address" name="address" rows="3" placeholder="Alamat Lengkap" required></textarea>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Daftar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('custom-script')
    <script>
        document.getElementById('defaultCheck1').addEventListener('change', function() {
            const registerForm = document.getElementById('registerForm');
            registerForm.style.display = this.checked ? 'block' : 'none';
        });
    </script>
@endpush