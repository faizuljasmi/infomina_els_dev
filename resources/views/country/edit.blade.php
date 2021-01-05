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
<h1>{{$country->name}}</h1>
@stop

@section('content')

<div class="row">
    <div class="mt-2 col-md-12">
        <div class="card">
            <div class="card-header bg-olive">
                <strong>Details for: {{$country->name}}</strong>
            </div>
            <div class="card-body">
                <form method="POST" action="{{route('country_update',$country)}}">
                    @csrf
                    <!-- Edit Form -->
                    <div class="card card-primary">
                        <div class="card-body">
                            <div class="form-row">
                                <!-- Country Name -->
                                <label>Country Name</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="name" id="name"
                                        value="{{$country->name}}" required>
                                </div>
                            </div>
                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-success float-right mt-3">Update</button>
                        </div>
                    </div>
                </form>

                <h3>States for {{$country->name}}</h3>
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 7%" scope="col">No.</th>
                            <th style="width: 40%" scope="col">Name</th>
                            <th style="width: 15%" scope="col">No. of Branches</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $count = 0;?>
                        @foreach($country->states as $state)
                        <tr>
                            <td style="width: 7%" scope="col">{{++$count}}</td>
                            <th style="width: 40%" scope="col">{{$state->name}}</th>
                            <td style="width: 10%">{{isset($state) ? $state->branches->count() : 0}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <a href="{{route('state_store_specific',$country)}}"><button type="button"
                        class="btn btn-success float-right mt-3">Add State</button></a>
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
