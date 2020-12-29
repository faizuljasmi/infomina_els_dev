@extends('adminlte::page')

@section('content')
<h2>Edit Holiday: {{$holiday->name}}</h2>

<div id="holiday-create">

    <form method="POST" action="{{route('holiday_update',$holiday)}}">
        @csrf
        <!-- Application Form -->
        <div class="card card-primary">
            <div class="card-body">
                <div class="form-row">
                    <!-- Leave Type -->
                    <div class="form-group col-md-6">
                        <label>Holiday Name</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="holiday_name" id="holiday_name"
                                value="{{$holiday->name}}">
                        </div>
                    </div>

                    <!-- Date From -->
                    <div class="form-group col-md-6">
                        <label>Date From</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-calendar-day"></i>
                                </span>
                            </div>
                            <input type="date" class="form-control float-right" name="date_from"
                                value="{{$holiday->date_from}}">
                        </div>
                    </div>

                    <!-- Date To -->
                    <div class="form-group col-md-6">
                        <label>Date To</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-calendar-day"></i>
                                </span>
                            </div>
                            <input type="date" class="form-control float-right" name="date_to"
                                value="{{$holiday->date_to}}">
                        </div>
                    </div>

                    <!-- Total Days -->
                    <div class="form-group col-md-6">
                        <label>Total Days</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="far fa-calendar-check"></i>
                                </span>
                            </div>
                            <input type="number" class="form-control float-right" name="total_days"
                                value="{{$holiday->total_days}}">
                        </div>
                    </div>

                    <div class="form-group col-md-6">
                        <!-- Branch Zipcode -->
                        <label>Country</label>
                        <div class="input-group">
                            <select class="form-control" name="country_id" id="country_id" required>
                                <option value="">Select One</option>
                                @foreach($countries as $country)
                                <option value="{{$country->id}}" {{isset($holiday->country_id) && $holiday->country_id == $country->id ? 'selected':''}}>{{$country->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <!-- Branch Zipcode -->
                        <label>State</label>
                        <div class="input-group">
                            <select class="form-control" name="state_id" id="state_id">
                                <option value="">None</option>
                                @foreach($states as $state)
                                <option value="{{$state->id}}" {{isset($holiday->state_id) && $holiday->state_id == $state->id ? 'selected':''}}>{{$state->name}}</option>
                                @endforeach
                            </select>
                        </div>
                         <!-- Submit Button -->
                <button type="submit" class="btn btn-success float-right mt-3">Update</button>
                    </div>
                </div>
                <div class="myCalendar vanilla-calendar" style="margin: 20px auto"></div>
                <h3>Other Holidays</h3>
                <table class="table table-sm table-bordered">
                    <h2 class="mt-3">{{$holiday->country->name}}</h2>
                    <thead>
                        <tr>
                            <th style="width: 7%" scope="col">No.</th>
                            <th style="width: 40%" scope="col">Name</th>
                            <th style="width: 15%" scope="col">From</th>
                            <th style="width: 15%" scope="col">To</th>
                            <th style="width: 10%">Duration</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $count = 0;?>
                        @foreach($other_holidays as $hol)
                        <tr>
                            <td style="width: 7%" scope="col">{{++$count}}</td>
                            <th style="width: 40%" scope="col">{{$hol->name}}</th>
                            <td style="width: 15%" scope="col">
                                {{ \Carbon\Carbon::parse($hol->date_from)->isoFormat(' D MMM YYYY')}}</td>
                            <td style="width: 15%" scope="col">
                                {{ \Carbon\Carbon::parse($hol->date_to)->isoFormat(' D MMM YYYY')}}
                            </td>
                            <td style="width: 10%">{{$hol->total_days}} day(s)</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </form>
    <script>
  $(document).ready(function () {
    $(document).on('change', '#country_id', function() {
        $('#state_id').empty();
        var country_id = $(this).val();
        var div = $(this).parent();
        var op = " ";
        $.ajax({
            type: 'get',
            url: '{!!URL::to('/states/filter')!!}',
            data: {'id':country_id},
            success: function(data){
                for (var i = 0; i < data.length; i++){
                    op += '<option value="'+data[i].id+'">'+data[i].name+'</option>';
                }
                $('#state_id').append('<option value="">None</option>');
                $('#state_id').append(op);
            },
            error: function(){
                console.log('success');
            },
        });
    });
});


$(document).ready(HolidayCreate);
  function HolidayCreate() {
    var hols = {!! json_encode($all_dates, JSON_HEX_TAG) !!};
    console.log("init HolidayCreate")
    let calendar = new VanillaCalendar({
        holiday:
            hols,
        selector: ".myCalendar",
        onSelect: (data, elem) => {
            // console.log(data, elem)
        }
    });


    const validation = {

      onchange : function(v, e, fc){
          console.log("onchange", v, e, fc);
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

      },
      validateDateFromAndTo : function(name){

        let date_from = _form.get(FC.date_from);
        let date_to = _form.get(FC.date_to);



        if(
          (name == FC.date_from.name && calendar.isWeekend(date_from))
          ||
          (name == FC.date_to.name && calendar.isWeekend(date_to))
        ){
          return `Selected date is a WEEKEND. Please select another date.`;
        }
        if(
          (name == FC.date_from.name && calendar.isHoliday(date_from))
          ||
          (name == FC.date_to.name && calendar.isHoliday(date_to))
        ){
          return `Selected date is a HOLIDAY. Please select another date.`;
        }

        if(!_form.isEmpty(FC.date_from) && !_form.isEmpty(FC.date_to)){
          if(calendar.isDateSmaller(date_to, date_from)){
            if(name == FC.date_from.name){
              return "[Date From] cannot be bigger than [Date To]";
            } else if(name == FC.date_to.name){
              return "[Date To] cannot be smaller than [Date From]";
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


      },
      _totalDay : function(name){


          if(!_form.isEmpty(FC.date_from) && !_form.isEmpty(FC.date_to)){
            let from = _form.get(FC.date_from);
            let to = _form.get(FC.date_to);
            let total = calendar.getTotalWorkingDay(from, to);
            console.log("total",total)
            _form.set(FC.total_days, total);
          } else{
            _form.set(FC.total_days, "");
          }
        }
    }

    let _form = null;
    let parent_id = "holiday-create";
    let FC = {
      holiday_name : {
        name : "holiday_name",
        type : MyFormType.TEXT
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

    }

    _form = new MyForm({parent_id : parent_id, items : FC, onchange : validation.onchange});

    _form.required(FC.holiday_name);
    _form.required(FC.date_from);
    _form.required(FC.date_to);

    _form.disabled(FC.total_days);


  }

    </script>

</div>

@endsection
