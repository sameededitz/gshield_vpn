@extends('layouts.guest')
@section('content')
    <div class="section-authentication-signin d-flex align-items-center justify-content-center my-5 my-lg-0">
        <div class="container">
            <div class="row row-cols-1 row-cols-lg-2 row-cols-xl-3">
                <div class="col mx-auto">
                    <div class="card mb-0">
                        <div class="card-body">
                            <div class="p-4">
                                <div class="mb-3 text-center">
                                    <img src="{{ asset('assets/images/logo-img.png') }}" width="160px" alt="logo" />
                                </div>
                                <div class="mb-3 text-center">
                                    <img src="{{ asset('assets/images/icons/verified.png') }}" width="300px" alt="logo" />
                                </div>
                                <div class="text-center mb-4">
                                    <p class="mb-0"> {{ $status }} </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end row-->
        </div>
    </div>
@endsection
