@include('page.header')
<!-- Sertakan jQuery terlebih dahulu -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<body class="g-sidenav-show   bg-gray-100">
    <div id="mobile-content">
        <div class="min-height-200 bg-primary position-absolute w-100"></div>
        <main class="main-content position-relative border-radius-lg ">
            <!-- Navbar -->
            <nav class="col-lg-12 text-center">
                <h6 class="font-weight-bolder text-white mb-0">Front Office | {{ Auth::user()->nama }} | Cabang {{ Auth::user()->cabang->nama }}<br> Input Data
                    Rombongan</h6>
            </nav>
            <!-- End Navbar -->
            <div class="container-fluid">
                <div class="row">
                    <div class="row mt-1">
                        <div class="col-lg-12 mb-lg-0 mb-4">
                            <div class="card ">
                                <div class="card-header pb-0 p-3">
                                    <div class="d-flex justify-content-between">
                                        <h6 class="mb-2">Data Rombongan Hari Ini</h6>
                                    </div>
                                </div>

                                <div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">
                                    <table class="table align-items-center">
                                        <tbody>
                                            <tr>
                                                <td class="w-30">
                                                    <div class="d-flex px-2 py-1 align-items-center">
                                                        <div>
                                                            <i class="bi bi-info-lg"></i>
                                                        </div>
                                                        <div class="ms-4">
                                                            <p class="text-xs font-weight-bold mb-0">Rombongan</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="text-center">
                                                        <p class="text-xs font-weight-bold mb-0">Waktu</p>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="text-center">
                                                        <p class="text-xs font-weight-bold mb-0">Kode</p>
                                                    </div>
                                                </td>
                                            </tr>


                                            <table>
                                                <tbody id="rombongan-table">
                                                    @foreach ($rombongan as $index)
                                                        <tr>
                                                            <td class="w-30">
                                                                <div class="d-flex px-2 py-1 align-items-center">
                                                                    <div>
                                                                        @if ($index->status == 'datang')
                                                                            <i
                                                                                class="bi bi-box-arrow-in-down text-success"></i>
                                                                        @elseif($index->status == 'transaksi')
                                                                            <i
                                                                                class="bi bi-calculator text-primary"></i>
                                                                        @elseif($index->status == 'selesai')
                                                                            <i
                                                                                class="bi bi-check2-circle text-danger"></i>
                                                                        @endif
                                                                    </div>
                                                                    <div class="ms-4">
                                                                        <h6 class="text-sm mb-0">{{ $index->nama }}
                                                                        </h6>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="text-center">
                                                                    <h6 class="text-sm mb-0 text-primary">
                                                                        {{ $index->waktu_datang }}</h6>
                                                                    <h6 class="text-sm mb-0 text-danger">
                                                                        {{ $index->waktu_pulang }}</h6>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="text-center">
                                                                    <h6 class="text-sm mb-0">{{ $index->kode }}</h6>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>

                                            <script>
                                                jQuery(document).ready(function($) {
                                                    setInterval(function() {
                                                        jQuery.ajax({
                                                            url: '/frontoffice/rombongan-data',
                                                            method: 'GET',
                                                            success: function(response) {
                                                                var tableContent = '';
                                                                jQuery.each(response, function(index, item) {
                                                                    tableContent += '<tr>';
                                                                    tableContent +=
                                                                        '<td class="w-30"><div class="d-flex px-2 py-1 align-items-center">';
                                                                    tableContent += '<div>';
                                                                    if (item.status === 'datang') {
                                                                        tableContent +=
                                                                            '<i class="bi bi-box-arrow-in-down text-success"></i>';
                                                                    } else if (item.status === 'transaksi') {
                                                                        tableContent +=
                                                                            '<i class="bi bi-calculator text-primary"></i>';
                                                                    } else if (item.status === 'selesai') {
                                                                        tableContent +=
                                                                            '<i class="bi bi-check2-circle text-danger"></i>';
                                                                    }
                                                                    tableContent +=
                                                                        '</div><div class="ms-4"><h6 class="text-sm mb-0">' +
                                                                        item.nama + '</h6></div>';
                                                                    tableContent += '</div></td>';
                                                                    tableContent += '<td><div class="text-center">';
                                                                    tableContent += '<h6 class="text-sm mb-0 text-primary">' +
                                                                        item.waktu_datang + '</h6>';
                                                                    tableContent += '<h6 class="text-sm mb-0 text-danger">' +
                                                                        item.waktu_pulang + '</h6>';
                                                                    tableContent += '</div></td>';
                                                                    tableContent +=
                                                                        '<td><div class="text-center"><h6 class="text-sm mb-0">' +
                                                                        item.kode + '</h6></div></td>';
                                                                    tableContent += '</tr>';
                                                                });
                                                                jQuery('#rombongan-table').html(tableContent);
                                                            },
                                                            error: function() {
                                                                console.log('Error loading data.');
                                                            }
                                                        });
                                                    }, 5000); // Refresh setiap 5 detik
                                                });
                                            </script>



                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>

                    </div>

                </div>
                @if ($status == null)
                    <button data-toggle="modal" data-target="#addrombongan"
                        class="btn btn-primary btn-sm mb-0 w-100 mb-3" type="button">Tambah Rombongan</button>
                @else
                    <div class="alert alert-warning" role="alert">
                        <h4 class="alert-heading text-white">Update Databse!</h4>
                        <p class="text-white">Untuk menambahkan rombongan baru kamu harus menghapus data romongan
                            kemarin dengan mengupdate databse</p>
                        <a href="/update.database" class="btn btn-primary btn-sm mb-0 w-100 mb-3" type="button"><i
                                class="bi bi-database-gear"></i> Update Databse</a>
                    </div>
                @endif

                <div class="d-flex justify-content-center">
                    <a href="/logout" class="btn btn-dark btn-sm w-30">Logout</a>
                </div>

            </div>
        </main>

        <!-- Tambah Rombongan -->
        <div class="modal fade" id="addrombongan" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Tambah Rombongan</h5>
                    </div>
                    <div class="modal-body">
                        <form action="/add.rombongan" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="exampleInputEmail1">Kode</label>
                                <h1>{{ $rombongan->max('kode') + 1 }}</h1>
                                <input type="hidden" name="kode" value="{{ $rombongan->max('kode') + 1 }}">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputPassword1">Nama Rombongan</label>
                                <textarea class="form-control" name="nama" rows="3"></textarea>
                            </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Tambah</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div id="desktop-message">
        Halaman ini hanya mendukung tampilan mobile.
      </div>
      
    <!-- Sertakan jQuery terlebih dahulu -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    @include('page.footer')
