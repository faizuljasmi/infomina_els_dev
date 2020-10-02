<form method="POST" action="{{$action}}">
    {{ csrf_field() }}
    <div class="form-row">

        <div class="form-group">
            <!-- Employee Group Name -->
            <label for="typename">Employee Group Name</label>
            <input type="text" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }} mb-2"
                value="{{ $empGroup->name }}" id="typename" placeholder="Ex: IT1" autofocus>


            @if ($errors->has('name'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('name') }}</strong>
            </div>
            @endif

                <select class="form-control" name="group_leader_id">
                    <option selected value="">Choose Group Leader</option>
                    @foreach($allUsers as $au)
                    <option value="{{$au->id}}" {{ (old('group_leader_id') == $au->id ? "selected":"") }}>
                        {{$au->name}}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback">
                    Please select a group leader
                </div>

        </div>


    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-success">Submit</button>
    </div>
</form>
