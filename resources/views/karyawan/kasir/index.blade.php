
@include('page.header')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<body class="g-sidenav-show   bg-gray-100">

    <div id="mobile-content">
  <div class="min-height-200 bg-primary position-absolute w-100"></div>
  
  <main class="main-content position-relative border-radius-lg ">
    <!-- Navbar -->
    <nav class="col-lg-12 text-center">
          <h6 class="font-weight-bolder text-white mb-0">Kasir |  {{ Auth::user()->nama }}<br> Input invoice Rombongan</h6>
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
                                                   {{ $index->nama }}
                                                   
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
                                                tableContent += '</div><div class="ms-4">' + item.nama + '</div>';
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
          
          @if ($status == null)
          <a href="/cal" class="btn btn-success btn-sm w-100 mt-5"><i class="bi bi-calculator-fill"></i> Masukan Invoice</a>
      @else
          <div class="alert alert-warning mt-5" role="alert">
              <h4 class="alert-heading text-white">Update Databse!</h4>
              <p class="text-white">Belum bisa melakukan transaksi hari ini, perlu tindakan update database dari Front Office</p>
          </div>
      @endif
          
          <a href="/logout" class="btn btn-dark btn-sm w-100 mt-5">Log Out</a>
        </div>
       
      </div>
    </div>
    
  </div>
  </div>
  </main>

  <div id="desktop-message">
    Halaman ini hanya mendukung tampilan mobile.
  </div>
  
  

  @include('page.footer')