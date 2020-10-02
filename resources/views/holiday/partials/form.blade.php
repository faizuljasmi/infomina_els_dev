<div id="holiday-create">
<form method="POST" action="{{$action}}">
          @csrf
            <!-- Application Form -->
            <div class="card card-primary">
              <div class="card-body">

                <!-- Leave Type -->
                <div class="form-group">
                  <label>Holiday Name</label>
                  <div class="input-group">
                  <input type="text" class="form-control" name="holiday_name" id="holiday_name">
                  </div>
                </div>


                <!-- Date From -->
                <div class="form-group">
                  <label>Date From</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="fa fa-calendar-day"></i>
                      </span>
                    </div>
                    <input type="date" class="form-control float-right" name="date_from">
                  </div>
                </div>

                <!-- Date To -->
                <div class="form-group">
                  <label>Date To</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="fa fa-calendar-day"></i>
                      </span>
                    </div>
                    <input type="date" class="form-control float-right" name="date_to">
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

                <!-- Submit Button -->
                <button type="submit" class="btn btn-success float-right">Submit</button>
              </div>
            </div>
          </form>

<div class="col-lg-12 connectedSortable ui-sortable">
    <!-- Vanilla Calendar -->
    <div class="card">
    <div class="card-header bg-teal">
        <strong>Calendar</strong>
    </div>
    <div class="myCalendar vanilla-calendar" style="margin: 20px auto"></div>
    </div>
</div>

<script>

  //$(document).ready(HolidayCreate);
  function HolidayCreate() {

    var dates = {!! json_encode($all_dates, JSON_HEX_TAG) !!};
    //console.log(dates);

    let calendar = new VanillaCalendar({
        holiday : dates,
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
            let total = calendar.getTotalDays(from, to);
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
