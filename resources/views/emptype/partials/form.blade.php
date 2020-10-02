<form method="POST" action="{{$action}}">
                    {{ csrf_field() }}
                    <div class = "form-row">

                    <!-- Employee Type Name -->
                    <label for = "typename">Employee Type Name</label>
                        <input type="text" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" value="{{ old('name') }}" id="typename"
                            placeholder="Ex: Executive" autofocus>
                    

                        @if ($errors->has('name'))
                            <div class="invalid-feedback">
                                <strong>{{ $errors->first('name') }}</strong>
                            </div>
                        @endif
                        
                    </div>
                    <div class="modal-footer">
            <button type="submit" class="btn btn-success">Submit</button>
        </div>
            </form> 