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
@stop

@section('content')

<div class="row">
    <div class="mt-2 col-md-12">
        <div class="card">
            <div class="card-header bg-olive">
                <strong>Add new country</strong>
            </div>
            <div class="card-body">
                <form method="POST" action="{{route('country_store')}}">
                    @csrf
                    <!-- Create Form -->
                    <div class="card card-primary">
                        <div class="card-body">
                            <div class="form-row">
                                <!-- Country Name -->
                                <label>Country Name</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="name" id="name" required>
                                </div>
                            </div>
                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-success float-right mt-3">Create</button>
                        </div>
                    </div>
                </form>
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
