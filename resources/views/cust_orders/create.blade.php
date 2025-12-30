@extends('layouts.app')

@section('content')

@section('create-button')
<a href="#" id="preview-form" class="btn btn-info btn-sm m-l-15"><i class="ti-save"></i>
    Submit Order
</a>
@endsection
@include('cust_orders._form')
@endsection