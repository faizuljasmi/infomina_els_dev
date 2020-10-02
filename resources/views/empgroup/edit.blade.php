@extends('adminlte::page')

@section('content')
<h4>Edit Employee Group: {{$empGroup->name}}</h4>
@include('empgroup.partials.form', ['action' => route('empgroup_update', $empGroup), 'empGroup' => $empGroup])

@endsection