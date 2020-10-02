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
    <h2>Leave Type</h2>

    <!-- <div class="register-box">
        <div class="card">
            <div class="card-body register-card-body">
            <form method="POST" action="/emptype/create">
                {{ csrf_field() }}

                <div class="input-group mb-3">
                    <input type="text" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" value="{{ old('name') }}"
                           placeholder="Ex: Executive" autofocus>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-user"></span>
                        </div>
                    </div>

                    @if ($errors->has('name'))
                        <div class="invalid-feedback">
                            <strong>{{ $errors->first('name') }}</strong>
                        </div>
                    @endif
                </div>
                <button type="submit" class="btn btn-primary btn-block btn-flat">
                    Create
                </button>
            </form>
        </div> -->
        <!-- /.form-box -->
    <!-- </div> -->






<div class="row">
    
<div class ="col-md-6">
    <!-- Button trigger modal -->
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalCenter">
 Create New Leave Type
</button>

</div>

    <div class=" mt-2 col-md-12">
        <div class="card">
            <div class="card-header bg-teal">
                <strong>Leave Types List</strong>
            </div>
                <div class="card-body">
                <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                            <th style ="width: 7%" scope="col">No.</th>
                            <th style ="width: 7%" scope="col">ID</th>
                            <th scope="col">Name</th>
                            <th style="width: 20%" scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $a = 0;
                        ?>
                        @foreach($allLeaves as $type)
                            <tr>
                            <td>{{++$a}}</td>
                            <td>{{$type->id}}</td>
                            <td>{{$type->name}}</td>
                            <td>
                                <!-- <button class="btn btn-success btn-sm">Set Leave Entitlement</button> -->
                                <button class="btn btn-info btn-sm"><i class="fa fa-pencil-alt"></i></button>
                                <a href="{{route('leavetype_delete',$type)}}" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this leave type?')"><i class="fa fa-trash-alt"></i></a>
                            </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
        </div>
</div>

<!-- Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Create New Leave Type</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <form method="POST" action="{{route('leavetype_create')}}">
                    {{ csrf_field() }}
                    <div class = "form-row">

                    <!-- Employee Type Name -->
                    <label for = "typename">Leave Type Name</label>
                        <input type="text" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" value="{{ old('name') }}" id="typename"
                            placeholder="Ex: Annual" autofocus>
                    

                        @if ($errors->has('name'))
                            <div class="invalid-feedback">
                                <strong>{{ $errors->first('name') }}</strong>
                            </div>
                        @endif
                        
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
@endsection

@section('adminlte_js')
    <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
    @stack('js')
    @yield('js')
@endsection