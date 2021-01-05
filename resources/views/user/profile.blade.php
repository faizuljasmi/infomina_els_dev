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
                            <div class="form-group col-md-6">
                                <label for="name">Name</label>
                                <input type="name" class="form-control" id="name" placeholder="{{$user->name}}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="staff_id">Staff ID</label>
                                <input type="text" class="form-control" id="staff_id" placeholder="{{$user->staff_id}}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" placeholder="{{$user->email}}">
                            </div>
                            @if (Gate::forUser(Auth::user())->allows('admin-dashboard'))
                            <div class="form-group col-md-6">
                                <label for="level">User Type</label>
                                <input type="text" class="form-control" id="level" placeholder="{{$user->user_type}}">
                            </div>
                            @endif
                            <div class="form-group col-md-6">
                                <label for="gender">Gender</label>
                                <input type="email" class="form-control" id="email" placeholder="{{$user->gender}}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="type">Employee Type</label>
                                <input type="text" class="form-control" id="type" placeholder="{{$empType->name}}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="type">Job Title</label>
                                <input type="text" class="form-control" id="type" placeholder="{{$user->job_title}}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="type">Join Date</label>
                                <input type="text" class="form-control" id="type"
                                    placeholder="{{ \Carbon\Carbon::parse($user->join_date)->format('d/m/Y')}}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="type">Emergency Contact Name</label>
                                <input type="text" class="form-control" id="type"
                                    placeholder="{{$user->emergency_contact_name}}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="type">Emergency Contact No.</label>
                                <input type="text" class="form-control" id="type"
                                    placeholder="{{$user->emergency_contact_no}}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="type">Branch</label>
                                <input type="text" class="form-control" id="type"
                                    placeholder="{{$user->branch->name}}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="type">Employee Groups</label>
                                <table class="table table-striped table-bordered table-sm">
                                    <tr>
                                        <th>Group No.</th>
                                        <th>Group Name</th>
                                        <th>Role</th>
                                    </tr>
                                    <tr>
                                        <td>1</td>
                                        <td>{{isset($empGroup->name) ? $empGroup->name:'NA'}}</td>
                                        <td>
                                            @if(isset($empGroup->group_leader_id))
                                            {{($empGroup->group_leader_id == $user->id ? 'Leader': 'Member')}}
                                            @else
                                            NA
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>{{isset($empGroup2->name) ? $empGroup2->name:'NA'}}</td>
                                        <td>
                                            @if(isset($empGroup2->group_leader_id))
                                            {{($empGroup2->group_leader_id == $user->id ? 'Leader': 'Member')}}
                                            @else
                                            NA
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>{{isset($empGroup3->name) ? $empGroup3->name:'NA'}}</td>
                                        <td>
                                            @if(isset($empGroup3->group_leader_id))
                                            {{($empGroup3->group_leader_id == $user->id ? 'Leader': 'Member')}}
                                            @else
                                            NA
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>4</td>
                                        <td>{{isset($empGroup4->name) ? $empGroup4->name:'NA'}}</td>
                                        <td>
                                            @if(isset($empGroup4->group_leader_id))
                                            {{($empGroup4->group_leader_id == $user->id ? 'Leader': 'Member')}}
                                            @else
                                            NA
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>5</td>
                                        <td>{{isset($empGroup5->name) ? $empGroup5->name:'NA'}}</td>
                                        <td>
                                            @if(isset($empGroup5->group_leader_id))
                                            {{($empGroup5->group_leader_id == $user->id ? 'Leader': 'Member')}}
                                            @else
                                            NA
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </fieldset>
                </form>
                @if($user_insesh->user_type == "Admin" )
                <div class="float-sm-right"><span class="d-inline-block" tabindex="0" data-toggle="tooltip"
                        title="Edit user profile and leave">
                        <a href="{{route('user_edit', $user->id)}}" class="btn btn-primary">Edit</a>
                    </span>
                </div>
                @endif
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
                <div class="float-sm-right mt-3"><button type="button" class="btn btn-primary" data-toggle="modal"
                        data-target="#createAuthority">Create</button></div>
                <div class="modal fade" id="createAuthority" tabindex="-1" role="dialog"
                    aria-labelledby="createAuthorityTitle" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">Create Approval Authorities for
                                    {{$user->name}}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                @include('leaveauth.partials.form', ['action' => route('approval_auth_create', $user),
                                'user' => $user])
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <strong>Record found</strong>
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
                @if($user_insesh->user_type == "Admin" )
                <div class="float-sm-right mt-3"><button type="button" class="btn btn-primary" data-toggle="modal"
                        data-target="#editAuthority">Edit</button></div>
                        @endif
                <div class="modal fade" id="editAuthority" tabindex="-1" role="dialog"
                    aria-labelledby="editAuthorityTitle" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">Edit Approval Authorities for
                                    {{$user->name}}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                @include('leaveauth.partials.form', ['action' => route('approval_auth_update',
                                $empAuth), 'empAuth' => $empAuth])
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>


    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-teal">
                <strong>Applications History <i class="fas fa-info-circle" data-toggle="tooltip"
                        data-placement="top"
                        title="This table shows you leave applications history"></i></strong>
                        @if($user_insesh->user_type == "Admin")
                        <div class="float-sm-right"><a href="{{route('apply_for', $user)}}"><button type="button" class="btn btn-primary">Apply On Behalf</button></a></div>
                        @endif
            </div>
            <div class="card-body">
                <h6><strong>Displaying {{$leaveHist->count()}} of {{$leaveHist->total()}} records.</strong>
                </h6>
                <table class="table table-bordered">
                    @if($leaveHist->count() > 0)
                    <thead>

                        <tr>
                            <th scope="col">No.</th>
                            <th scope="col">Leave Type @sortablelink('leaveType.name','',[])</th>
                            <th scope="col">Duration @sortablelink('total_days','',[])</th>
                            <th scope="col">From @sortablelink('date_from','',[])</th>
                            <th scope="col">To @sortablelink('date_to','',[])</th>
                            <th scope="col">Date Submitted @sortablelink('created_at','',[])</th>
                            <th scope="col">Status</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $count = ($leaveHist->currentPage()-1) * $leaveHist->perPage(); @endphp
                        @foreach($leaveHist as $la)

                        <tr>
                            <td>{{++$count}}</td>
                            <td>{{$la->leaveType->name}}</td>
                            <td>{{$la->total_days}} day(s)</td>
                            <td>{{ \Carbon\Carbon::parse($la->date_from)->isoFormat('ddd, D MMM YY')}}</td>
                            <td>{{ \Carbon\Carbon::parse($la->date_to)->isoFormat('ddd, D MMM YY')}}</td>
                            <td>{{ \Carbon\Carbon::parse($la->created_at)->diffForHumans()}}</td>
                            <td>
                                @if($la->status == 'PENDING_1')
                                <span class="badge badge-warning" data-toggle="tooltip"
                                    title="Your application is pending on lvl 1"><i class="far fa-clock"></i>
                                    Lvl 1</span>
                                @elseif($la->status == 'PENDING_2')
                                <span class="badge badge-warning" data-toggle="tooltip"
                                    title="Your application is pending on lvl 2"><i class="far fa-clock"></i>
                                    Lvl 2</span>
                                @elseif($la->status == 'PENDING_3')
                                <span class="badge badge-warning" data-toggle="tooltip"
                                    title="Your application is pending on lvl 3"><i class="far fa-clock"></i>
                                    Lvl 3</span>
                                @elseif($la->status == 'APPROVED')
                                <span class="badge badge-success" data-toggle="tooltip"
                                    title="Your application has been approved"><i
                                        class="far fa-check-circle"></i></span>
                                @elseif($la->status == 'DENIED_1')
                                <span class="badge badge-danger" data-toggle="tooltip"
                                    title="Your application has been denied by lvl 1"><i class="fas fa-ban"></i>
                                    Lvl 1</span>
                                @elseif($la->status == 'DENIED_2')
                                <span class="badge badge-danger" data-toggle="tooltip"
                                    title="Your application has been denied by lvl 2"><i class="fas fa-ban"></i>
                                    Lvl 2</span>
                                @elseif($la->status == 'DENIED_3')
                                <span class="badge badge-danger" data-toggle="tooltip"
                                    title="Your application has been denied by lvl 3"><i class="fas fa-ban"></i>
                                    Lvl 3</span>
                                @elseif($la->status == 'CANCELLED')
                                <span class="badge badge-secondary" data-toggle="tooltip"
                                    title="This application has been cancelled">Cancelled</span>
                                @endif
                            </td>
                            <td><a href="{{route('view_application', $la->id)}}" class="btn btn-success btn-sm"
                                    data-toggle="tooltip" title="View leave application"><i
                                        class="fa fa-eye"></i></a></td>
                        </tr>

                        @endforeach
                        {{$leaveHist->links()}}
                        @else
                        <th>No Record Found</th>
                        @endif

                    </tbody>
                </table>
                {!! $leaveHist->appends(\Request::except('page'),['history' =>
                $leaveHist->currentPage()])->render() !!}
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
                            @if($user_insesh->user_type == "Admin")
                                @if ($leaveEarn->count() == 0)<small><a href=""
                                        onclick="return alert('Please set this year\'s\ leave earnings before setting carry forward leaves')">Edit</a></small>
                                @else <small><a href="" data-toggle="modal"
                                        data-target="#setBroughtForward">Edit</a></small>
                                @endif
                            @endif
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
                            <th>Earned
                            @if($user_insesh->user_type == "Admin")
                            <small><a href="" data-toggle="modal" data-target="#setEarnings">Edit</a></small>
                            @endif
                            </th>
                            @foreach($leaveEarn as $le)
                            @foreach($broughtFwd as $bf)
                            @if($le->leave_type_id == $bf->leave_type_id )
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
                                    @if($lt->leave_type_id == '1')
                                    <?php $taken = $lt->no_of_days;
                                          $bfwd =  $broughtFwd[0]->no_of_days;
                                          $frmBfwd = 0;
                                          $frmAnnual = 0;
                                        if($total_ann_taken_first_half <= $bfwd){$frmBwd = $total_ann_taken_first_half; $frmAnnual = $taken - $total_ann_taken_first_half;}
                                        elseif($total_ann_taken_first_half > $bfwd){ $frmBwd = $bfwd; $frmAnnual = $taken - $bfwd;} ?>
                                    <td class="table-danger" data-toggle="tooltip"
                                        title="{{$frmBwd}} from Brought Forward + {{$frmAnnual}} from Annual Leave"><a
                                            href="#leaveRecord"><u>{{$lt->no_of_days}}</u></a></td>
                                    @else
                                    <td class="table-danger">{{$lt->no_of_days}}</td>
                                    @endif
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

                <!-- MODAL FOR LEAVE EARNINGS SETTINGS -->
                <div class="modal fade" id="setEarnings" tabindex="-1" role="dialog" aria-labelledby="setEarningsTitle"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">Set Leave Earnings for
                                    {{$user->name}}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                @include('leave.partials.form', ['action' => route('earnings_set', $user), 'user' =>
                                $user, 'leave' => $leaveEarn])
                            </div>
                        </div>
                    </div>
                </div>

                <!-- MODAL FOR BROUGHT FORWARD LEAVE SETTINGS -->
                <div class="modal fade" id="setBroughtForward" tabindex="-1" role="dialog"
                    aria-labelledby="setBroughtForward" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">Set Brought Forward Leaves for
                                    {{$user->name}} <i class="fas fa-info-circle" data-toggle="tooltip"
                                        title="All fields must be filled out, even if the assigned days is 0"></i></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                @include('leave.partials.form', ['action' => route('brought_fwd_set', $user), 'user' =>
                                $user, 'leave' => $broughtFwd])
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
