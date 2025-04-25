@include('page.header')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<meta name="csrf-token" content="{{ csrf_token() }}">

<body class="g-sidenav-show   bg-gray-100">

    <div id="mobile-content">
        <div class="min-height-200 bg-primary position-absolute w-100"></div>

        <main class="main-content position-relative border-radius-lg ">
            <!-- Navbar -->
            <nav class="col-lg-12 text-center">
                <h6 class="font-weight-bolder text-white mb-0">Kasir | {{ Auth::user()->nama }} | Cabang {{ Auth::user()->cabang->nama }}<br> Input invoice
                    Rombongan
                </h6>
            </nav>

            <!-- End Navbar -->
            <div class="container-fluid">
                <div class="row">
                    <div class="row mt-1">
                        <div class="col-lg-12 mb-lg-0 mb-2">
                            <div class="card">
                                <div class="card-header pb-0 p-3">
                                    <h5 class="text-center">Rombongan Aktif</h5>
                                    <div class="col-lg-12 mb-lg-3 mb-4">
                                        <div id="rombongans-container">
                                            <!-- Bagian ini akan diperbarui setiap 5 detik -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-1">
                        <div class="col-lg-12 mb-lg-0 mb-4">
                            <div class="card">
                                <div id="rombongan-detail">
                                    <div class="card-header pb-0 p-3">
                                        <h5 class="text-center">Data Invoice</h5>
                                        <p class="d-flex justify-content-center text-primary text-center"
                                            id="rombongan-info">
                                            <!-- Nama dan waktu datang akan dimuat di sini -->
                                        </p>

                                        <div class="col-lg-12 mb-lg-3 mb-4 mt-2">
                                            <b style="display: block; max-height: 8em; overflow-y: auto;">
                                                <div class="harga" id="harga">
                                                    <!-- Nilai belanja akan dimuat di sini -->
                                                </div>
                                            </b>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form id="invoiceForm">
                            <input type="hidden" name="rombongan_id" id="value-rombongan" value="">
                            <!-- Contoh ID -->
                            <input type="hidden" name="belanja" id="input-belanja" autofocus>
                            <div class="d-flex justify-content-center">
                                <button type="button" class="btn btn-primary btn-lg btn-block" id="btn-tambah-invoice"
                                    style="margin: 5px; display:none">
                                    Tambah Invoice
                                </button>
                            </div>
                        </form>

                    </div>
                    <!-- Modal -->
                    <div class="modal fade" id="inputBelanjaModal" tabindex="-1"
                        aria-labelledby="inputBelanjaModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="inputBelanjaModalLabel">Masukkan Jumlah Belanja</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p class="d-flex justify-content-center text-primary text-center"
                                        id="rombongan-info-modal">
                                    </p>
                                    <small class="text-danger" id="error-message" style="display: none;">Harap masukkan
                                        angka saja. Tanpa titik dan koma</small>
                                    <small id="notif-invoice"
                                        style="display: none; color: green; text-align: center;" class="d-flex justify-content-center"></small>
                                        
                                        
                                        <div class="d-flex justify-content-center">
                                            <h4></h4>
                                        </div>
                                    <input type="number" id="belanja-input" class="form-control"
                                        placeholder="Masukkan angka saja" />

                                </div>
                                <div class="d-flex justify-content-center">
                                    <button type="button" class="btn btn-primary btn-lg btn-block"
                                        id="saveBelanja">Simpan</button>
                                </div>
                            </div>
                        </div>
                    </div>
<script>
    $('#belanja-input').on('input', function() {
        // Ambil nilai input dan hapus koma yang sudah ada
        let inputVal = $(this).val().replace(/,/g, '');

        // Pastikan nilai adalah angka sebelum memformat
        if (!isNaN(inputVal) && inputVal !== '') {
            // Format angka dengan koma menggunakan toLocaleString dan tambahkan "000" di belakangnya
            const formattedValue = Number(inputVal).toLocaleString('en-US') + ',000';
            
            // Tampilkan angka terformat di elemen <h4>
            $('.d-flex h4').text(formattedValue);
        } else {
            $('.d-flex h4').text('0'); // Jika input kosong atau tidak valid, tampilkan 0
        }
    });
</script>

                    
                    <script>
                        $(document).ready(function() {
                            // Tambahkan CSRF token ke header AJAX
                            $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                }
                            });

                            // Event ketika tombol "Tambah Invoice" diklik
                            $('#btn-tambah-invoice').on('click', function() {
                                // Buka modal untuk input jumlah belanja
                                $('#inputBelanjaModal').modal('show');
                            });

                            // Event ketika tombol "Simpan" di dalam modal diklik
                            $('#saveBelanja').on('click', function() {
                                let belanjaValue = $('#belanja-input').val();
                                 belanjaValue = belanjaValue + '000';

                                // Validasi input hanya angka

                                // Data yang akan dikirim ke server
                                const formData = {
                                    rombongan_id: $('#value-rombongan').val(),
                                    belanja: belanjaValue
                                };

                                // Mengirim data menggunakan AJAX
                                $.ajax({
                                    type: "POST",
                                    url: "/tambah-invoice", // Sesuaikan dengan endpoint server
                                    data: formData,
                                    success: function(response) {
                                        $('#input-belanja').val(
                                        ''); // Reset nilai input hidden belanja setelah submit
                                        $('#belanja-input').val(''); // Reset input modal

                                        if (response.status && response.status === 'selesai') {
                                            // Menampilkan pesan jika transaksi sudah selesai
                                            $('#notif-invoice').html('<i class="bi bi-exclamation-triangle-fill"></i> Rombongan sudah selesai transaksi')
                                        .show();
                                        } else {
                                            // Menampilkan notifikasi jika transaksi berhasil
                                            $('#notif-invoice').html(
                                                `<i class="bi bi-cloud-arrow-up-fill"></i> Invoice ${belanjaValue} terupload`
                                                ).show();

                                                if (/^\d+$/.test(belanjaValue)) {
                                            // Kosongkan pesan error jika validasi berhasil
                                            $('#error-message').hide();

                                            // Tambahkan nilai ke elemen dengan id "harga"
                                            $('#harga').append(`${belanjaValue} + `);

                                            // Set nilai input hidden untuk belanja
                                            $('#input-belanja').val(belanjaValue);
                                        } else {
                                            // Tampilkan pesan error jika input tidak valid
                                            $('#error-message').show();
                                        }
                                        }
                                        // Sembunyikan notifikasi setelah 2 detik
                                        setTimeout(function() {
                                            $('#notif-invoice').fadeOut();
                                        }, 2000);

                                        $('#belanja-input').prop('disabled', false);
                                    },
                                    error: function(xhr) {
                                        console.error("Gagal menyimpan data:", xhr.responseText);
                                        alert("Gagal menyimpan data karena ada titik/koma/huruf. Code : " + xhr.responseText);
                                    }
                                });

                            });
                        });
                    </script>



                    <script>
                        // Fungsi untuk memperbarui daftar rombongan setiap 5 detik
                        function fetchRombongans() {
                            $.ajax({
                                url: '/rombongans-data',
                                method: 'GET',
                                success: function(response) {
                                    let container = $('#rombongans-container');
                                    container.empty(); // Kosongkan container
                                    response.forEach(function(all) {

                                        container.append(`
                                    <a href="#" class="btn btn-outline-dark btn-sm mb-1 me-0 px-3 rombongan-btn" data-id="${all.id}">
                                       ${all.nama} (${all.waktu_datang})
                                    </a>
                                `);
                                    });
                                },
                                error: function() {
                                    console.error('Error fetching data.');
                                }
                            });
                        }

                        // Fungsi untuk memformat angka dengan koma
                        function formatNumberWithCommas(number) {
                            return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                        }

                        // Menangani klik pada elemen rombongan untuk menampilkan detail tanpa reload
                        $(document).on('click', '.rombongan-btn', function(e) {
                            e.preventDefault(); // Mencegah reload
                            let rombonganId = $(this).data('id');

                            $('#btn-tambah-invoice').css('display', 'block');
                            // Ambil data detail rombongan dan invoice menggunakan AJAX
                            $.ajax({
                                url: '/rombongan-detail',
                                method: 'GET',
                                data: {
                                    id: rombonganId
                                },
                                success: function(response) {
                                    if (response.rombongan) {
                                        // Update informasi rombongan di #rombongan-info dengan gaya yang diinginkan
                                        $('#rombongan-info').html(`
                    <div class="btn btn-outline-success btn-sm mb-1 me-0 px-3">
                        ${response.rombongan.nama} (${response.rombongan.waktu_datang})
                    </div>
                `);
                                        $('#rombongan-info-modal').html(`
                    <b>
                        ${response.rombongan.nama} (${response.rombongan.waktu_datang})
                    </b>
                `);
                                        $('#value-rombongan').val(response.rombongan.id);


                                        // Update nilai belanja dengan format koma di #harga
                                        let hargaContainer = $('#harga');
                                        hargaContainer.empty(); // Kosongkan kontainer harga
                                        response.invoice.forEach(function(inv) {
                                            hargaContainer.append(`${formatNumberWithCommas(inv.belanja)} + `);
                                        });
                                    } else {
                                        console.log('Data rombongan tidak ditemukan.');
                                    }
                                },
                                error: function() {
                                    console.error('Error loading rombongan details.');
                                }
                            });
                        });

                        // Fungsi untuk memperbarui daftar rombongan setiap 5 detik
                        setInterval(fetchRombongans, 5000);

                        // Panggil fungsi fetchRombongans pertama kali
                        fetchRombongans();
                    </script>


                </div>

        </main>
    </div>

    <div id="desktop-message">
        Halaman ini hanya mendukung tampilan mobile.
    </div>


    @include('page.footer')
