@csrf
<style>
    .select2-container--default .select2-dropdown {
    z-index: 999999;
}

</style>
<div class="form-group row">
    <div class="col-6">
        <label for="firstname" class="col-form-label text-md-right">@trans(First Name)*</label>
        <input id="firstname" maxlength="100" value="{{isset($staff) ? $staff->firstname : ''}}" type="text" class="form-control{{ $errors->has('firstname') ? ' is-invalid' : '' }}" name="firstname" value="{{ old('firstname') }}" required>

        @if ($errors->has('firstname'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('firstname') }}</strong>
        </span>
        @endif
    </div>
    <div class="col-6">
        <label for="lastname" class="col-form-label text-md-right">@trans(Last Name)*</label>
        <input id="lastname" maxlength="100" value="{{isset($staff) ? $staff->lastname : ''}}" type="lastname" class="form-control{{ $errors->has('lastname') ? ' is-invalid' : '' }}" name="lastname" value="{{ old('lastname') }}" required>

        @if ($errors->has('lastname'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('lastname') }}</strong>
        </span>
        @endif
    </div>
</div>

<div class="form-group row">
    <div class="col-6">
        <label for="email" class="col-form-label text-md-right">@trans(Email)</label>
        <input id="email" type="email" maxlength="100" value="{{isset($staff) ? $staff->email : ''}}" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}">

        @if ($errors->has('email'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('email') }}</strong>
        </span>
        @endif
    </div>
    <div class="col-6">
        <label for="mobile" class="col-form-label text-md-right">@trans(Phone)</label>
        <input id="mobile" maxlength="20" value="{{isset($staff) ? $staff->phone : ''}}" type="text" class="form-control{{ $errors->has('mobile') ? ' is-invalid' : '' }}" name="mobile" value="{{ old('mobile') }}">

        @if ($errors->has('mobile'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('mobile') }}</strong>
        </span>
        @endif
    </div>
</div>
<div class="form-group row">
    <div class="col-6">
        <label  for="hospital" class="col-form-label text-md-right">@trans(Site)*</label>
        <select class="ssites select2 m-b-10 form-control{{ $errors->has('hospital') ? ' is-invalid' : '' }}" 
                data-placeholder="" name="hospital" id="hospital" required="">
            <option value=""></option>
            @foreach ($hospitals as $item)
            <option {{selected(isset($staff) ? $staff->hospital_id : '',$item->id)}} value="{{ $item->id }}">{{ ucwords($item->name) }}</option>   
            @endforeach
        </select>
        @if ($errors->has('hospital'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('hospital') }}</strong>
        </span>
        @endif
    </div>
    <div class="col-6">
        <label for="title" class="col-form-label text-md-right">@trans(Title)*</label>
        <select class="select2 m-b-10 form-control{{ $errors->has('title') ? ' is-invalid' : '' }}" 
                data-placeholder="" name="title" id="title" required="">
            <option value=""></option>
            @foreach ($titles as $item)
            <option {{selected(isset($staff) ? $staff->title_id : '5',$item->id)}} value="{{ $item->id }}">{{ ucwords($item->title_name) }}</option>   
            @endforeach
        </select>

        @if ($errors->has('title'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('title') }}</strong>
        </span>
        @endif
    </div>
</div>
<div class="form-group row">
    <div class="col-6">
        <label for="supervisor_name" class="col-form-label text-md-right">@trans(Supervisor) @trans(Name)</label>
        <input id="supervisor_name" maxlength="100" value="{{isset($staff) ? $staff->supervisor_name : ''}}" type="text" class="form-control{{ $errors->has('supervisor_name') ? ' is-invalid' : '' }}" name="supervisor_name" value="{{ old('supervisor_name') }}" >

        @if ($errors->has('supervisor_name'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('supervisor_name') }}</strong>
        </span>
        @endif
    </div>
    <div class="col-6">
        <label for="supervisor_email" class="col-form-label text-md-right">@trans(Supervisor) @trans(Email)</label>
        <input id="supervisor_email" maxlength="100" value="{{isset($staff) ? $staff->supervisor_email : ''}}" type="email" class="form-control{{ $errors->has('supervisor_email') ? ' is-invalid' : '' }}" name="supervisor_email" value="{{ old('supervisor_email') }}" >

        @if ($errors->has('supervisor_email'))
        <span class="invalid-feedback">
            <strong>{{ $errors->first('supervisor_email') }}</strong>
        </span>
        @endif
    </div>
</div>
<!-- Ensure jQuery is loaded before Select2 -->



<script>
    $(document).ready(function () {
    console.log('Before initialization');
     $('.modal .select2').select2();
    console.log('After initialization');
});
   
    </script>