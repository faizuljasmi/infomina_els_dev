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
<h1>States</h1>
@stop

@section('content')

<div class="row">

    <div class="col-md-6">
        <!-- Button trigger modal -->
        <a href="{{route('state_create')}}"><button type="button" class="btn btn-primary">
                Add New State
            </button>
        </a>
    </div>
    <div class="mt-2 col-md-12">
        <div class="card">
            <div class="card-header bg-olive">
                <strong>States List</strong>
            </div>
            <div class="card-body">
                @foreach($countries as $country)
                <table class="table table-sm table-bordered">
                    <h2 class="mt-3">{{$country->name}}</h2>
                    <thead>
                        <tr>
                            <th style="width: 7%" scope="col">No.</th>
                            <th style="width: 40%" scope="col">Name</th>
                            <th style="width: 20%" scope="col">No. of Branches</th>
                            <th style="width: 5%" scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $count = 0;?>
                        @foreach($country->states as $state)
                        <tr>
                            <td style="width: 7%" scope="col">{{++$count}}</td>
                            <th style="width: 40%" scope="col">{{$state->name}}</th>
                            <td style="width: 15%" scope="col">{{$state->branches->count()}}</td>

                            <td style="width: 10%">
                                <a href="{{route('state_edit',$state)}}"><button type="button"
                                        onclick="HolidayCreate();" class="btn btn-primary btn-sm"><i
                                            class="fa fa-pencil-alt"></i></button></a>
                                <a href="{{route('state_delete',$state)}}" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Are you sure you want to delete {{$state->name}}?')"><i
                                        class="fa fa-trash-alt"></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endforeach

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
