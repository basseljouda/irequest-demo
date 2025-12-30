@extends('layouts.app')

@section('content')

@section('create-button')
<a href="#" id="preview-form" class="btn btn-info btn-sm m-l-15"><i class="ti-save-alt"></i>
    Save Order
</a>
@endsection
@include('orders._form')
@endsection