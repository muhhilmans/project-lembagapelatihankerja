@extends('auth.layout.main', ['title' => 'Syarat dan Ketentuan'])

@section('main')
    <div class="col-lg-8 text-right">
        <a href="{{ route('select-register') }}" class="btn btn-secondary mb-3 shadow"><i
                class="fas fa-fw fa-arrow-left"></i></a>

        <div class="row text-center">
            <div class="col-lg-12">
                <div class="card o-hidden border-0 shadow-lg mb-3 mb-lg-0 p-3">
                    <h3 class="card-title">Syarat dan Ketentuan Pengguna Jasa (Majikan) di Aplikasi Sipembantu.com</h3>
                    <div class="card-body text-left">
                        <ol class="font-weight-bold text-justify">
                            <li>Biaya dan Komisi:
                                <ol type="a" class="font-weight-normal">
                                    <li>Aplikasi Sipembantu tidak mengenakan biaya administrasi untuk
                                        pengambilan tenaga kerja PRT/ART.</li>
                                    <li>Pengguna jasa dikenakan komisi sebesar <strong>7,5% dari gaji bulanan
                                        PRT/ART</strong>, yang wajib dibayarkan setiap bulan selama PRT/ART bekerja di
                                        rumah pengguna jasa.</li>
                                </ol>
                            </li>
                            <li>Pembayaran Gaji dan Komisi:
                                <ol type="a" class="font-weight-normal">
                                    <li>Gaji PRT/ART dan komisi aplikasi Sipembantu wajib ditransfer ke rekening
                                        perusahaan <strong>PT. Purwa Sentosa Gemilang</strong> paling lambat tanggal <strong>30 setiap bulannya</strong>.</li>
                                    <li>Pengguna jasa harus mengirimkan bukti transfer ke email atau WhatsApp
                                        resmi aplikasi Sipembantu.</li>
                                    <li>Gaji PRT/ART akan diteruskan oleh aplikasi Sipembantu pada tanggal <strong>1
                                        setiap bulan</strong> setelah dipotong komisi sebesar <strong>2,5%</strong>.</li>
                                </ol>
                            </li>
                            <li>Tanggung Jawab Pengguna Jasa terhadap PRT/ART:
                                <ol type="a" class="font-weight-normal">
                                    <li>Menyediakan tempat tinggal yang layak, fasilitas kesehatan, perlengkapan
                                        mandi, serta waktu untuk beristirahat dan beribadah.</li>
                                    <li>Tidak diperbolehkan meminjamkan uang kepada PRT/ART. Jika terjadi,
                                        aplikasi Sipembantu tidak bertanggung jawab.</li>
                                    <li>Mengawasi dan memeriksa barang bawaan PRT/ART saat tiba di rumah
                                        pengguna jasa.</li>
                                </ol>
                            </li>
                            <li>Kesesuaian Gaji dan Peraturan:
                                <ol type="a">
                                    <li>Gaji PRT/ART yang telah disepakati saat interview tidak dapat diubah dan
                                        harus sesuai dengan nominal yang disepakati.</li>
                                    <li>Pengguna jasa wajib mematuhi <strong>Peraturan Menteri Ketenagakerjaan RI
                                            Nomor 2 Tahun 2015</strong> tentang perlindungan pekerja rumah tangga.</li>
                                </ol>
                            </li>
                            <li>Pekerjaan dan Prosedur:
                                <ol type="a" class="font-weight-normal">
                                    <li>PRT/ART harus dipekerjakan sesuai dengan tugas dan kesepakatan saat
                                        interview.</li>
                                    <li>Pengguna jasa tidak diperbolehkan melakukan tindakan kekerasan,
                                        pelanggaran HAM, atau eksploitasi terhadap PRT/ART. Pelanggaran akan
                                        diproses sesuai hukum yang berlaku di Indonesia.</li>
                                </ol>
                            </li>
                            <li>Keamanan dan Tanggung Jawab:
                                <ol type="a" class="font-weight-normal">
                                    <li>Aplikasi Sipembantu tidak bertanggung jawab atas tindak pidana atau
                                        kerugian yang disebabkan oleh PRT/ART selama bekerja di rumah pengguna
                                        jasa. Namun, kami bersedia membantu menyediakan data yang diperlukan jika terjadi hal yang tidak diinginkan.</li>
                                    <li>PRT/ART telah melalui proses verifikasi data, meliputi foto terbaru, KTP,
                                        KK, lokasi tempat tinggal, nomor telepon keluarga, dan sidik jari.</li>
                                </ol>
                            </li>
                            <li>Biaya tambahan untuk Tenaga Kerja:
                                <ol type="a" class="font-weight-normal">
                                    <li>PRT/ART akan diantar langsung oleh pihak aplikasi Sipembantu ke rumah
                                        pengguna jasa dengan biaya tambahan sebesar Rp. 500.000,-
                                        {{-- <ul type="disc" class="font-weight-normal">
                                            <li>Tangerang: Rp 100.000</li>
                                            <li>Jakarta: Rp 150.000</li>
                                            <li>Depok: Rp 200.000</li>
                                            <li>Bekasi: Rp 350.000</li>
                                            <li>Bogor: Rp 350.000</li>
                                        </ul> --}}
                                    </li>
                                    <li>Besaran biaya BPJS Ketenagakerjaan akan ditentukan berdasarkan kesepakatan antara pihak-pihak terkait pada saat wawancara.</li>
                                </ol>
                            </li>
                            <li>Ketentuan Pembayaran yang Terlambat:
                                <ol type="a" class="font-weight-normal">
                                    <li>Jika pengguna jasa tidak membayar gaji dan komisi sesuai tanggal yang
                                        ditentukan, aplikasi Sipembantu berhak menagih langsung ke rumah
                                        pengguna jasa.</li>
                                </ol>
                            </li>
                            <li>Suspensi Akun:
                                <ol type="a" class="font-weight-normal">
                                    <li>Aplikasi Sipembantu dapat mensuspend akun pengguna jasa atau PRT/ART
                                        yang melanggar ketentuan yang telah disepakati.</li>
                                </ol>
                            </li>
                            <li>Asuransi dan Perlindungan PRT/ART:
                                <ol type="a" class="font-weight-normal">
                                    <li>PRT/ART yang disalurkan melalui aplikasi Sipembantu telah terdaftar di <strong>BPJS
                                            Ketenagakerjaan</strong> dengan perlindungan kecelakaan kerja dan kematian.</li>
                                </ol>
                            </li>
                        </ol>
                    </div>
                    <div class="card-footer text-right">
                        <div class="form-check text-left">
                            <input class="form-check-input" type="checkbox" value="" id="checkTerm" required>
                            <label class="form-check-label" for="checkTerm">
                                Dengan mencentang ini, Pengguna Jasa dianggap telah membaca, memahami, dan menyetujui semua
                                syarat dan ketentuan ini.
                            </label>
                        </div>

                        <a href="{{ route('register-employe') }}" class="btn btn-primary mt-3 shadow disabled"
                            id="nextButton">Selanjutnya</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('custom-script')
    <script>
        document.getElementById('checkTerm').addEventListener('change', function() {
            const nextButton = document.getElementById('nextButton');
            const isDisabled = !this.checked;

            nextButton.classList.toggle('disabled', isDisabled);
            nextButton.setAttribute('tabindex', isDisabled ? '-1' : '0');
            nextButton.setAttribute('aria-disabled', isDisabled.toString());
        });
    </script>
@endpush
