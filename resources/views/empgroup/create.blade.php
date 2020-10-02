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
<h1>Employee Group</h1>
@stop

@section('content')

<div class="row">

    <div class="col-md-6">
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalCenter">
            Create New Group
        </button>

    </div>
    <div class="mt-2 col-md-12">
        <div class="card">
            <div class="card-header bg-olive">
                <strong>Employee Groups List</strong>
            </div>
            <div class="card-body">
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 7%" scope="col">Type ID</th>
                            <th scope="col">Name</th>
                            <th scope="col">Group Leader</th>
                            <th style="width: 20%" scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($allGroups as $ag)
                        <tr>
                            <td>{{$ag->id}}</td>
                            <td>{{$ag->name}}</td>
                            <td>{{isset($ag->leader)? $ag->leader->name:"Group leader has not been set"}}</td>
                            <td>
                                <a href="{{route('empgroup_edit',$ag)}}" class="btn btn-info btn-sm"><i
                                        class="fa fa-pencil-alt"></i></a>
                                <a href="{{route('empgroup_delete',$ag)}}" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Warning: Are you sure you want to delete this employee group? Deleting this group will also delete all users associated with it')"><i
                                        class="fa fa-trash-alt"></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Create New Employee Group</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{route('empgroup_store')}}">
                        {{ csrf_field() }}
                        <div class="form-row">

                            <div class="form-group">
                                <!-- Employee Type Name -->
                                <label for="typename">Employee Group Name</label>
                                <input type="text" name="name"
                                    class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                                    value="{{ old('name') }}" id="typename" placeholder="Ex: IT1" autofocus>
                                @if ($errors->has('name'))
                                <div class="invalid-feedback">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </div>
                                @endif
                            </div>

                            <div class="form-group">
                                <select class="form-control" name="group_leader_id" required>
                                    <option selected value="">Choose Group Leader</option>
                                    @foreach($allUsers as $au)
                                    <option value="{{$au->id}}"
                                        {{ (old('group_leader_id') == $au->id ? "selected":"") }}>
                                        {{$au->name}}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">
                                    Please select a group leader
                                </div>
                            </div>



                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-success">Create</button>
                        </div>
                    </form>
                </div>
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
