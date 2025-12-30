@extends('layouts.app')

@push('head-script')
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.0.3/css/buttons.dataTables.min.css">
@endpush

@section('content')
{!! $html->table() !!}
@endsection

@push('footer-script')
<script src="https://cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons.min.js"></script>
<script src="/vendor/datatables/buttons.server-side.js"></script>
{!! $html->scripts() !!}
@endpush
