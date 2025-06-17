@include('page.header')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<body class="g-sidenav-show   bg-gray-100">
    <div class="min-height-300 bg-danger position-absolute w-100"></div>
    @include('karyawan.bo.side')
    <main class="main-content position-relative border-radius-lg ">
        <!-- Navbar -->
        <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl " id="navbarBlur"
            data-scroll="false">
            <div class="container-fluid py-1 px-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                        <li class="breadcrumb-item text-sm text-white active" aria-current="page">Apliaksi Kalkulator
                            Crew Banana Krezzz</li>
                        <li class="breadcrumb-item text-sm text-white">Back Office | {{ Auth::user()->nama }} | Cabang {{ Auth::user()->cabang ? Auth::user()->cabang->nama : 'Dihapus' }}</li>
                    </ol>
                    <h6 class="font-weight-bolder text-white mb-0">Detail Transaksi</h6>
                </nav>

            </div>
        </nav>
        <!-- End Navbar -->
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-xl-12 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="row">
                                <div class="col-8">
                                    <div class="numbers">
                                        <p class="text-sm mb-0 text-uppercase font-weight-bold">Detail Rombongan</p>
                                        <ul class="list-group">
                                            <li class="list-group-item">Rombongan : <b>{{ $rombongan->nama }}</b></li>
                                            <li class="list-group-item">Waktu Kedatangan :
                                                {{ $rombongan->waktu_datang }}</li>
                                            </li>
                                            <li class="list-group-item">Total Transaksi :
                                                {{ $rombongan->total_belanja }}</li>
                                            <li class="list-group-item">ID : <b>{{ $rombongan->id }}</b>
                                            </li>
                                            <li class="list-group-item">Status : {{ strtoupper($rombongan->status) }}
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-4 text-center">
                                    @if ($rombongan->status == 'selesai')
                                        <a class="btn btn-danger btn-lg btn-block"><i
                                                class="bi bi-lock-fill"></i>Transaksi Sudah Selesai</a> <br>
                                        <small>Transaksi untuk rombongan ini sudah selesai dan kasir sudah tidak bisa mengupdate invoice untuk rombongan ini</small>
                                    @else
                                    <form method="GET" action="/update_status_transaksi">
                                      <input type="hidden" value="" name="total" id="total-belanja">
                                      <input type="hidden" value="{{ $rombongan->id }}" name="rombongan_id">
                                      <input type="hidden" value="" name="total_belanja2">
                                      <input type="hidden" value="" name="total_fee">
                                      <button type="submit" href="/transaksi_selesai?id={{ $rombongan->id }}"
                                        class="btn btn-primary btn-lg btn-block"><i
                                            class="bi bi-lock-fill"></i>Selesaikan Transaksi</button> <br>
                                    <small>Untuk Menyelesaiakan Transaksi pastikan bahwa tidak ada transaksi lagi di
                                        kasir, setelah transaksi selesai kasir tidak bisa menambahkan invoice untuk
                                        rombongan ini</small>
                                    </form>
                                        
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



            </div>

            <div class="row mt-4">
                <div class="col-lg-12 mb-lg-0 mb-4">
                    <div class="card p-3">
                        <div class="card-header pb-0 p-3">
                            <div class="d-flex justify-content-between">
                                <h6 class="mb-2">Data Rombongan Hari Ini</h6>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col">User</th>
                                        <th scope="col">Belanja</th>
                                        <th scope="col">Total</th>
                                    </tr>
                                </thead>
                                <tbody id="invoice-table">
                                    <!-- Data akan dimuat dari AJAX di sini -->
                                </tbody>
                            </table>
                        </div>
                        <h1 id="total-amount" class="h3 btn btn-success btn-lg btn-block"></h1>
                        <p class="m-0 mt-2">Masukkan Biaya Dibulatkan (angka)</p>
                        <input type="number" id="total-amount2" class="form-control w-100">
                        <p class="m-0 mt-2">Fee</p>
                        <input type="text" id="total-fee" class="form-control w-100 bg-light" readonly>

                        <script>
                            jQuery(document).ready(function($) {
                                // Get the rombongan_id from Blade template and store in a variable
                                var rombonganId = {{ $rombongan->id }};

                                // Function to fetch data and update the table
                                function fetchInvoiceData() {
                                    $.ajax({
                                        url: '/invoice-detail', // The route to fetch data
                                        method: 'GET',
                                        data: {
                                            rombongan_id: rombonganId
                                        }, // Pass rombongan_id with the request
                                        success: function(response) {
                                            var tableContent = '';
                                            var grandTotal = 0;
                                            let userDataTotal_belanja2 = 0
                                            let userDataTotal_fee = 0

                                            // Loop through each user's invoice data
                                            $.each(response.userTotals, function(userId, userData) {
                                                var userName = userData.user.nama;
                                                var totalBelanja = 0;
                                                var belanjaItems = '';

                                                // Loop through the 'belanja' items for each user
                                                userDataTotal_belanja2 = userData.rombongan.total_belanja2
                                                userDataTotal_fee = userData.rombongan.fee
                                                $.each(userData.belanja, function(index, belanja) {
                                                    // Pastikan belanja adalah angka
                                                    belanja = parseInt(belanja, 10) || 0;  // Jika tidak bisa dikonversi, jadikan 0
                                                    belanjaItems +=
                                                        '<span class="belanja-item" data-belanja="' +
                                                        belanja + '">' + belanja.toLocaleString('id-ID') +
                                                        ' + </span>';
                                                    totalBelanja += belanja;
                                                });


                                                // Append the row for this user
                                                tableContent += '<tr>';
                                                tableContent += '<th scope="row">' + userName + '</th>';
                                                tableContent +=
                                                    '<td style="max-width: 200px; word-wrap: break-word; white-space: normal;">' +
                                                    belanjaItems + '</td>';
                                                tableContent += '<td><b>' + totalBelanja.toLocaleString('id-ID') +
                                                    '</b></td>';
                                                tableContent += '</tr>';

                                                // Add to the grand total
                                                grandTotal += totalBelanja;
                                            });

                                            // Update the table and grand total
                                            $('#invoice-table').html(tableContent);
                                            $('#total-amount').text('Total: ' + grandTotal.toLocaleString('id-ID'));
                                            $('#total-belanja').val(grandTotal.toLocaleString('id-ID'));
                                            if (userDataTotal_belanja2 != null) {
                                                $('#total-amount2').val(userDataTotal_belanja2.replace('.','').replace('.',''));
                                                $('[name=total_belanja2]').val(userDataTotal_belanja2);
                                            }
                                            if (userDataTotal_fee != null) {
                                                $('#total-fee').val(userDataTotal_fee);
                                            }
                                            if ($('#total-amount2').val() == 0 && userDataTotal_belanja2 == null) {
                                                $('#total-amount2').val(grandTotal.toLocaleString('id-ID').replace('.','').replace('.',''));
                                                $('[name=total_belanja2]').val(grandTotal.toLocaleString('id-ID'));
                                            }
                                            console.log(123)
                                            $('#total-fee').val(0.20*$('#total-amount2').val()).change()

                                        },
                                        error: function(xhr, status, error) {
                                            console.error('Error loading data: ', error);
                                        }
                                    });
                                }

                                // Initial fetch of data and set interval to update every 5 seconds
                                fetchInvoiceData();
                                setInterval(fetchInvoiceData, 5000);
                            });

                            $('#total-amount2').keyup(function(){
                                $("[name=total_belanja2]").val($(this).val().toLocaleString('id-ID'))
                                $('#total-fee').val(0.20*$(this).val()).change()
                            });

                            $('#total-fee').change(function(){
                                $("[name=total_fee]").val($(this).val().toLocaleString('id-ID'))
                            });
                        </script>




                    </div>
                </div>

            </div>

        </div>
    </main>


    @include('page.footer')
