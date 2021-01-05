@extends('adminlte::page')

@section('content_header')
@if(session()->has('message'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="icon fa fa-check"></i>
    {{ session()->get('message') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif
<h1>Countries</h1>
@stop

@section('content')

<div class="row">

    <div class="col-md-6">
        <!-- Button trigger modal -->
        <a href="{{route('countries_create')}}"><button type="button" class="btn btn-primary">
                Add Country
            </button>
        </a>
    </div>
    <div class="mt-2 col-md-12">
        <div class="card">
            <div class="card-header bg-olive">
                <strong>Countries List</strong>
            </div>
            <div class="card-body">
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 7%" scope="col">No.</th>
                            <th scope="col">Name</th>
                            <th scope="col">No. of States</th>
                            <th scope="col">No. of Branches</th>
                            <th style="width: 10%" scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($countries as $country)
                        <tr>
                            <td>{{$country->id}}</td>
                            <td>{{$country->name}}</td>
                            <td>{{$country->states->count()}}</td>
                            <td>{{$country->branches->count()}}</td>
                            <td>
                                <a href="{{route('country_edit',$country)}}" class="btn btn-info btn-sm"><i
                                        class="fa fa-pencil-alt"></i></a>
                                <a href="{{route('country_delete',$country)}}" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Warning: Are you sure you want to delete this country? Deleting this country will also delete all users associated with it')"><i
                                        class="fa fa-trash-alt"></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>


</div>
@endsection

@section('adminlte_js')
<script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
@stack('js')
@yield('js')
@endsection
