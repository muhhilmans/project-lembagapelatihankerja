<div class="card shadow mb-4">
    <div class="card-body">
        <table class="table table-responsive table-borderless">
            <tbody>
                <tr>
                    <th scope="row">Nama</th>
                    <td>:</td>
                    <td>{{ $data->name }}</td>
                </tr>
                <tr>
                    <th scope="row">Username</th>
                    <td>:</td>
                    <td>{{ $data->username }}
                    </td>
                </tr>
                <tr>
                    <th scope="row">Email</th>
                    <td>:</td>
                    <td>{{ $data->email }}</td>
                </tr>
                <tr>
                    <th scope="row">Nomor Telepon</th>
                    <td>:</td>
                    <td>{{ $data->employeDetails->phone }}</td>
                </tr>
                <tr>
                    <th scope="row">Alamat</th>
                    <td>:</td>
                    <td>{{ $data->employeDetails->address }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>