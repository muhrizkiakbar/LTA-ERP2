@extends('layouts.backend.app')
@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 py-2 d-flex justify-content-between align-items-center">
                    <h3>DASHBOARD</h3>
                    <div>
                        <h5 class="text-secondary">{{ $meta['date'] }}, {{ $meta['branch'] }}</h5>
                    </div>
                </div>
                <div class="col-12">
                    <h2 style="color: #e2e8f0;">SALES ORDER</h2>
                    <div class="row">
                        {{-- SO TOTAL --}}
                        <div class="col-3">
                            <div class="card ">
                                <div class="card-body d-flex align-items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="4em" class="text-success"
                                        viewBox="0 0 576 512">
                                        <!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                        <path fill="#cbd5e1"
                                            d="M0 24C0 10.7 10.7 0 24 0H69.5c22 0 41.5 12.8 50.6 32h411c26.3 0 45.5 25 38.6 50.4l-41 152.3c-8.5 31.4-37 53.3-69.5 53.3H170.7l5.4 28.5c2.2 11.3 12.1 19.5 23.6 19.5H488c13.3 0 24 10.7 24 24s-10.7 24-24 24H199.7c-34.6 0-64.3-24.6-70.7-58.5L77.4 54.5c-.7-3.8-4-6.5-7.9-6.5H24C10.7 48 0 37.3 0 24zM128 464a48 48 0 1 1 96 0 48 48 0 1 1 -96 0zm336-48a48 48 0 1 1 0 96 48 48 0 1 1 0-96z" />
                                    </svg>
                                    <div class="ml-3">
                                        <h1>{{ $so['png'] }}</h1>
                                        <h5 class="text-secondary">TOTAL SO P&G</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- UNPROCESSED --}}
                        <div class="col-3">
                            <div class="card">
                                <div class="card-body d-flex align-items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="4em" class="text-success"
                                        viewBox="0 0 576 512">
                                        <!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                        <path fill="#cbd5e1"
                                            d="M0 24C0 10.7 10.7 0 24 0H69.5c22 0 41.5 12.8 50.6 32h411c26.3 0 45.5 25 38.6 50.4l-41 152.3c-8.5 31.4-37 53.3-69.5 53.3H170.7l5.4 28.5c2.2 11.3 12.1 19.5 23.6 19.5H488c13.3 0 24 10.7 24 24s-10.7 24-24 24H199.7c-34.6 0-64.3-24.6-70.7-58.5L77.4 54.5c-.7-3.8-4-6.5-7.9-6.5H24C10.7 48 0 37.3 0 24zM128 464a48 48 0 1 1 96 0 48 48 0 1 1 -96 0zm336-48a48 48 0 1 1 0 96 48 48 0 1 1 0-96z" />
                                    </svg>
                                    <div class="ml-3">
                                        <h1>{{ $so['mix'] }}</h1>
                                        <h5 class="text-secondary">TOTAL SO MIX</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- PROCESSED --}}
                         <div class="col-3">
                            <div class="card ">
                                <div class="card-body d-flex align-items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="4em" viewBox="0 0 448 512">
                                        <path fill="#fca5a5"
                                            d="M0 80V229.5c0 17 6.7 33.3 18.7 45.3l176 176c25 25 65.5 25 90.5 0L418.7 317.3c25-25 25-65.5 0-90.5l-176-176c-12-12-28.3-18.7-45.3-18.7H48C21.5 32 0 53.5 0 80zm112 32a32 32 0 1 1 0 64 32 32 0 1 1 0-64z" />
                                    </svg>
                                    <div class="ml-3">
                                        <h1 class="text-danger">{{ $so['unprocess_png'] }}</h1>
                                        <h5 class="text-secondary">UNPROCESSED SO PNG</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="card ">
                                <div class="card-body d-flex align-items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="4em" viewBox="0 0 448 512">
                                        <path fill="#fca5a5"
                                            d="M0 80V229.5c0 17 6.7 33.3 18.7 45.3l176 176c25 25 65.5 25 90.5 0L418.7 317.3c25-25 25-65.5 0-90.5l-176-176c-12-12-28.3-18.7-45.3-18.7H48C21.5 32 0 53.5 0 80zm112 32a32 32 0 1 1 0 64 32 32 0 1 1 0-64z" />
                                    </svg>
                                    <div class="ml-3">
                                        <h1 class="text-danger">{{ $so['unprocess_mix'] }}</h1>
                                        <h5 class="text-secondary">UNPROCESSED SO MIX</h5>
                                    </div>
                                </div>
                            </div>
                        </div> 
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
