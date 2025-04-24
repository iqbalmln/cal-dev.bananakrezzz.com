@include('page.header')

<body class="">
    <main class="main-content  mt-0">
        <section>
            <div class="page-header min-vh-100">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-7 d-flex flex-column mx-lg-0 mx-auto">
                            <div class="card card-plain">
                                <div class="card-header pb-0 text-start">
                                    <div class="d-flex justify-content-center align-items-center mb-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50"
                                            fill="currentColor" class="bi bi-calculator text-primary"
                                            viewBox="0 0 16 16">
                                            <path
                                                d="M12 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z" />
                                            <path
                                                d="M4 2.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.5.5h-7a.5.5 0 0 1-.5-.5zm0 4a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3-6a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3-6a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5z" />
                                        </svg>
                                    </div>

                                    <h2 class="font-weight-bolder text-center">Apliaksi Kalkulator Crew Banana Krezzz
                                    </h2>
                                    <hr>
                                    @if (session('status'))
                                    <div class="alert alert-info alert-dismissible fade show text-center" role="alert">
                                      <strong class="text-white">{{ session('status') }}</strong><br>
                                      <button type="button" class="btn btn-sm btn-primary" data-dismiss="alert" aria-label="Close">
                                          <span aria-hidden="true">&times;</span>
                                      </button>
                                  </div>
                                  
                                    @endif

                                    <h5 class="font-weight-bolder">Log In</h5>
                                    <p class="mb-0">Masukan PIN mu untuk Log In</p>
                                </div>
                                <div class="card-body">
                                    <form role="form" method="POST" action="/login">
                                        @csrf
                                        <div class="mb-3">
                                            <input type="number" name="pin" class="form-control form-control-lg"
                                                placeholder="PIN" aria-label="PIN" required autofocus>
                                        </div>

                                        <div class="text-center">
                                            <button type="submit"
                                                class="btn btn-lg btn-primary btn-lg w-100 mt-1 mb-0">Log in</button>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </main>



    @include('page.footer')
