@include('page.header')

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
                        <li class="breadcrumb-item text-sm text-white">Back Office | {{ Auth::user()->nama }}</li>
                    </ol>
                    <h6 class="font-weight-bolder text-white mb-0">Akun</h6>
                </nav>

            </div>
        </nav>
        <!-- End Navbar -->
        <div class="container-fluid py-4">
            <div class="row mt-4">
                <div class="col-lg-12 mb-lg-0 mb-4">

                    <!-- Button trigger modal -->
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
                        Tambah Akun
                    </button>
                    <div class="card ">
                        <div class="card-header pb-0 p-3">
                            <div class="d-flex justify-content-between">
                                <h6 class="mb-2">Data Rombongan Hari Ini</h6>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col">Nama</th>
                                        <th scope="col">Role</th>
                                        <th scope="col">Hapus</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $user)
                                        <tr>
                                            <th scope="row">{{ $user->nama }}</th>
                                            <td>
                                                @if ($user->role == 'fo')
                                                    Front Office
                                                @elseif($user->role == 'bo')
                                                    Back Office
                                                @elseif($user->role == 'kasir')
                                                    Kasir
                                                @endif
                                            </td>
                                            <td>

                                                <a href="/del.user?id={{ $user->id }}" class="btn btn-danger"><i
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
                    <h5 class="modal-title" id="exampleModalLabel">Tambah Akun</h5>
                </div>
                <form action="/add.user" method="POST">
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
                            <label for="role">Role</label>
                            <select class="form-select" name="role" required>
                                <option selected disabled>Pilih Role</option>
                                <option value="fo" {{ old('role') == 'fo' ? 'selected' : '' }}>Front Office</option>
                                <option value="kasir" {{ old('role') == 'kasir' ? 'selected' : '' }}>Kasir</option>
                                <option value="bo" {{ old('role') == 'bo' ? 'selected' : '' }}>Back Office</option>
                            </select>
                            <!-- Tampilkan pesan error untuk field role -->
                            @error('role')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="pin">Pin</label>
                            <input type="number" class="form-control" name="pin" value="{{ old('pin') }}"
                                required>
                            <!-- Tampilkan pesan error untuk field pin -->
                            @error('pin')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
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


    @include('page.footer')
