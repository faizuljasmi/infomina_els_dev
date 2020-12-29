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


<!-- Create Holiday Modal -->
<div class="modal fade" id="createHoliday" tabindex="-1" role="dialog" aria-labelledby="createHolidayTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createHolidayLongTitle">Create New Holiday</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @include('holiday.partials.form',['action' =>route('holiday_create')])
            </div>
        </div>
    </div>
</div>

<h2>Holiday</h2>

<div class="row">
    <div class="col-md-6">
        <!-- Button trigger modal -->
        <button type="button" onclick="HolidayCreate();" class="btn btn-primary" data-toggle="modal"
            data-target="#createHoliday">
            Create New Holiday
        </button>
    </div>

    <div class=" mt-2 col-md-12">
        <div class="card">
            <div class="card-header bg-teal">
                <strong>Holidays List</strong>
            </div>
            <div class="card-body">
                <table class="table table-sm table-bordered">
                    {{-- <thead>
                            <tr>
                            <th style ="width: 7%" scope="col">No.</th>
                            <th style ="width: 40%" scope="col">Name</th>
                            <th style="width: 15%"scope="col">From</th>
                            <th style="width: 15%" scope="col">To</th>
                            <th style="width: 10%">Duration</th>
                            <th style="width: 10%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach($holidays as $hol)
                            <tr>
                            <td style ="width: 7%" scope="col">{{++$count}}</td>
                    <th style="width: 40%" scope="col">{{$hol->name}}</th>
                    <td style="width: 15%" scope="col">
                        {{ \Carbon\Carbon::parse($hol->date_from)->isoFormat(' D MMM YY')}}</td>
                    <td style="width: 15%" scope="col">{{ \Carbon\Carbon::parse($hol->date_to)->isoFormat(' D MMM YY')}}
                    </td>
                    <td style="width: 10%">{{$hol->total_days}} day(s)</td>
                    <td style="width: 10%">
                        <a href="{{route('holiday_edit',$hol)}}"><button type="button" onclick="HolidayCreate();"
                                class="btn btn-primary btn-sm"><i class="fa fa-pencil-alt"></i></button></a>
                        <a href="{{route('holiday_delete',$hol)}}" class="btn btn-danger btn-sm"
                            onclick="return confirm('Are you sure you want to delete {{$hol->name}}?')"><i
                                class="fa fa-trash-alt"></i></a>
                    </td>
                    </tr>
                    @endforeach
                    </tbody> --}}
                    @foreach($countries as $country)
                    <table class="table table-sm table-bordered">
                        <h2 class="mt-3">{{$country->name}}</h2>
                        <thead>
                            <tr>
                                <th style="width: 7%" scope="col">No.</th>
                                <th style="width: 35%" scope="col">Name</th>
                                <th style="width: 10%" scope="col">Nation Wide</th>
                                <th style="width: 10%" scope="col">State Involved</th>
                                <th style="width: 10%" scope="col">From</th>
                                <th style="width: 10%" scope="col">To</th>
                                <th style="width: 10%">Duration</th>
                                <th style="width: 10%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $count = 0;?>
                            @foreach($country->holidays as $hol)
                            <tr>
                                <td style="width: 7%" scope="col">{{++$count}}</td>
                                <th style="width: 35%" scope="col">{{$hol->name}}</th>
                                <td style="width: 10%" scope="col">
                                    @if($hol->state_id == null)
                                    Yes
                                    @else
                                    No
                                    @endif
                                </td>
                                <td style="width: 10%" scope="col">
                                    @if($hol->state_id == null)
                                    -
                                    @else
                                    {{$hol->state->name}}
                                    @endif
                                </td>
                                <td style="width: 10%" scope="col">
                                    {{ \Carbon\Carbon::parse($hol->date_from)->isoFormat(' D MMM YY')}}</td>
                                <td style="width: 10%" scope="col">
                                    {{ \Carbon\Carbon::parse($hol->date_to)->isoFormat(' D MMM YY')}}
                                </td>
                                <td style="width: 10%">{{$hol->total_days}} day(s)</td>
                                <td style="width: 10%">
                                    <a href="{{route('holiday_edit',$hol)}}"><button type="button"
                                            onclick="HolidayCreate();" class="btn btn-primary btn-sm"><i
                                                class="fa fa-pencil-alt"></i></button></a>
                                    <a href="{{route('holiday_delete',$hol)}}" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Are you sure you want to delete {{$hol->name}}?')"><i
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
