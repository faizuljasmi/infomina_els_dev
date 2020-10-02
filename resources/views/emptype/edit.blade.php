@extends('adminlte::page')

@section('content')
<h4>Edit Employee Type: {{$empType->name}}</h4>
@include('emptype.partials.form', ['action' => route('emptype_update', $empType), 'empType' => $empType])

@endsection