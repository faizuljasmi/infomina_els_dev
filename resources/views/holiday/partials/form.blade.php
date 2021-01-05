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
                <div class="form-group">
                    <!-- Branch Zipcode -->
                    <label>Country</label>
                    <div class="input-group">
                        <select class="form-control" name="country_id" id="country_id" required>
                            <option value="">Select One</option>
                            @foreach($countries as $country)
                            <option value="{{$country->id}}">{{$country->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <!-- Branch Zipcode -->
                    <label>State (select "None" for nation-wide holidays)</label>
                    <div class="input-group">
                        <select class="form-control" name="state_id" id="state_id">
                            <option value="">None</option>
                        </select>
                    </div>
                </div>
                <!-- Submit Button -->
                <button type="submit" class="btn btn-success float-right">Submit</button>
            </div>
        </div>
    </form>



    <script>
        //$(document).ready(HolidayCreate);
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

    </script>

</div>
