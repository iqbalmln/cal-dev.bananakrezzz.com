<aside class="sidenav bg-white navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-4 "
    id="sidenav-main">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <hr class="horizontal dark mt-0">
    <div class="collapse navbar-collapse  w-auto " id="sidenav-collapse-main">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link active" href="/backoffice">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-tv-2 text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Dashboard</span>
                </a>
            </li>
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Data Rombongan Aktif</h6>
            </li>
            <div class="card bg-success text-white" style="margin: 10px">
                <div class="card-body table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col"><i class="bi bi-info-lg text-white opacity-10 text-sm"></i></th>
                                <th scope="col" class="text-white">Rombongan </th>
                                <th scope="col" class="text-white">Waktu</th>
                                <th scope="col" class="text-white">Kode</th>
                            </tr>
                        </thead>
                        <tbody id="rombongan-table-update">
                            <!-- Data dari AJAX akan di-inject di sini -->
                        </tbody>
                    </table>
                    <script>
                        jQuery(document).ready(function($) {
                            setInterval(function() {
                                jQuery.ajax({
                                    url: '/frontoffice/rombongan-data-transaksi',
                                    method: 'GET',
                                    success: function(response) {
                                        var tableContent = '';
                                        jQuery.each(response, function(index, item) {
                                            tableContent += '<tr>';
                                            tableContent += '<td class="w-30"><div class="d-flex px-2 py-1 align-items-center">';
                                            tableContent += '</div>';
                                            if (item.status === 'datang') {
                                                tableContent += '<i class="bi bi-box-arrow-in-down text-primary"></i>';
                                            } else if (item.status === 'transaksi') {
                                                tableContent += '<i class="bi bi-calculator text-primary"></i>';
                                            } else if (item.status === 'selesai') {
                                                tableContent += '<i class="bi bi-check2-circle text-danger"></i>';
                                            }
                                            tableContent += '<td><div class="ms-4"> <a class="text-white" href="/detail_transaksi?id=' + item.id +'">' + item.nama + '</a></div>';
                                            tableContent += '</div></td>';
                                            tableContent += '<td><div class="text-center">';
                                            tableContent += '<h6 class="text-sm mb-0 text-white">' + item.waktu_datang + '</h6>';
                                            tableContent += '</div></td>';
                                            tableContent += '<td><div class="text-center"><h6 class="text-sm mb-0 text-white">' + item.kode + '</h6></div></td>';
                                            tableContent += '</tr>';
                                        });
                                        jQuery('#rombongan-table-update').html(tableContent);
                                    },
                                    error: function() {
                                        console.log('Error loading data.');
                                    }
                                });
                            }, 2000); // Refresh setiap 5 detik
                        });
                    </script>
                                        
                </div>
            </div>



            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Manajemen Penguna</h6>
            </li>
            <li class="nav-item">
                <a class="nav-link " href="/akun">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-single-02 text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Akun Pengguna</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link " href="/cabang">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-single-02 text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Pengaturan Cabang</span>
                </a>
            </li>
        </ul>
    </div>
    <div class="sidenav-footer mx-3 ">

        <a href="/logout" class="btn btn-dark btn-sm w-100 mb-3">Log Out</a>
    </div>
</aside>
