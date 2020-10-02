<form method="POST" action="{{$action}}">
                {{ csrf_field() }}
                    <div class="form-row">
                        <div class="header">
                        <h6 class="text-danger">All fields must be filled out. Fill in '0' for non applicable leaves</h6>
                        </div>
                        @if($leave->count() == 0)
                            @foreach($leaveEnt as $le)
                                <div class="form-group col-md-6">
                                <label for="{{$le->leave_type->name}}">{{$le->leave_type->name}}</label>
                                <input class="form-control" type = "number" step="0.5" name="leave_{{$le->leave_type_id}}" value="{{isset($l->no_of_days) ? $l->no_of_days: '0'}}" />
                                </div>
                            @endforeach
                        @else
                            @foreach($leaveEnt as $le)
                                @foreach($leave as $l)
                                @if($le->leave_type_id == $l->leave_type_id)
                                <div class="form-group col-md-6">
                                <label for="{{$le->leave_type->name}}">{{$le->leave_type->name}}</label>
                                <input class="form-control" type = "number" step="0.5" name="leave_{{$le->leave_type_id}}" value="{{isset($l->no_of_days) ? $l->no_of_days: '0'}}" />
                                </div>
                                @endif
                                @endforeach
                            @endforeach
                        @endif
                    </div>
                <div class="float-sm-right">
                <button type="submit" class="btn btn-success">Submit</button>
                </div>
</form>