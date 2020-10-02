@extends('adminlte::page')
@section('content')

@section('content_header')
@if(session()->has('message'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="icon fa fa-check"></i>
            {{ session()->get('message') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
             <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    <h1 class="m-0 text-dark">Edit Application</h1>
@endsection


<section id="leaveapp-create">
  <section class="content">
    <div class="container-fluid">

    @if($leaveApplication->leave_type_id != '12')
      <div class="row">
        <!-- Left Col -->
        <section class="col-lg-6 connectedSortable ui-sortable">
          <form class="needs-validation" novalidate method="POST" action="{{route('update_application', $leaveApplication)}}" enctype="multipart/form-data" id ="createApp">
            @csrf
            <!-- Application Form -->
            <div class="card card-primary">
                <div class="card-header bg-teal">
                    <strong>Application Form</strong>
                </div>
                <div class="card-body">

                    <!-- Leave Type -->
                    <div class="form-group">
                        <label>Leave Type <font color="red">*</font></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="far fa-star"></i>
                                </span>
                            </div>
                            <select class="form-control" name="leave_type_id" id="leave_type_id"
                                onchange="clearDates()" required>
                                <option value="">Choose Leave</option>
                                @foreach($leaveType as $lt)
                                @if($lt->name == 'Replacement')
                                @else
                                <option value="{{$lt->id}}"
                                    {{ ($leaveApplication->leave_type_id == $lt->id ? "selected":"") }}>{{$lt->name}}
                                </option>
                                @endif
                                @endforeach
                            </select>
                            <div class="invalid-feedback">
                                Please choose a leave type
                            </div>
                            <div class="valid-feedback">
                                Looks good!
                            </div>
                        </div>
                    </div>

                    <!-- Leave Variation -->
                    <div class="form-group">
                        <label>Full/Half Day <font color="red">*</font></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="far fa-clock"></i>
                                </span>
                            </div>
                            <select class="form-control" name="apply_for" value="{{old('apply_for')}}">
                                <option value="full-day"
                                    {{ ($leaveApplication->apply_for == 'full-day' ? "selected":"") }}>Full Day</option>
                                <option value="half-day-am"
                                    {{ ($leaveApplication->apply_for == 'half-day-am' ? "selected":"") }}>Half Day AM
                                </option>
                                <option value="half-day-pm"
                                    {{ ($leaveApplication->apply_for == 'half-day-pm' ? "selected":"") }}>Half Day PM
                                </option>
                            </select>
                        </div>
                    </div>


                    <!-- Date From -->
                    <div class="form-group">
                        <label>Date From <font color="red">*</font></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-calendar-day"></i>
                                </span>
                            </div>
                            <input type="date" class="form-control float-right" name="date_from"
                                id="FromDate" value="{{$leaveApplication->date_from}}">
                        </div>
                    </div>

                    <!-- Date From -->
                    <div class="form-group">
                        <label>Date To <font color="red">*</font></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-calendar-day"></i>
                                </span>
                            </div>
                            <input type="date" class="form-control float-right" name="date_to" id="ToDate"
                                value="{{$leaveApplication->date_to}}">
                        </div>
                    </div>

                    <!-- Total Days -->
                    <div class="form-group">
                        <label>Total Days</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="far fa-calendar-check"></i>
                                </span>
                            </div>
                            <input type="number" class="form-control float-right" name="total_days"
                                value="{{$leaveApplication->total_days}}">
                        </div>
                    </div>

                    <!-- Date Resume -->
                    <div class="form-group">
                        <label>Date Resume</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="far fa-calendar-alt"></i>
                                </span>
                            </div>
                            <input type="date" class="form-control float-right" name="date_resume"
                                id="ResumeDate" value="{{$leaveApplication->date_resume}}">
                        </div>
                    </div>


                    <!-- Reason -->
                    <div class="form-group">
                        <label>Reason <small>(5 characters minimum)</small> <font color="red">*</font></label>
                        <textarea class="form-control" rows="5" name="reason" id="reason"
                            value="{{$leaveApplication->reason}}" minlength="5" required></textarea>
                        <h6 class="float-right" id="count_reason"></h6>
                        <div class="invalid-feedback">
                            Reason is required
                        </div>
                    </div>

                    <!-- File Attachment -->
                    <div class="form-group">
                        <label>Attachment <small class="text-muted">
                                <font color="red">Required for Sick & Marriage Leave. </font></br>Format:
                                jpg, jpeg, png, pdf. Max size: 2MB
                            </small></label>
                        <div class="input-group">
                            <input type="file" class="form-control-file" name="attachment" id="attachment"
                                value="{{isset($leaveApplication->attachment_url)? $leaveApplication->attachemnt_url:''}}">
                                <a href="{{$leaveApplication->attachment_url}}" target="_blank">View Attachment</a>
                            <div class="invalid-feedback">
                                Attachment is required for this type of leave
                            </div>
                        </div>
                    </div>

                    <!-- Relief Personel -->
                    <div class="form-group">
                        <label>Relief Personel <font color="red">*</font></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-user"></i>
                                </span>
                            </div>
                            <select class="form-control" name="relief_personnel_id" required>
                                <option selected value="">Choose Person</option>
                                @foreach($groupMates as $emp)
                                <option value="{{$emp->id}}"
                                    {{ ($leaveApplication->relief_personnel_id == $emp->id ? "selected":"") }}>
                                    {{$emp->name}}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">
                                Please select a relief personnel
                             </div>
                        </div>
                    </div>

                    <!-- Emergency Contact Name-->
                    <div class="form-group">
                        <label>Emergency Contact Name</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-user"></i>
                                </span>
                            </div>
                            <input type="text" class="form-control float-right"
                                name="emergency_contact_name" value="{{$leaveApplication->emergency_contact_name}}" required
                                {{isset($leaveApplication->emergency_contact_name) ? "readonly":''}}>
                                <div class="invalid-feedback">
                                    Please update your emergency contact at "Edit My Profile" or fill it in here
                                </div>
                        </div>
                    </div>

                    <!-- Emergency Contact No -->
                    <div class="form-group">
                        <label>Emergency Contact No</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-phone"></i>
                                </span>
                            </div>
                            <input type="text" class="form-control float-right" name="emergency_contact_no"
                                value="{{$leaveApplication->emergency_contact_no}}" required
                                {{isset($leaveApplication->emergency_contact_no) ? "readonly":''}}>
                                <div class="invalid-feedback">
                                    Please update your emergency contact at "Edit My Profile" or fill it in here
                                </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <strong>
                            <font color="red">* </font>Required
                        </strong>
                    </div>

                    <!-- $leaveAuth->authority_1_id -->
                    <input style="display:none;" type="text" name="approver_id_1"
                        value="{{isset($leaveAuth->authority_1_id) ? $leaveAuth->authority_1_id:''}}" />
                    <input style="display:none;" type="text" name="approver_id_2"
                        value="{{isset($leaveAuth->authority_2_id) ? $leaveAuth->authority_2_id:'' }}" />
                    <input style="display:none;" type="text" name="approver_id_3"
                        value="{{isset($leaveAuth->authority_3_id) ? $leaveAuth->authority_3_id: ''}}" />

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-success float-right">Submit</button>
                </div>
            </div>
        </form>
        </section>

        <!-- Right Col -->
        <section class="col-lg-5 connectedSortable ui-sortable">

          <div class="row">
            <div class="col-lg-12 connectedSortable ui-sortable">
              <!-- Vanilla Calendar -->
              <div class="card">
                <div class="card-header bg-teal">
                  <strong>Calendar </strong><i class="fas fa-info-circle" data-toggle="tooltip" data-placement="top" title="For your reference, other than the holidays, this calendar shows your groupmates' applied & approved leaves."></i>
                </div>
                <div class="myCalendar vanilla-calendar" style="margin: 20px auto"></div>
              </div>
            </div>
            <div class="col-lg-12 connectedSortable ui-sortable">
                            <!-- Approval Authorities -->
                            <div class="card">
                <div class="card-header bg-teal">
                  <strong>Approval Authorities</strong>
                </div>
                <div id="collapse-leave" class="collapse show" aria-labelledby="heading-leave">
                  <div class="card-body">
                    <table class="table table-bordered">
                      <tr>
                        <th>Level</th>
                        <th>Name</th>
                      </tr>
                      <tr>
                        <td>1</td>
                        <td>{{isset($leaveAuth->authority_1_id) ? $leaveAuth->authority_one->name:'NA'}}</td>
                      </tr>
                      <tr>
                        <td>2</td>
                        <td>{{isset($leaveAuth->authority_2_id) ? $leaveAuth->authority_two->name:'NA'}}</td>
                      </tr>
                      <tr>
                        <td>3</td>
                        <td>{{isset($leaveAuth->authority_3_id) ? $leaveAuth->authority_three->name:'NA'}}</td>
                      </tr>
                    </table>
                  </div>
                </div>
            </div>

            <div class="col-lg-6 connectedSortable ui-sortable">
              <!-- Leaves Balance -->
              <div class="card">
                <div class="card-header bg-teal">
                  <strong>Leave Balances</strong>
                </div>
                <div id="collapse-leave" class="collapse show" aria-labelledby="heading-leave">
                  <div class="card-body">
                    <table class="table table-bordered">
                      @foreach($leaveBal as $lb)
                      <tr>
                        <th>{{$lb->leave_type->name}}</th>
                        <td>{{$lb->no_of_days}}</td>
                      </tr>
                      @endforeach
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>

      </div>
      @else
      <div class="row">
        <!-- Left Col -->
        <section class="col-lg-6 connectedSortable ui-sortable">
          <form class="needs-validation" novalidate method="POST" action="{{route('update_application',$leaveApplication)}}" enctype="multipart/form-data">
                @csrf
                  <!-- Application Form -->
                  <div class="card card-primary">
                    <div class="card-header bg-teal">
                      <strong>Application Form</strong>
                    </div>
                    <div class="card-body">

                      <!-- Leave Type -->
                      <div class="form-group">
                        <label>Leave Type</label>
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span class="input-group-text">
                              <i class="far fa-star"></i>
                            </span>
                          </div>
                          <select class="form-control" disabled>
                            <option value="12" selected>Replacement</option>
                          </select>
                          <input style="display:none;" type="text" class="form-control float-right" name="leave_type_id" value="12">
                        </div>
                      </div>

                      <!-- Leave Variation -->
                      <div class="form-group">
                        <label>Full/Half Day <font color="red">*</font></label>
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span class="input-group-text">
                              <i class="far fa-clock"></i>
                            </span>
                          </div>
                          <select class="form-control" name="apply_for">
                              <option value="full-day"  {{ (old('apply_for') == 'full-day' ? "selected":"") }}>Full Day</option>
                              <option value="half-day-am" {{ (old('apply_for') == 'half-day-am' ? "selected":"") }}>Half Day AM</option>
                              <option value="half-day-pm" {{ (old('apply_for') == 'half-day-pm' ? "selected":"") }}>Half Day PM</option>
                          </select>
                        </div>
                      </div>


                      <!-- Date From -->
                      <div class="form-group">
                        <label>Date From <font color="red">*</font></label>
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span class="input-group-text">
                              <i class="fa fa-calendar-day"></i>
                            </span>
                          </div>
                        <input type="date" class="form-control float-right" name="date_from" id="date_from" value="{{$leaveApplication->date_from}}">
                        </div>
                      </div>

                      <!-- Date From -->
                      <div class="form-group">
                        <label>Date To <font color="red">*</font></label>
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span class="input-group-text">
                              <i class="fa fa-calendar-day"></i>
                            </span>
                          </div>
                          <input type="date" class="form-control float-right" name="date_to" id="date_to" value="{{$leaveApplication->date_to}}">
                        </div>
                      </div>

                      <!-- Total Days -->
                      <div class="form-group">
                        <label>Total Days</label>
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span class="input-group-text">
                              <i class="far fa-calendar-check"></i>
                            </span>
                          </div>
                        <input type="number" class="form-control float-right" name="total_days" value="{{$leaveApplication->total_days}}">
                        </div>
                      </div>

                      <!-- Date Resume -->
                      <div class="form-group" style="display:none">
                        <label>Date Resume</label>
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span class="input-group-text">
                              <i class="far fa-calendar-alt"></i>
                            </span>
                          </div>
                          <input type="date" class="form-control float-right" name="date_resume">
                        </div>
                      </div>


                      <!-- Reason -->
                      <div class="form-group">
                        <label>Replacement Reason <font color="red">*</font></label>
                      <textarea class="form-control" rows="5" name="reason" value="{{$leaveApplication->reason}}" required></textarea>
                        <h6 class="float-right" id="count_reason"></h6>
                      </div>

                      <!-- File Attachment -->
                      <div class="form-group">
                        <label>Attachment <small class="text-muted">Optional. Format: jpg,jpeg,png,pdf. Max size: 2MB</small></label>
                        <div class="input-group">
                          <input type="file" class="form-control-file" name="attachment" id="attachment" value="{{$leaveApplication->attachment_url}}">
                          <span class="text-danger"> {{ $errors->first('attachment') }}</span>
                          <a href="{{$leaveApplication->attachment_url}}" target="_blank">View Attachment</a>
                        </div>
                      </div>

                        <!-- Approval Authority -->
                        <div class="form-group">
                        <label>Approval Authority 1 <font color="red">*</font></label>
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span class="input-group-text">
                              <i class="fa fa-user"></i>
                            </span>
                          </div>
                          <select class="form-control" name="approver_id_1" required>
                            <option selected value="">Choose Person</option>
                            @foreach($userAuth as $ua)
                            <option value="{{$ua->id}}"  {{ ($leaveApplication->approver_id_1 == $ua->id ? "selected":"") }}>{{$ua->name}}</option>
                            @endforeach
                          </select>
                        </div>
                      </div>

                      <!-- Approval Authority 2 -->
                      <div class="form-group">
                        <label>Approval Authority 2</label>
                        <div class="input-group">
                          <div class="input-group-prepend">
                            <span class="input-group-text">
                              <i class="fa fa-user"></i>
                            </span>
                          </div>
                          <select class="form-control" name="approver_id_2">
                            <option value=""selected>Choose Person (Optional)</option>
                            @foreach($userAuth as $ua)
                            <option value="{{$ua->id}}" {{ ($leaveApplication->approver_id_2 == $ua->id ? "selected":"") }}>{{$ua->name}}</option>
                            @endforeach
                          </select>
                        </div>
                      </div>



                      <!-- $leaveAuth->authority_1_id -->
                      <input style="display:none;" type="text" class="form-control float-right" name="emergency_contact_name" value="{{$user->emergency_contact_name}}">
                      <input style="display:none;" type="text" class="form-control float-right" name="emergency_contact_no" value="{{$user->emergency_contact_no}}">
                      <!-- CHANGE TO CYNTHIA ID -->
                      <input style="display:none;" type="text" name="approver_id_3" value="4" />
                      <input style="display:none;" type="text" name="relief_personnel_id" value=" " />

                      <!-- Submit Button -->
                      <button type="submit" class="btn btn-success float-right">Submit</button>
                    </div>
                  </div>
                </form>
        </section>

        <!-- Right Col -->
        <section class="col-lg-5 connectedSortable ui-sortable">

          <div class="row">
            <div class="col-lg-12 connectedSortable ui-sortable">
              <!-- Vanilla Calendar -->
              <div class="card">
                <div class="card-header bg-teal">
                  <strong>Calendar</strong>
                </div>
                <div class="myCalendar vanilla-calendar" style="margin: 20px auto"></div>
              </div>
            </div>
          </div>
      </div>
      @endif
      <div id="loading">
            <div id="loading-image">
                <figure>
                    <img src="{{url('images/loader.gif')}}" alt="Loading..." />
                    <figcaption>Updating application...</figcaption>
                </figure>
            </div>
</div>
  </section>

  <!-- Empty Col -->
  <section class="col-lg-1 connectedSortable ui-sortable">
  </section>

  </div>

  </div>
</section>

<script>
    $(document).ready(MainLeaveApplicationCreate);

$("#leave_type_id").change(function() {
        $("#FromDate").val("");
        $("#ToDate").val("");
    });

$("#FromDate").change(function() {
    var from = $("#FromDate").val();
        $("#ToDate").val("");
        $("#ToDate").attr({
              "min" : from          // values (or variables) here
        });
    });

var text_max = 5;
$('#count_reason').html(text_max + ' remaining');

$('#reason').keyup(function() {
  var text_length = $('#reason').val().length;
  var text_remaining = text_max - text_length;
  $('#count_reason').css("color", "red");
    if(text_remaining < 0){
        $('#count_reason').html('Looks good!');
        $('#count_reason').css("color", "green");
    }
    else{
  $('#count_reason').html(text_remaining + ' characters remaining');
    }
});

// Example starter JavaScript for disabling form submissions if there are invalid fields
(function() {
'use strict';
window.addEventListener('load', function() {
// Fetch all the forms we want to apply custom Bootstrap validation styles to
var forms = document.getElementsByClassName('needs-validation');
var spinner = $('#loading');
// Loop over them and prevent submission
var validation = Array.prototype.filter.call(forms, function(form) {
  form.addEventListener('submit', function(event) {
    if (form.checkValidity() === false) {
      event.preventDefault();
      event.stopPropagation();
    }
    else{
            spinner.show();
        }
    form.classList.add('was-validated');
  }, false);
});
}, false);
})();

function MainLeaveApplicationCreate() {

var dates = {!! json_encode($all_dates, JSON_HEX_TAG) !!};
var applied = {!! json_encode($applied_dates, JSON_HEX_TAG) !!};
var approved = {!! json_encode($approved_dates, JSON_HEX_TAG) !!};
var balances= {!! json_encode($leaveBal, JSON_HEX_TAG) !!};
var myapplications= {!! json_encode($myApplication, JSON_HEX_TAG) !!};
var userGroup = {!! json_encode($user->emp_group->name, JSON_HEX_TAG) !!};

//console.log("MY APP:",myapplications);

let calendar = new VanillaCalendar({
    holiday: dates,
    applied: applied,
    approved: approved,
    selector: ".myCalendar",
    onSelect: (data, elem) => {
        // console.log(data, elem)
    }
});


const validation = {
  isAnnualLeave : function(){
    return _form.get(FC.leave_type_id) == "1";
  },
  isCalamityLeave : function(){
    return _form.get(FC.leave_type_id) == "2";
  },
   isSickLeave : function(){
    return _form.get(FC.leave_type_id) == "3";
  },
  isHospitalizationLeave : function(){
    return _form.get(FC.leave_type_id) == "4";
  },
  isCompassionateLeave : function(){
    return _form.get(FC.leave_type_id) == "5";
  },
  isEmergencyLeave : function(){
    return _form.get(FC.leave_type_id) == "6";
  },
  isMarriageLeave : function(){
    return _form.get(FC.leave_type_id) == "7";
  },
  isMaternityLeave : function(){
    return _form.get(FC.leave_type_id) == "8";
  },
  isPaternityLeave : function(){
    return _form.get(FC.leave_type_id) == "9";
  },
  isTrainingLeave : function(){
    return _form.get(FC.leave_type_id) == "10";
  },
  isUnpaidLeave : function(){
    return _form.get(FC.leave_type_id) == "11";
  },
  isReplacementLeave : function(){
    return _form.get(FC.leave_type_id) == "12";
  },

  onchange : function(v, e, fc){
      //console.log("onchange", v, e, fc);
      let name = fc.name;

      if(name == FC.date_from.name || name == FC.date_to.name){
        let error = validation.validateDateFromAndTo(name);
        if(error != null){
          alert(error);
          _form.set(fc, "");
          return;
        }
      }

      validation._dateFrom(name);
      validation._dateTo(name);

      validation._totalDay(name);
      validation._dateResume(name);

      validation._attachment(name);

  },
  isHalfDayAm : function(){
    return _form.get(FC.apply_for) == "half-day-am";
  },
  isHalfDayPm : function(){
    return _form.get(FC.apply_for) == "half-day-pm";
  },
  isFullDay : function(){
    return _form.get(FC.apply_for) == "full-day";
  },
  validateDateFromAndTo : function(name){

    let date_from = _form.get(FC.date_from);
    let date_to = _form.get(FC.date_to);


    for (index = 0; index < myapplications.length; index++) {
        if( myapplications[index] == calendar.getDateDb(date_from)){
            return "User already have a Pending/Approved application during this date.";
        }
    }

    //ANNUAL POLICY
    if(validation.isAnnualLeave()){
      let next2 = calendar.getNextWorkingDay(calendar.today());
      next2 = calendar.getNextWorkingDay(next2);
      next2 = calendar.getDateDb(next2);
      if(calendar.isDateSmaller(date_from, calendar.today())){
        return "Attention: Annual leave cannot be applied on passed dates.";
      }
      if(calendar.isDateSmaller(date_from, next2)){
        return "Attention: Annual leave must be applied at least 2 days prior to the leave date.";
      }
    }

    if(validation.isTrainingLeave()){
        let nextMonth = calendar.nextMonth(calendar.today());
        let prevWeek = calendar.getPrevWeekWorkingDay(calendar.today());
        if(calendar.isDateSmaller(date_from,prevWeek)){
            return "Attention: Training Leave must be applied within 7 days after the training day."
        }
        if(calendar.isDateBigger(date_from,nextMonth)){
            return "Attention: Training Leave cannot be applied more than a month in advance."
        }
    }

    //SICK, EMERGENCY, PATERNITY, COMPASSIONATE, CALAMITY POLICY
    if(validation.isSickLeave() || validation.isEmergencyLeave() || validation.isPaternityLeave() || validation.isCompassionateLeave() || validation.isCalamityLeave()){
      let prev3 = calendar.getPrevWeekWorkingDay(calendar.today());
      //prev3 = calendar.getThreePrevWorkingDay(prev3);
      prev3 = calendar.getDateDb(prev3);
      if(calendar.isDateSmaller(date_from, prev3) || calendar.isDateEqual(date_from, prev3)){
        return "Attention: Leave must be applied within 7 working days after the day of leave.";
      }
      if(calendar.isDateBigger(date_from, calendar.today())){
        return "Attention: Leave cannot be applied in advance.";
      }
    }

    // MATERNITY POLICY
    if(validation.isMaternityLeave()){
      let monthFwd = calendar.nextMonth(calendar.today());
      monthFwd = calendar.getDateDb(monthFwd);


      if(calendar.isDateSmaller(date_from,monthFwd) || calendar.isDateEqual(date_from,monthFwd)){
        return "Attention: Maternity leave application shall be made not less than one (1) month prior to the date on which it is desired that maternity leave commences."
      }
      if(calendar.isDateSmaller(date_from, calendar.today())){
        return "Attention: Maternity leave cannot be applied in advance.";
      }
    }

    //HOSPITALIZATION POLICY
    if(validation.isHospitalizationLeave()){
      let prev7 = calendar.getPrevWeekWorkingDay(calendar.today());
      prev7 = calendar.getDateDb(prev7);

      if(calendar.isDateSmaller(date_from, prev7) || calendar.isDateEqual(date_from, prev7)){
        return "Attention: Hospitalization leave must be applied within 7 days after the day of leave.";
      }
      if(calendar.isDateBigger(date_from, calendar.today())){
        return "Attention: Hospitalization leave cannot be applied in advance.";
      }
    }

    //UNPAID POLICY
    if(validation.isUnpaidLeave()){
      let prev3 = calendar.getThreePrevWorkingDay(calendar.today());
      prev3 = calendar.getDateDb(prev3);

      let next2 = calendar.getNextWorkingDay(calendar.today());
      next2 = calendar.getNextWorkingDay(next2);
      next2 = calendar.getDateDb(next2);

      if(calendar.isDateSmaller(date_from,calendar.today())){
      if(calendar.isDateSmaller(date_from, prev3)){
        return "Attention: Unpaid leave must be applied within 3 days after the day of leave.";
      }
    }
    if(calendar.isDateBigger(date_from,calendar.today())){
      if(calendar.isDateSmaller(date_from,next2)){
        return "Attention: Unpaid leave must be applied within 2 days before the day of leave.";
      }
    }
    }


    if(!(userGroup == 'Support Engineer' || userGroup == 'ICSC' || userGroup == 'Helpdesk')){
            if(	
            (name == FC.date_from.name && calendar.isWeekend(date_from) && (!validation.isTrainingLeave()) && (!validation.isMaternityLeave()))	
            ||	
            (name == FC.date_to.name && calendar.isWeekend(date_to) && (!validation.isTrainingLeave()) && (!validation.isMaternityLeave()))
          ){	
            return "Selected date is a Weekend day. Please select another date.";
          }	
        }	


        if(!(userGroup == 'Support Engineer' || userGroup == 'ICSC' || userGroup == 'Helpdesk')){
        if(
            (name == FC.date_from.name && calendar.isHoliday(date_from) && (!validation.isMaternityLeave()))
          ||
          (name == FC.date_to.name && calendar.isHoliday(date_to) && (!validation.isMaternityLeave()))
        ){
          return `Selected date is an announced Public Holiday. Please select another date.`;
        }
    }

    if(!_form.isEmpty(FC.date_from) && !_form.isEmpty(FC.date_to)){
      if(calendar.isDateSmaller(date_to, date_from)){
        if(name == FC.date_from.name){
          return "Starting date cannot be bigger than end date";
        } else if(name == FC.date_to.name){
          return "End date cannot be smaller than starting date";
        }
      }
    }
    return null;
  },
  // #########################################
  // specific to field
  _dateFrom : function(name){
  },
  _dateTo : function(name){
    if(validation.isHalfDayAm() || validation.isHalfDayPm()){
      _form.disabled(FC.date_to);
      _form.copy(FC.date_from, FC.date_to);
    } else if(validation.isFullDay()){
      _form.required(FC.date_to);
      if(name == FC.apply_for.name){
        _form.set(FC.date_to, "");
      }
    }
  },
  _dateResume : function(name){
    if(!_form.isEmpty(FC.date_to)){
      let dateTo = _form.get(FC.date_to);
      let nextWorkingDay = calendar.getNextWorkingDay(dateTo);
      nextWorkingDay = calendar.getDateInput(nextWorkingDay);
      if(validation.isHalfDayAm()){
      _form.set(FC.date_resume, calendar.getDateInput(dateTo));
    }
    else{
      _form.set(FC.date_resume, nextWorkingDay);
    }
    }
  },
  _totalDay : function(name){
    if(validation.isHalfDayAm() || validation.isHalfDayPm()){
      _form.set(FC.total_days, 0.5);
    }else if(validation.isFullDay()){
      if(name == FC.apply_for.name){
        _form.set(FC.total_days, "");
      }
      if(!_form.isEmpty(FC.date_from) && !_form.isEmpty(FC.date_to)){
        let from = _form.get(FC.date_from);
        let to = _form.get(FC.date_to);
        let total = calendar.getTotalWorkingDay(from, to);
        if(validation.isUnpaidLeave() || validation.isHospitalizationLeave() || validation.isMaternityLeave() || userGroup == 'Support Engineer' || userGroup == 'ICSC' || userGroup == 'Helpdesk'){	
          total = calendar.getTotalDays(from, to);	
        }
        var leaveId = _form.get(FC.leave_type_id);
        var i = leaveId - 1;
        if(total > balances[i]['no_of_days'] && _form.get(FC.leave_type_id) != "12"){
            alert('You have insufficient leave balance');
            _form.set(FC.date_to, "");
            _form.set(FC.total_days, "");
        }
        if(total > 60){
            _form.set(FC.date_to, "");
            _form.set(FC.total_days, "");
        }
        else if(total > 0 && total <= 60){
            _form.set(FC.total_days, total);
        }
      } else{
        _form.set(FC.total_days, "");
      }
    }
  },
  _attachment : function(name){
      if(validation.isMarriageLeave() || validation.isSickLeave() || validation.isHospitalizationLeave() || validation.isPaternityLeave()|| validation.isMaternityLeave()){
        _form.required(FC.reason);
      }
  }
}

let _form = null;
let parent_id = "leaveapp-create";
let FC = {
  leave_type_id : {
    name : "leave_type_id",
    type : MyFormType.SELECT
  },
  apply_for : {
    name : "apply_for",
    type : MyFormType.SELECT
  },
  date_from : {
    name : "date_from",
    type : MyFormType.DATE
  },
  date_to : {
    name : "date_to",
    type : MyFormType.DATE
  },
  total_days : {
    name : "total_days",
    type : MyFormType.NUMBER
  },
  date_resume : {
    name : "date_resume",
    type : MyFormType.DATE
  },
  reason : {
    name : "reason",
    type : MyFormType.TEXTAREA
  },
  attachment : {
    name : "attachment",
    type : MyFormType.FILE
  },
  relief_personnel_id : {
    name : "relief_personnel_id",
    type : MyFormType.SELECT
  },
  emergency_contact_no  : {
    name : "emergency_contact_no",
    type : MyFormType.TEXT
  },
  emergency_contact_name  : {
    name : "emergency_contact_name",
    type : MyFormType.TEXT
  },
  // ####################################
  // ## data generated from controller ##
  // user_id
  // status
  // approver_id_1
  // approver_id_2
  // approver_id_3
}

_form = new MyForm({parent_id : parent_id, items : FC, onchange : validation.onchange});

_form.required(FC.leave_type_id);
_form.required(FC.date_from);
_form.required(FC.date_to);

_form.disabled(FC.date_resume);
_form.disabled(FC.total_days);


}
</script>
<style type="text/css">
    #loading {
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        position: fixed;
        opacity: 0.7;
        background-color: #fff;
        z-index: 99;
        text-align: center;
        display: none;
    }

    #loading-image {
        position: fixed;
        top: 50%;
        left: 50%;
        /* bring your own prefixes */
        transform: translate(-50%, -50%);
        z-index: 100;
    }
</style>
</section>


@endsection
