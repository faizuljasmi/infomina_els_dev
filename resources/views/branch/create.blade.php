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
                <strong>Add new branch</strong>
            </div>
            <div class="card-body">
                <form method="POST" action="{{route('branch_store')}}">
                    @csrf
                    <!-- Create Form -->
                    <div class="card card-primary">
                        <div class="card-body">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <!-- Branch Name -->
                                    <label>Name</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="name" id="name" required>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <!-- Branch Address -->
                                    <label>Address</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="address" id="address" required>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <!-- Branch Zipcode -->
                                    <label>Zipcode</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="zipcode" id="zipcode" required>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <!-- Branch Zipcode -->
                                    <label>City</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="city" id="city" required>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <!-- Branch Zipcode -->
                                    <label>Country</label>
                                    <div class="input-group">
                                        <select class="form-control" name="country_id" id="country_id" required>
                                            <option value="">Select One</option>
                                            @foreach($countries as $country)
                                            <option value="{{$country->id}}"
                                                {{isset($state->country_id) && $state->country_id == $country->id ? 'selected':''}}>
                                                {{$country->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <!-- Branch Zipcode -->
                                    <label>State</label>
                                    <div class="input-group">
                                        <select class="form-control" name="state_id" id="state_id" required>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-success float-right mt-3">Add</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
    $(document).on('change', '#country_id', function() {
        $('#state_id').empty();
        var country_id = $(this).val();
        var div = $(this).parent();
        var op = " ";
        $.ajax({
            type: 'get',
            url: '{!!URL::to('/states/filter')!!}',
            data: {'id':country_id},
            success: function(data){
                for (var i = 0; i < data.length; i++){
                    op += '<option value="'+data[i].id+'">'+data[i].name+'</option>';
                }
                $('#state_id').append(op);
            },
            error: function(){
                console.log('success');
            },
        });
    });
});
</script>
@endsection

@section('adminlte_js')
<script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
@stack('js')
@yield('js')
@endsection
