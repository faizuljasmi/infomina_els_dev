<form method="POST" action="{{$action}}">
                {{ csrf_field() }}
                    <div class="form-row">

                        <div class="form-group col-md-3">
                        <fieldset disabled>
                        <label for="name">Name</label>
                        <input type="name" class="form-control" id="name" value="{{$user->name}}">
                        <fieldset>
                        </div>

                        <div class="form-group col-md-3">
                        <label for="email">Email</label>
                        <fieldset disabled>
                        <input type="email" class="form-control" id="email" value="{{$user->email}}">
                        <fieldset>
                        </div>

                        <div class="form-group col-md-3">
                        <label for="gender">Gender</label>
                       <fieldset disabled>
                        <input type="email" class="form-control" id="email" placeholder="{{$user->gender}}">
                        <fieldset>
                        </div>


                        <div class="form-group col-md-3">
                        <fieldset disabled>
                        <label for="type">Employee Type</label>
                        <input type="text" class="form-control" id="type" placeholder="{{$empType->name}}">
                        <fieldset>
                        </div>

                        <div class="form-group col-md-3">
                        <fieldset disabled>
                        <label for="type">Employee Group</label>
                        <input type="text" class="form-control" id="type" placeholder="{{$empGroup->name}}">
                        <fieldset>
                        </div>

                        <div class="form-group col-md-3">
                        <fieldset disabled>
                        <label for="type">Job Title</label>
                        <input type="text" class="form-control" id="job_title" name="job_title" value="{{$user->job_title}}">
                        <fieldset>
                        </div>

                        <div class="form-group col-md-3">
                        <fieldset disabled>
                        <label for="type">Join Date</label>
                        <input type="date" class="form-control" id="join_date" name="join_date" value="{{$user->join_date}}">
                        <fieldset>
                        </div>

                        <div class="form-group col-md-3">
                        <label for="type">Emergency Contact Name</label>
                        <input type="text" class="form-control" id="emergency_contact_name" name="emergency_contact_name" value="{{$user->emergency_contact_name}}">
                        </div>
                        <div class="form-group col-md-3">
                        <label for="type">Emergency Contact No.</label>
                        <input type="text" class="form-control" id="emergency_contact_no" name="emergency_contact_no" value="{{$user->emergency_contact_no}}">
                        </div>
                    </div>
                    <div class="float-sm-right">
                <button type="submit" class="btn btn-success">Submit</button>
                </div>
</form>
