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
<h4>Leave Entitlement: {{$empType->name}}</h4>
<div class="row">
<?php

// function X($x){
// echo "<pre>";
// print_r($x);
// echo "</pre>";
// }

// X($allLeaveTypes->toArray());
// X($leaveEnt->toArray());

$leaveEntMap = [];
foreach ($leaveEnt->toArray() as $row) {
    $leaveEntMap[$row["leave_type_id"]] = $row;
}
// X($leaveEntMap);

?>


    <!-- Leave Days Form -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-olive">
                <strong>Leave entitlement for {{$empType->name}}</strong>
            </div>
            <div class="card-body">
                <table class="table table-sm table-bordered">
                <tbody>
                    <tr>
                    <th>Leave Name</th>
                        @foreach($allLeaveTypes as $lt)

                        <td>{{$lt->name}} </td>
                        @endforeach
                    </tr>
                    <tr>
                    <th>No. of Days</th>
                        @foreach($allLeaveTypes as $lt)
                        <td> ( {{array_key_exists($lt->id, $leaveEntMap) ? $leaveEntMap[$lt->id]["no_of_days"] : "-"}} )</td>
                        @endforeach
                    </tr>
                 </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Leave Checkboxes -->
    <div class="col-md-12">
        <div class="card">
            <div class ="card-body">
                <label>Applicable Leaves:</label>
                <span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="Choose all leave types that are applicable to {{$empType->name}}">
                    <i class="fa fa-info-circle" style="pointer-events: none;" disabled></i>
                </span>
                <div class="checkbox">
                   
                    @foreach($allLeaveTypes as $type)
                    <?php
                        $leaveTypeName = str_replace(' ', '_', $type->name);
                    ?>
                        <label><input type="checkbox" value="" data-toggle="collapse" data-target="#{{$leaveTypeName}}">{{$type->name}}</label>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Leave Days Form -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
            <form method="POST" action="{{route('leaveent_store', $empType)}}">
                {{ csrf_field() }}
                    @foreach($allLeaveTypes as $type)
                    <?php
                        $leaveTypeName = str_replace(' ', '_', $type->name);
                    ?>
                    <div id="{{$leaveTypeName}}" class="collapse">
                        <label for="{{$type->name}}">{{$type->name}} Leave Entitlement:</label>
                        <input class="form-control" type = "number" name="leave_{{$type->id}}" value="{{array_key_exists($type->id, $leaveEntMap) ? $leaveEntMap[$type->id]['no_of_days'] : 0}}" />
                    </div>
                    @endforeach
                    <button type="submit" class="btn btn-success">Submit</button>
            </form>
            </div>
        </div>
    </div>
</div>
@endsection