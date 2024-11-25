@extends('layouts.backend.app')
@section('content')
<div class="container text-center">
  <div class="row">
    <div class="col-md-8 offset-md-2">
       <h1 class="mb-5">Laravel 8 Barcode Generator</h1>
       <div>{!! DNS1D::getBarcodeHTML($barcode, 'C128', 1, 22) !!}</div></br>
    </div>
  </div>
</div>
@endsection