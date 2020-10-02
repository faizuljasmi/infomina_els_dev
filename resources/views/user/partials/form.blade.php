<form method="POST" action="{{$action}}">
    {{ csrf_field() }}
    <div class="form-row">
        <div class="form-group col-md-3">
            <label for="name">Name</label>
            <input type="name" class="form-control" id="name" name="name" value="{{$user->name}}">
        </div>
        <div class="form-group col-md-3">
            <label for="staff_id">Staff ID</label>
            <input type="text" class="form-control" id="staff_id" name="staff_id" value="{{$user->staff_id}}">
        </div>
        <div class="form-group col-md-3">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="{{$user->email}}">
        </div>
        @if (Gate::forUser(Auth::user())->allows('admin-dashboard'))
        <div class="form-group col-md-3">
            <label for="type">User Type</label>
            <select class="form-control" id="user_type" name="user_type">
                <option {{isset($user->user_type) && $user->user_type == 'Admin' ? 'selected':''}}>Admin</option>
                <option {{isset($user->user_type) && $user->user_type == 'Authority' ? 'selected':''}}>Authority
                </option>
                <option {{isset($user->user_type) && $user->user_type == 'Employee' ? 'selected':''}}>Employee</option>
            </select>
        </div>
        @endif
        <div class="form-group col-md-3">
            <label for="level">Gender</label>
            <select class="form-control" id="gender" name="gender">
                <option {{isset($user->gender) && $user->gender == 'Male' ? 'selected':''}}>Male</option>
                <option {{isset($user->gender) && $user->gender == 'Female' ? 'selected':''}}>Female</option>
            </select>
        </div>

        <div class="form-group col-md-3">
            <label for="type">Employee Type</label>
            <select class="form-control" id="emp_type_id" name="emp_type_id">
                @foreach($empTypes as $et)
                <option value="{{$et->id}}"
                    {{isset($user->emp_type_id) && $user->emp_type_id == $et->id ? 'selected':''}}>{{$et->name}}
                </option>
                @endforeach
            </select>
        </div>

        <div class="form-group col-md-3">
            <label for="type">Job Title</label>
            <input type="text" class="form-control" id="job_title" name="job_title" value="{{$user->job_title}}">
        </div>
        <div class="form-group col-md-3">
            <label for="type">Join Date</label>
            <input type="date" class="form-control" id="join_date" name="join_date" value="{{$user->join_date}}">
        </div>
        <div class="form-group col-md-3">
            <label for="type">Emergency Contact Name</label>
            <input type="text" class="form-control" id="emergency_contact_name" name="emergency_contact_name"
                value="{{$user->emergency_contact_name}}">
        </div>
        <div class="form-group col-md-3">
            <label for="type">Emergency Contact No.</label>
            <input type="text" class="form-control" id="emergency_contact_no" name="emergency_contact_no"
                value="{{$user->emergency_contact_no}}">
        </div>
        <div class="form-group col-md-6">
            <label for="type">Employee Group</label>
            <table class="table table-striped table-bordered table-sm">
                <tr>
                    <th>Group No.</th>
                    <th>Group Name</th>
                </tr>
                <tr>
                    <td>1</td>
                    <td>
                        <select class="form-control" id="emp_group_id" name="emp_group_id">
                            <option value="" {{isset($user->emp_group_id) ? 'selected':''}}>Unassigned</option>
                            @foreach($empGroups as $eg)
                            <option value="{{$eg->id}}"
                                {{isset($user->emp_group_id) && $user->emp_group_id == $eg->id ? 'selected':''}}>
                                {{$eg->name}}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>
                        <select class="form-control" id="emp_group_two_id" name="emp_group_two_id">
                            <option value="" {{isset($user->emp_group_two_id) ? 'selected':''}}>Unassigned</option>
                            @foreach($empGroups as $eg)
                            <option value="{{$eg->id}}"
                                {{isset($user->emp_group_two_id) && $user->emp_group_two_id == $eg->id ? 'selected':''}}>
                                {{$eg->name}}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>
                        <select class="form-control" id="emp_group_three_id" name="emp_group_three_id">
                            <option value="" {{isset($user->emp_group_three_id) ? 'selected':''}}>Unassigned</option>
                            @foreach($empGroups as $eg)
                            <option value="{{$eg->id}}"
                                {{isset($user->emp_group_three_id) && $user->emp_group_three_id == $eg->id ? 'selected':''}}>
                                {{$eg->name}}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>4</td>
                    <td>
                        <select class="form-control" id="emp_group_four_id" name="emp_group_four_id">
                            <option value="" {{isset($user->emp_group_four_id) ? 'selected':''}}>Unassigned</option>
                            @foreach($empGroups as $eg)
                            <option value="{{$eg->id}}"
                                {{isset($user->emp_group_four_id) && $user->emp_group_four_id == $eg->id ? 'selected':''}}>
                                {{$eg->name}}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>5</td>
                    <td>
                        <select class="form-control" id="emp_group_five_id" name="emp_group_five_id">
                            <option value="" {{isset($user->emp_group_five_id) ? 'selected':''}}>Unassigned</option>
                            @foreach($empGroups as $eg)
                            <option value="{{$eg->id}}"
                                {{isset($user->emp_group_five_id) && $user->emp_group_five_id == $eg->id ? 'selected':''}}>
                                {{$eg->name}}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <div class="float-sm-right">
        <button type="submit" class="btn btn-success">Submit</button>
    </div>
</form>
