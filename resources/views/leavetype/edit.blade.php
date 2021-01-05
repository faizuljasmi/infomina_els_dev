@extends('adminlte::page')

@section('content')
@if(session()->has('message'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="icon fa fa-check"></i>
    {{ session()->get('message') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

<div class="row">
    <div class=" mt-2 col-md-12">
        <div class="card">
            <div class="card-header">
            <h3>Edit Leave Type: {{$leaveType->name}}</h3>
            </div>
            <div class="card-body"></div>
        </div>
    </div>
</div>
@endsection
