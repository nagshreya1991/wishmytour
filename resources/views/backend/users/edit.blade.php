@extends('backend.layouts.master')
@section('title','Edit Customer | Wishmytour Admin')
@section('main-content')

 <!-- Title -->
 <div class="row heading-bg">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h5 class="txt-dark">Edit Customer</h5>
        </div>
        <!-- Breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">dashboard</a></li>
                <li><a href="{{ route('admin.customers') }}"><span>Customer</span></a></li>
                <li class="active"><span>Edit Customer</span></li>
            </ol>
        </div>
        <!-- /Breadcrumb -->
    </div>
    <!-- /Title -->
    <!-- Row -->
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default card-view">
                <div class="panel-wrapper collapse in">
                    <div class="panel-body">
                        <div class="form-wrap">
    <form method="POST" action="{{ route('admin.customers.update', ['id' => $user->id]) }}">
   
    @csrf
    @method('PUT')
    <hr class="light-grey-hr"/>
        <div class="row">

        <div class="col-md-12">
        <div class="form-group">
        <label for="inputFirstName" class="col-form-label">First Name</label>
        <input id="inputFirstName" type="text" name="first_name" placeholder="Enter first name" value="{{ $user->customerDetail ? $user->customerDetail->first_name : '' }}" class="form-control">
        @error('first_name')
        <span class="text-danger">{{ $message }}</span>
        @enderror
        </div>
        </div>

       

        <div class="col-md-12">
        <div class="form-group">
        <label for="inputFirstName" class="col-form-label">Last Name</label>
        <input id="inputFirstName" type="text" name="last_name" placeholder="Enter Last name" value="{{ $user->customerDetail ? $user->customerDetail->last_name : '' }}" class="form-control">
        @error('last_name')
        <span class="text-danger">{{ $message }}</span>
        @enderror
        </div></div>

        <div class="col-md-12">
        <div class="form-group">
        <label for="status" class="col-form-label">Gender</label>
        <select name="gender" class="form-control">
        <option value="Male" {{ ($user->customerDetail && strtolower($user->customerDetail->gender) == 'male') ? 'selected' : '' }}>Male</option>
        <option value="Female" {{ ($user->customerDetail && strtolower($user->customerDetail->gender) == 'female') ? 'selected' : '' }}>Female</option>
        </select>
        @error('gender')
        <span class="text-danger">{{ $message }}</span>
        @enderror
        </div>
        </div>

        <div class="col-md-12">
        <div class="form-group">
            <label for="inputEmail" class="col-form-label">Email</label>
          <input id="inputEmail" type="email" name="email" placeholder="Enter email"  value="{{$user->email}}" class="form-control">
          @error('email')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>
        </div>

        <div class="col-md-12">
        <div class="form-group">
            <label for="inputEmail" class="col-form-label">Mobile</label>
          <input id="inputEmail" type="text" name="mobile" placeholder="Enter mobile"  value="{{$user->mobile}}" class="form-control">
          @error('mobile')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div>
        </div>

        <div class="col-md-12">
        <div class="form-group">
        <label for="inputFirstName" class="col-form-label">Address</label>
        <input id="inputFirstName" type="text" name="address" placeholder="Enter address" value="{{ $user->customerDetail ? $user->customerDetail->address : '' }}" class="form-control">
        @error('address')
        <span class="text-danger">{{ $message }}</span>
        @enderror
        </div>
        </div>

        <div class="col-md-12">
        <div class="form-group">
        <label for="inputFirstName" class="col-form-label">zipcode</label>
        <input id="inputFirstName" type="text" name="zipcode" placeholder="Enter zipcode" value="{{ $user->customerDetail ? $user->customerDetail->zipcode : '' }}" class="form-control">
        @error('zipcode')
        <span class="text-danger">{{ $message }}</span>
        @enderror
        </div></div>

        <div class="col-md-12">
        <div class="form-group">
        <label for="inputFirstName" class="col-form-label">Id type</label>
        <input id="inputFirstName" type="text" name="id_type" placeholder="Enter Id type" value="{{ $user->customerDetail ? $user->customerDetail->id_type : '' }}" class="form-control">
        @error('id_type')
        <span class="text-danger">{{ $message }}</span>
        @enderror
        </div></div>

        <div class="col-md-12">
        <div class="form-group">
        <label for="inputFirstName" class="col-form-label">Id Number</label>
        <input id="inputFirstName" type="text" name="id_number" placeholder="Enter id number" value="{{ $user->customerDetail ? $user->customerDetail->id_number : '' }}" class="form-control">
        @error('id_number')
        <span class="text-danger">{{ $message }}</span>
        @enderror
        </div></div>

       
        <div class="col-md-12">
        <div class="form-group">
        <label for="status" class="col-form-label">Id verified</label>
        <select name="id_verified" class="form-control">
        <option value="1" {{ ($user->customerDetail && $user->customerDetail->id_verified == 1) ? 'selected' : '' }}>Verified</option>
        <option value="0" {{ ($user->customerDetail && $user->customerDetail->id_verified == 0) ? 'selected' : '' }}>Not Verified</option>
        </select>
        @error('id_verified')
        <span class="text-danger">{{ $message }}</span>
        @enderror
        </div></div>



        {{-- <div class="form-group">
            <label for="inputPassword" class="col-form-label">Password</label>
          <input id="inputPassword" type="password" name="password" placeholder="Enter password"  value="{{$user->password}}" class="form-control">
          @error('password')
          <span class="text-danger">{{$message}}</span>
          @enderror
        </div> --}}

       
      
        <div class="col-md-12">
          <div class="form-group">
            <label for="status" class="col-form-label">User Verified</label>
            <select name="verified" class="form-control">
                <option value="1" {{(($user->verified==1) ? 'selected' : '')}}>Verified</option>
                <option value="0" {{(($user->verified==0) ? 'selected' : '')}}>Not Verified</option>
            </select>
          @error('verified')
          <span class="text-danger">{{$message}}</span>
          @enderror
          </div></div>

          </div>
                                <!-- Row -->
        <div class="form-group mb-3">
           <button class="btn btn-success" type="submit">Update</button>
        </div>
      </form>
    </div>
</div>
</div>
</div>
</div>
</div>

@endsection

@push('scripts')
<script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
<script>
    $('#lfm').filemanager('image');
</script>
@endpush