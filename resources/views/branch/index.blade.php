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
<h1>Branches</h1>
@stop

@section('content')

<div class="row">

    <div class="col-md-6">
        <!-- Button trigger modal -->
        <a href="{{route('branch_create')}}"><button type="button" class="btn btn-primary">
                Add New Branch
            </button>
        </a>
    </div>
    <div class="mt-2 col-md-12">
        <div class="card">
            <div class="card-header bg-olive">
                <strong>Branches List</strong>
            </div>
            <div class="card-body">
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 7%" scope="col">No.</th>
                            <th scope="col">Name</th>
                            <th style="width: 20%" scope="col">Address</th>
                            <th style="width: 5%" scope="col">City</th>
                            <th style="width: 10%" scope="col">Zipcode</th>
                            <th style="width: 10%" scope="col">State</th>
                            <th style="width: 15%" scope="col">Country</th>
                            <th style="width: 10%" scope="col">No. of Employees</th>
                            <th style="width: 10%" scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $a = 0;
                        ?>
                        @foreach($branches as $branch)
                        <tr>
                            <td>{{$branch->id}}</td>
                            <td>{{$branch->name}}</td>
                            <td>{{$branch->address}}</td>
                            <td>{{$branch->city}}</td>
                            <td>{{$branch->zipcode}}</td>
                            <td>{{$branch->state->name}}</td>
                            <td>{{$branch->country->name}}</td>
                            <td>{{$branch->active_employees->count()}}</td>
                            <td>
                                <a href="{{route('branch_edit',$branch)}}" class="btn btn-info btn-sm"><i
                                        class="fa fa-pencil-alt"></i></a>
                                <a href="{{route('branch_delete',$branch)}}" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Warning: Are you sure you want to delete this branch? Deleting this branch will also delete all users associated with it')"><i
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
