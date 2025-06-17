

@include('page.header')

<body class="g-sidenav-show   bg-gray-100">
  <div class="min-height-300 bg-danger position-absolute w-100"></div>
  @include('karyawan.bo.side')
  <main class="main-content position-relative border-radius-lg ">
    <!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl " id="navbarBlur" data-scroll="false">
      <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
              <li class="breadcrumb-item text-sm text-white active" aria-current="page">Apliaksi Kalkulator Crew Banana Krezzz</li>
              <li class="breadcrumb-item text-sm text-white">Back Office |  {{ Auth::user()->nama }} | Cabang {{ Auth::user()->cabang ? Auth::user()->cabang->nama : 'Dihapus' }}</li>
          </ol>
          <h6 class="font-weight-bolder text-white mb-0">Dashboard</h6>
        </nav>
       
      </div>
    </nav>
    <!-- End Navbar -->
    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
          <div class="card">
            <div class="card-body p-3">
              <div class="row">
                <div class="col-8">
                  <div class="numbers">
                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Rombongan Datang</p>
                    <h5 class="font-weight-bolder">
                      {{ $rombongan->where('status','datang')->count() }}
                    </h5>
                  </div>
                </div>
                <div class="col-4 text-end">
                  <div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle">
                    <i class="ni bi-bus-front-fill text-lg opacity-10" aria-hidden="true"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
          <div class="card">
            <div class="card-body p-3">
              <div class="row">
                <div class="col-8">
                  <div class="numbers">
                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Rombongan Dalam Proses Transaksi</p>
                    <h5 class="font-weight-bolder">
                      {{ $rombongan->where('status','transaksi')->count() }}
                    </h5>
                   
                  </div>
                </div>
                <div class="col-4 text-end">
                  <div class="icon icon-shape bg-gradient-danger shadow-danger text-center rounded-circle">
                    <i class="ni bi-bus-front-fill text-lg opacity-10" aria-hidden="true"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
          <div class="card">
            <div class="card-body p-3">
              <div class="row">
                <div class="col-8">
                  <div class="numbers">
                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Rombongan Selesai</p>
                    <h5 class="font-weight-bolder">
                      {{ $rombongan->where('status','selesai')->count() }}
                    </h5>
                    
                  </div>
                </div>
                <div class="col-4 text-end">
                  <div class="icon icon-shape bg-gradient-success shadow-success text-center rounded-circle">
                    <i class="ni bi-bus-front-fill text-lg opacity-10" aria-hidden="true"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
       
      </div>
      
      @if ($status != null)
      <div class="alert alert-warning mt-5" role="alert">
          <h4 class="alert-heading text-white">Update Databse!</h4>
          <p class="text-white">Belum bisa melakukan transaksi hari ini, perlu tindakan update database dari Front Office</p>
      </div>
  @endif
      <div class="row mt-4">
        <div class="col-lg-12 mb-lg-0 mb-4">
          <div class="card ">
            <div class="card-header pb-0 p-3">
              <div class="d-flex justify-content-between">
                <h6 class="mb-2">Data Rombongan Hari Ini</h6>
                <div>
                  <a href="/export-excel" class="btn  btn-success">Export Data</a>
                  <button type="button" class="btn sync-api btn-primary">Sinkronkan Data</button>
                </div>
              </div>
            </div>
            <div class="table-responsive">
              <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">Status</th>
                        <th scope="col">Rombongan</th>
                        <th scope="col">Waktu</th>
                        <th scope="col">Total Belanja</th>
                        <th scope="col">Kode</th>
                    </tr>
                </thead>
                <tbody id="rombongan-table">
                    <!-- Data akan dimuat di sini oleh jQuery AJAX -->
                </tbody>
            </table>
            <meta name="csrf-token" content="{{ csrf_token() }}">
            <script>
              $(".sync-api").click(async function () {
                $(".sync-api").attr('disabled',true)
                $(".sync-api").html('Loading...')
                try {
                  const now = new Date();
                  const today = '2025-06-04';
                  {{-- const today = '2025-06-15'; --}}
                  {{-- const today = new Date().toISOString().slice(0, 10); --}}

                  let token = "";
                  let page = 1;
                  let result_data = [];
                  let result_data_send = [];

                  // Dapatkan token
                  const tokenResponse = await $.ajax({
                    url: "https://api-open.olsera.co.id/api/open-api/v1/id/token",
                    type: "post",
                    data: {
                      app_id: 'pjm6n8KIbywEf4jLRuRX',
                      secret_key: 'otAW7uDlLFt1cvAgyzgsON6ifh31xXJw',
                      grant_type: 'secret_key'
                    }
                  });
                  token = tokenResponse.access_token;

                  while (true) {
                    try {
                      const salesResponse = await $.ajax({
                        url: "https://api-open.olsera.co.id/api/open-api/v1/id/report/salesitemsbydate",
                        type: "get",
                        data: {
                          per_page: '100',
                          from: today,
                          to: today,
                          page: page
                        },
                        headers: {
                          "Content-Type": "application/x-www-form-urlencoded",
                          "Authorization": `Bearer ${token}`
                        }
                      });

                      if (salesResponse.data.length > 0) {
                        result_data = result_data.concat(salesResponse.data);
                        page++;
                        await new Promise(resolve => setTimeout(resolve, 200)); // Jeda 1 detik antar page
                      } else {
                        break;
                      }
                    } catch (err) {
                      if (err.status === 429) {
                        let waitSeconds = 30; // default
                        if (err.responseJSON && err.responseJSON.message) {
                          const waitMatch = err.responseJSON.message.match(/Wait for (\d+)s/);
                          if (waitMatch) {
                            waitSeconds = parseInt(waitMatch[1]);
                          }
                        }
                        $(".sync-api").html(`Terlalu banyak request. Menunggu ${waitSeconds} detik...`)
                        await new Promise(resolve => setTimeout(resolve, waitSeconds * 200));
                        // lanjutkan loop
                      } else {
                        throw err; // selain 429, lempar error agar bisa ditangani di luar
                      }
                    }

                  }


                  $.each(result_data,(i,val)=>{
                    if (val.customer_name != null) {
                      result_data_send.push({
                        name : val.customer_name,
                        price : val.profit,
                        order_time : val.forder_date.split(' ')[1],
                        order_date : val.order_date
                      })
                    }
                  })

                  const finalResponse = await $.ajax({
                    url: window.location.origin + '/sync-api',
                    type: "POST",
                    data: JSON.stringify({
                      result: result_data_send
                    }),
                    contentType: "application/json",
                    processData: false,
                    headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                      window.location.href = window.location.origin + '/sync-api-success';
                    }
                  });


                } catch (error) {
                  console.error("error:", error);
                  $(".sync-api").attr('disabled',false)
                  $(".sync-api").html('Gagal, Silahkan Sinkronkan Ulang')
                }
              });


              jQuery(document).ready(function($) {
                  setInterval(function() {
                      jQuery.ajax({
                          url: '/frontoffice/rombongan-data',
                          method: 'GET',
                          success: function(response) {
                              var tableContent = '';
                              jQuery.each(response, function(index, item) {
                                  tableContent += '<tr>';
                                  
                                  // Status kolom dengan ikon sesuai status
                                  tableContent += '<td>';
                                  if (item.status === 'datang') {
                                      tableContent += '<i class="bi bi-box-arrow-in-down text-success"></i>';
                                  } else if (item.status === 'transaksi') {
                                      tableContent += '<i class="bi bi-calculator text-primary"></i>';
                                  } else if (item.status === 'selesai') {
                                      tableContent += '<i class="bi bi-check2-circle text-danger"></i>';
                                  }
                                  tableContent += '</td>';
          
                                  // Rombongan (nama)
                                  tableContent += '<td> <a href="/detail_transaksi?id=' + item.id +'">' + item.nama + '</a></td>';
          
                                  // Waktu (datang dan pulang)
                                  tableContent += '<td>';
                                  tableContent += '<h6 class="text-sm mb-0 text-primary">' + item.waktu_datang + '</h6>';
                                  tableContent += '<h6 class="text-sm mb-0 text-danger">' + item.waktu_pulang + '</h6>';
                                  tableContent += '</td>';
          
                                  // Total Belanja (dengan kelas harga)
                                  tableContent += '<td >' + (item.total_belanja ? item.total_belanja+' ('+item.total_belanja2+')' : '-') + '</td>';
          
                                  // Kode
                                  tableContent += '<td>' + item.kode + '</td>';
          
                                  tableContent += '</tr>';
                              });
                              
                              // Tambahkan konten baru ke tabel
                              jQuery('#rombongan-table').html(tableContent);
                              
                              // Format angka di elemen dengan kelas 'harga' setelah elemen HTML ditambahkan
                              document.querySelectorAll(".harga").forEach(function(element) {
                                  let textContent = element.textContent.trim();
                                  if (!isNaN(textContent) && textContent !== '-') {
                                      element.textContent = Number(textContent).toLocaleString();
                                  }
                              });
                          },
                          error: function() {
                              console.log('Error loading data.');
                          }
                      });
                  }, 5000); // Refresh setiap 5 detik
              });
          </script>
          
            
            
            </div>
          </div>
          
        </div>
       
      </div>
     
    </div>
    
  </main>
  
  
  @include('page.footer')