@extends('layouts.app')
@section('content')
<a href="{{ url('admin/test-sheets/create') }}">Create Manually</a>
<a href="{{ url('admin/test-sheets/import') }}">Import from Excel</a>
<table>
    <thead>
        <tr><th>Name</th><th>Fields Count</th><th>Equipments</th></tr>
    </thead>
    <tbody>
        @foreach($templates as $t)
        <tr>
            <td><a href="{{ url('admin/test-sheets/'.$t->id) }}">{{ $t->name }}</a></td>
            <td>{{ $t->fields->count() }}</td>
            <td>
                @foreach($t->equipments as $eq)
                    {{ $eq->name }} (SN: {{ $eq->serial_number }})@if(!$loop->last), @endif
                @endforeach
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
