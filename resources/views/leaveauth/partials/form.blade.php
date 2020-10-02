<form class="needs-validation" novalidate method="POST" action="{{$action}}">
    {{ csrf_field() }}
    <div class="form-row">
        <!-- AUTH 1 -->
        <div class="form-group col-md-6">
            <label for="name">Authority One</label>
            <select class="form-control" id="authority_1_id" name="authority_1_id" onchange= "resetAndChange()" required>
                <option value="">NA</option>
                @foreach($authUsers as $u)
                <option value="{{$u->id}}"
                    {{isset($empAuth->authority_1_id) && $empAuth->authority_one->name == $u->name ? 'selected':''}}>
                    {{$u->name}}</option>
                @endforeach
            </select>
            <div class="invalid-feedback">
                Authority 1 is required
            </div>
        </div>
        <!-- AUTH 2 -->
        <div class="form-group col-md-6">
            <label for="name">Authority Two</label>
            <select class="form-control" id="authority_2_id" name="authority_2_id" onchange= "resetAndChange()">
                <option value="">NA</option>
                @foreach($authUsers as $u)
                <option value="{{$u->id}}"
                    {{isset($empAuth->authority_2_id) && $empAuth->authority_two->name == $u->name ? 'selected':''}}>
                    {{$u->name}}</option>
                @endforeach
            </select>
            <div class="invalid-feedback">
                Authority 2 is required when there is Authority 3
            </div>
        </div>
        <!-- AUTH 3 -->
        <div class="form-group col-md-6">
            <label for="name">Authority Three</label>
            <select class="form-control" id="authority_3_id" name="authority_3_id" onchange= "resetAndChange()">
                <option value="">NA</option>
                @foreach($authUsers as $u)
                <option value="{{$u->id}}"
                    {{isset($empAuth->authority_3_id) && $empAuth->authority_three->name == $u->name ? 'selected':''}}>
                    {{$u->name}}</option>
                @endforeach
            </select>
        </div>

    </div>
    <div class="float-sm-right">
        <button type="submit" class="btn btn-success">Submit</button>
    </div>
</form>

<script>
    // Example starter JavaScript for disabling form submissions if there are invalid fields
    (function() {
      'use strict';
      window.addEventListener('load', function() {
        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        var forms = document.getElementsByClassName('needs-validation');
        // Loop over them and prevent submission
        var validation = Array.prototype.filter.call(forms, function(form) {
          form.addEventListener('submit', function(event) {
            if (form.checkValidity() === false) {
              event.preventDefault();
              event.stopPropagation();
            }
            form.classList.add('was-validated');
          }, false);
        });
      }, false);
    })();

function resetAndChange(){
    var auth_3 =$('#authority_3_id');
    //If 3rd authority is chosen, 2nd authority is required
    if(auth_3.val() !=""){
        $("#authority_2_id").attr('required', '');
    }
    //If not, then it is not required
    if(auth_3.val() ==""){
        $("#authority_2_id").removeAttr('required');
    }

    //Reset the validation
    var forms = document.getElementsByClassName('needs-validation');
    var validation = Array.prototype.filter.call(forms, function(form) {
        if (form.checkValidity() === false) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.remove('was-validated');
    });

}

</script>
