@extends('adminlte::page')

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
    <h1 class="m-0 text-dark">Claim Replacement Leave</h1>
@stop

@section('content')

<section id="leaveapp-create">
  <section class="content">
    <div class="container-fluid">

      <div class="row">
        <!-- Left Col -->
        <section class="col-lg-6 connectedSortable ui-sortable">
          <form class="needs-validation" novalidate method="POST" action="{{route('leaveapp_store')}}" enctype="multipart/form-data">
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
                    <input type="date" class="form-control float-right" name="date_from" id="FromDate">
                  </div>
                </div>

                <!-- Date To -->
                <div class="form-group">
                  <label>Date To <font color="red">*</font></label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="fa fa-calendar-day"></i>
                      </span>
                    </div>
                    <input type="date" class="form-control float-right" name="date_to" id="ToDate">
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
                    <input type="number" class="form-control float-right" name="total_days">
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
                  <textarea class="form-control" rows="5" name="reason" required></textarea>
                  <h6 class="float-right" id="count_reason"></h6>
                </div>

                <!-- File Attachment -->
                <div class="form-group">
                  <label>Attachment <small class="text-muted">Optional. Format: jpg,jpeg,png,pdf. Max size: 2MB</small></label>
                  <div class="input-group">
                    <input type="file" class="form-control-file" name="attachment" id="attachment">
                    <span class="text-danger"> {{ $errors->first('attachment') }}</span>
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
                      @foreach($leaveAuth as $la)
                      <option value="{{$la->id}}">{{$la->name}}</option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <!-- Relief Personel -->
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
                      @foreach($leaveAuth as $emp)
                      <option value="{{$emp->id}}">{{$emp->name}}</option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <!-- $leaveAuth->authority_1_id -->
                <input style="display:none;" type="text" class="form-control float-right" name="emergency_contact_name" value="{{isset($user->emergency_contact_name) ? $user->emergency_contact_name:'NA'}}">
                <input style="display:none;" type="text" class="form-control float-right" name="emergency_contact_no" value="{{isset($user->emergency_contact_no) ? $user->emergency_contact_no :'NA'}}">
                <!-- CHANGE TO CYNTHIA ID -->
                <input style="display:none;" type="text" name="approver_id_3" value="4" />
                <input style="display:none;" type="text" name="relief_personnel_id" value=" " />
                <input style="display:none;" type="text" name="replacement_action" value="Claim" />

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
      <div id="loading">
        <div id="loading-image">
            <figure>
                <img src="{{url('images/loader.gif')}}" alt="Loading..." />
                <figcaption>Submitting your application...</figcaption>
            </figure>
        </div>
    </div>
  </section>
</section>

<script>
  $(document).ready(MainLeaveApplicationCreate);

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
    if(text_remaining < 0){
        $('#count_reason').html('Looks good!');
    }
    else{
  $('#count_reason').html(text_remaining + ' remaining');
    }
});

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
    var myapplications= {!! json_encode($myApplication, JSON_HEX_TAG) !!};
    var applied = {!! json_encode($applied_dates, JSON_HEX_TAG) !!};
    var approved = {!! json_encode($approved_dates, JSON_HEX_TAG) !!};

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

        // REPLACEMENT POLICY
        let prev3 = calendar.getThreePrevWorkingDay(calendar.today());
        prev3 = calendar.getDateDb(prev3);
        if(calendar.isDateSmaller(date_from, prev3) || calendar.isDateEqual(date_from, prev3)){
          return "Attention: Claim must be submitted within 3 working days after the day of event.";
        }

        for (index = 0; index < myapplications.length; index++) {
            if( myapplications[index] == calendar.getDateDb(date_from) || myapplications[index] == calendar.getDateDb(date_to)){
                return "You already have a Pending/Approved application during this date.";
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
        if(calendar.isDateBigger(date_from,calendar.today())){
            return "Attention: Replacement leave cannot be claimed in advance."
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
          _form.set(FC.date_resume, nextWorkingDay)
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
            let total = calendar.getTotalDays(from, to);
            console.log("total",total)
            _form.set(FC.total_days, total);
          } else{
            _form.set(FC.total_days, "");
          }
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
