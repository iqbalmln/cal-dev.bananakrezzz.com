@include('page.header')

<body class="g-sidenav-show   bg-gray-100">
    <div class="min-height-300 bg-danger position-absolute w-100"></div>
    @include('karyawan.master.side')
    <main class="main-content position-relative border-radius-lg ">
        <!-- Navbar -->
        <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl " id="navbarBlur"
            data-scroll="false">
            <div class="container-fluid py-1 px-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                        <li class="breadcrumb-item text-sm text-white active" aria-current="page">Apliaksi Kalkulator
                            Crew Banana Krezzz</li>
                        <li class="breadcrumb-item text-sm text-white">Master | {{ Auth::user()->nama }} | Cabang {{ Auth::user()->cabang ? Auth::user()->cabang->nama : '' }}</li>
                    </ol>
                    <h6 class="font-weight-bolder text-white mb-0">Cabang</h6>
                </nav>

            </div>
        </nav>
        <!-- End Navbar -->
        <div class="container-fluid py-4">
            <div class="row mt-4">
                <div class="col-lg-12 mb-lg-0 mb-4">

                    <!-- Button trigger modal -->
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
                        Tambah Cabang
                    </button>
                    <div class="card ">
                        <div class="card-header pb-0 p-3">
                            <div class="d-flex justify-content-between">
                                <h6 class="mb-2">Data Cabang</h6>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col">No</th>
                                        <th scope="col">Nama</th>
                                        <th scope="col">Olsera App ID</th>
                                        <th scope="col">Sync</th>
                                        <th scope="col">Last Sync</th>
                                        <th scope="col">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($cabang as $key => $user)
                                        <tr>
                                            <th scope="row">{{ $key+1 }}</th>
                                            <th scope="row">{{ $user->nama }}</th>
                                            <td class="text-sm">
                                                {{ $user->olsera_app_id ?: '—' }}
                                            </td>
                                            <td>
                                                @if (!$user->olsera_app_id || !$user->olsera_secret_key)
                                                    <span class="badge bg-gradient-secondary">Belum diatur</span>
                                                @elseif ($user->sync_aktif)
                                                    <span class="badge bg-gradient-success">Aktif</span>
                                                @else
                                                    <span class="badge bg-gradient-warning">Nonaktif</span>
                                                @endif
                                            </td>
                                            <td class="text-sm">
                                                {{ $user->last_sync ? $user->last_sync->translatedFormat('d M Y, H:i') : '—' }}
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-info mb-0" data-toggle="modal"
                                                    data-target="#editCabang{{ $user->id }}">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </button>
                                                <a href="/del.cabang?id={{ $user->id }}" class="btn btn-danger mb-0"><i
                                                        class="bi bi-trash3-fill"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </main>


    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Tambah Cabang</h5>
                </div>
                <form action="/add.cabang" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="nama">Nama</label>
                            <input type="text" class="form-control" name="nama" value="{{ old('nama') }}"
                                required>
                            <!-- Tampilkan pesan error untuk field nama -->
                            @error('nama')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="olsera_app_id">Olsera App ID</label>
                            <input type="text" class="form-control" name="olsera_app_id"
                                value="{{ old('olsera_app_id') }}" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="olsera_secret_key">Olsera Secret Key</label>
                            <input type="password" class="form-control" name="olsera_secret_key"
                                autocomplete="new-password">
                            <small class="text-muted">Disimpan terenkripsi dan tidak pernah ditampilkan lagi.</small>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="sync_aktif" value="1" checked
                                id="sync_aktif_baru">
                            <label class="form-check-label" for="sync_aktif_baru">Aktifkan sinkronisasi otomatis</label>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- Modal Edit per cabang -->
    @foreach ($cabang as $item)
        <div class="modal fade" id="editCabang{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Cabang — {{ $item->nama }}</h5>
                    </div>
                    <form action="/edit.cabang" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{ $item->id }}">
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Nama</label>
                                <input type="text" class="form-control" name="nama" value="{{ $item->nama }}" required>
                            </div>
                            <div class="form-group">
                                <label>Olsera App ID</label>
                                <input type="text" class="form-control" name="olsera_app_id"
                                    value="{{ $item->olsera_app_id }}" autocomplete="off">
                            </div>
                            <div class="form-group">
                                <label>Olsera Secret Key</label>
                                <input type="password" class="form-control" name="olsera_secret_key"
                                    placeholder="{{ $item->olsera_secret_key ? 'Kosongkan bila tidak diubah' : 'Belum diisi' }}"
                                    autocomplete="new-password">
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="sync_aktif" value="1"
                                    id="sync_aktif_{{ $item->id }}" {{ $item->sync_aktif ? 'checked' : '' }}>
                                <label class="form-check-label" for="sync_aktif_{{ $item->id }}">Aktifkan sinkronisasi
                                    otomatis</label>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach


    @include('page.footer')
