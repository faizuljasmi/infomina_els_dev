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
<h1>{{$state->name}}</h1>
@stop

@section('content')

<div class="row">
    <div class="mt-2 col-md-12">
        <div class="card">
            <div class="card-header bg-olive">
                <strong>Details for: {{$state->name}}</strong>
            </div>
            <div class="card-body">
                <form method="POST" action="{{route('state_update',$state)}}">
                    @csrf
                    <!-- Edit Form -->
                    <div class="card card-primary">
                        <div class="card-body">
                            <div class="form-row">
                                <!-- Country Name -->
                                <div class="form-group col-md-6">
                                    <label>State Name</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="name" id="name"
                                            value="{{$state->name}}" required>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>State Country</label>
                                    <select class="form-control" id="country_id" name="country_id" required>
                                        @foreach($countries as $country)
                                        <option value="{{$country->id}}"
                                            {{isset($state->country_id) && $state->country_id == $country->id ? 'selected':''}}>
                                            {{$country->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-success float-right mt-3">Update</button>
                        </div>
                    </div>
                </form>

                <h3>Branches for {{$state->name}}</h3>
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 7%" scope="col">No.</th>
                            <th style="width: 40%" scope="col">Name</th>
                            <th style="width: 15%" scope="col">No. of Employees</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $count = 0;?>
                        @foreach($state->branches as $branch)
                        <tr>
                            <td style="width: 7%" scope="col">{{++$count}}</td>
                            <th style="width: 40%" scope="col">{{$branch->name}}</th>
                            <th style="width: 40%" scope="col">{{$branch->active_employees->count()}}</th>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <a href="{{url('/branches/index')}}"><button type="button" class="btn btn-success float-right mt-3">Add
                        Branch</button></a>
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
