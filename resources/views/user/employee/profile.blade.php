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
<h4>Profile for {{$user->name}}</h4>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-teal">
                <strong>Profile</strong>
            </div>
            <div class="card-body">
                <form>
                    <fieldset disabled>
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label for="name">Name</label>
                                <input type="name" class="form-control" id="name" placeholder="{{$user->name}}">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" placeholder="{{$user->email}}">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="gender">Gender</label>
                                <input type="email" class="form-control" id="email" placeholder="{{$user->wspace_user->gender}}">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="type">Employee Type</label>
                                <input type="text" class="form-control" id="type" placeholder="{{$empType->name}}">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="type">Employee Group</label>
                                <input type="text" class="form-control" id="type" placeholder="{{$empGroup->name}}">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="type">Job Title</label>
                                <input type="text" class="form-control" id="type" placeholder="{{$user->wspace_user->job_title}}">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="type">Join Date</label>
                                <input type="text" class="form-control" id="type"
                                    placeholder="{{ \Carbon\Carbon::parse($user->wspace_user->join_date)->format('d/m/Y')}}">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="type">Emergency Contact Name</label>
                                <input type="text" class="form-control" id="type"
                                    placeholder="{{$user->wspace_user->emergency_contact_name}}">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="type">Emergency Contact No.</label>
                                <input type="text" class="form-control" id="type"
                                    placeholder="{{$user->wspace_user->emergency_contact_no}}">
                            </div>
                        </div>
                    </fieldset>
                </form>
                <div class="float-sm-right"><span class="d-inline-block" tabindex="0" data-toggle="tooltip"
                        title="Edit user profile and leave">
                        <a href="{{route('edit_profile')}}" class="btn btn-info">Edit</a>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-teal">
                <strong>Approval Authorities</strong>
            </div>
            <div class="card-body">
                @if($empAuth === null)
                <strong>No record found</strong>
                @else
                <table class="table table-bordered">
                    <tr>
                        <th>Level</th>
                        <th>Name</th>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td>{{isset($empAuth->authority_1_id) ? $empAuth->authority_one->name:'NA'}}</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>{{isset($empAuth->authority_2_id) ? $empAuth->authority_two->name:'NA'}}</td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>{{isset($empAuth->authority_3_id) ? $empAuth->authority_three->name:'NA'}}</td>
                    </tr>
                </table>
                @endif
            </div>
        </div>
    </div>





    <!-- Leave Days Form -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-teal">
                <strong>Leave Record</strong>

            </div>
            <div class="card-body">
                <table class="table table-sm table-bordered">
                    <tbody>
                        <tr>
                            <th>Leave Name</th>
                            @foreach($leaveTypes as $lt)
                            <td><strong>{{$lt->name}}</strong></td>
                            @endforeach
                        </tr>
                        <tr>
                            <th>Entitled</th>
                            @foreach($leaveEnt as $le)
                            <td class="table-primary">{{$le->no_of_days}}</td>

                            @endforeach
                        </tr>
                        <tr>
                            <th>Brought Forward
                            </th>
                            @foreach($broughtFwd as $bf)
                            @if($bf->leave_type_id == '1')
                            <td class="table-success">{{isset($bf->no_of_days) ? $bf->no_of_days:'NA'}}</td>
                            @else
                            <td class="table-secondary"></td>
                            @endif
                            @endforeach
                        </tr>
                        <tr>
                            <th>Earned <small></th>
                            @foreach($leaveEarn as $le)
                            @foreach($broughtFwd as $bf)
                            @if($le->leave_type_id == $bf->leave_type_id)
                            <td class="table-success" data-toggle="tooltip"
                                title="{{$le->no_of_days - $bf->no_of_days}} (Earned) + {{$bf->no_of_days}} (Brought Forward)">
                                {{$le->no_of_days}}</td>
                            @endif
                            @endforeach
                            @endforeach
                        </tr>
                        <tr>
                            <th>Taken</th>
                            @foreach($leaveTak as $lt)
                            <td class="table-danger">{{$lt->no_of_days}}</td>
                            @endforeach
                        </tr>
                        {{-- <tr>
                            <th>Replacement</th>
                            @foreach($leaveEarn as $le)
                            @if($le->leave_type_id == "12")
                            <td class="table-success">{{$le->no_of_days}}</td>
                            @endif
                            @endforeach
                            <td class="table-secondary"></td>
                            <td class="table-secondary"></td>
                            <td class="table-secondary"></td>
                            <td class="table-secondary"></td>
                            <td class="table-secondary"></td>
                            <td class="table-secondary"></td>
                            <td class="table-secondary"></td>
                            <td class="table-secondary"></td>
                            <td class="table-secondary"></td>
                            <td class="table-secondary"></td>
                        </tr> --}}
                        <tr>
                            <th>Burnt <i class="fas fa-info-circle" data-toggle="tooltip" data-placement="top"
                                    title="Unused brought forward leaves will go here on 1 July"></i></th>
                            @if($burntLeave != null)
                            <td class="table-danger">{{$burntLeave->no_of_days}}</td>
                            @else
                            <td class="table-danger">0</td>
                            @endif
                            <td class="table-secondary"></td>
                            <td class="table-secondary"></td>
                            <td class="table-secondary"></td>
                            <td class="table-secondary"></td>
                            <td class="table-secondary"></td>
                            <td class="table-secondary"></td>
                            <td class="table-secondary"></td>
                            <td class="table-secondary"></td>
                            <td class="table-secondary"></td>
                            <td class="table-secondary"></td>
                            @if($burntReplacement != null)
                            <td class="table-danger">{{$burntReplacement->no_of_days}}</td>
                            @else
                            <td class="table-danger">0</td>
                            @endif
                        </tr>
                        <tr>
                            <th>Balance</th>
                            @foreach($leaveBal as $lb)
                            <td class="table-primary">{{$lb->no_of_days}}</td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>
@endsection
