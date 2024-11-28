@extends('backend.layouts.master')
@section('title','Package Details | Wishmytour Admin')
@section('main-content')

<div class="card">
<h5 class="card-header">Package Details</h5>
<div class="card-body">

<div class="row">
<div class="col-md-12">
<div class="panel panel-default card-view">
<div class="panel-heading">

<div class="clearfix"></div>
</div>
<!-- Message Bar -->
@if($messagesCount > 0)
<div class="row">
<div class="col-md-12">
<div class="alert alert-info" role="alert">
<strong>Messages:</strong> You have {{ $messagesCount }} unread messages.
</div>
</div>
</div>
@endif

<a href="{{ route('admin.packages.messages', ['id' => $transformedPackage['package_id']]) }}"
class="btn btn-primary">Messages</a>


<div class="panel-wrapper collapse in">
<div class="panel-body">
<div class="row">
<div class="col-md-12">
<div class="form-wrap">
<div class="form-body">
<h6 class="txt-dark capitalize-font"><i
class="zmdi zmdi-account mr-10"></i>Package's Info</h6>
<hr class="light-grey-hr"/>
<div class="row">
<div class="col-md-6">
<div class="form-group">
<label class="control-label col-md-3">Package Name:</label>
<div class="col-md-9">
<p class="form-control-static">{{ $transformedPackage['package_name'] }}  </p>
</div>
</div>
</div>
<!--/span-->
<div class="col-md-6">
<div class="form-group">
<label class="control-label col-md-3">Vendor Name:</label>
<div class="col-md-9">
<p class="form-control-static"> {{ $transformedPackage['vendor_name'] }} </p>
</div>
</div>
</div>
<!--/span-->
</div>
<!-- /Row -->
<div class="row">
<div class="col-md-6">
<div class="form-group">
<label class="control-label col-md-3">Total Days:</label>
<div class="col-md-9">
<p class="form-control-static"> {{ $transformedPackage['total_days'] }} </p>
</div>
</div>
</div>
<!--/span-->
<div class="col-md-6">
<div class="form-group">
<label class="control-label col-md-3">Starting
Price:</label>
<div class="col-md-9">
<p class="form-control-static">
Rs. {{ number_format($transformedPackage['starting_price']) }}
</p>
</div>
</div>
</div>
<!--/span-->
</div>
<!-- /Row -->
<div class="row">
<div class="col-md-6">
<div class="form-group">
<label class="control-label col-md-3">Origin:</label>
<div class="col-md-9">
<p class="form-control-static"> {{ $transformedPackage['origin'] }}</p>
</div>
</div>
</div>
<!--/span-->
<div class="col-md-6">
<div class="form-group">
<label class="control-label col-md-3">Destinaton
State:</label>
<div class="col-md-9">
<p class="form-control-static"> {{ $transformedPackage['state_name'] }} </p>
</div>
</div>
</div>
<!--/span-->
</div>
<!-- /Row -->
<!-- /Row -->
<div class="row">
<div class="col-md-6">
<div class="form-group">
<label class="control-label col-md-3">Destination
City:</label>
<div class="col-md-9">
<p class="form-control-static"> {{ $transformedPackage['cities_name'] }}</p>
</div>
</div>
</div>
<!--/span-->
<div class="col-md-6">
<div class="form-group">
<label class="control-label col-md-3">Keywords:</label>
<div class="col-md-9">
<p class="form-control-static"> {{ $transformedPackage['keywords'] }} </p>
</div>
</div>
</div>
<!--/span-->
</div>
<!-- /Row -->
<!-- /Row -->
<div class="row">
<div class="col-md-6">
<div class="form-group">
<label class="control-label col-md-3">Transportation Name:</label>
<div class="col-md-9">
<p class="form-control-static"> {{ $transformedPackage['transportation_name'] }}</p>
</div>
</div>
</div>
<!--/span-->
<div class="col-md-6">
<div class="form-group">
<label class="control-label col-md-3">Religion:</label>
<div class="col-md-9">
<p class="form-control-static"> {{ $transformedPackage['religion'] }} </p>
</div>
</div>
</div>
<!--/span-->
</div>
<!-- /Row -->
<!-- Row -->
<div class="row">
<div class="col-md-6">
<div class="form-group">
<label class="control-label col-md-3">Trip Name:</label>
<div class="col-md-9">
<p class="form-control-static"> {{ $transformedPackage['trip'] }}</p>
</div>
</div>
</div>
<!--/span-->
<div class="col-md-6">
<div class="form-group">
<label class="control-label col-md-3">Type of tour packages:</label>
<div class="col-md-9">
<p class="form-control-static"> {{ $transformedPackage['type_of_tour_packages'] }} </p>
</div>
</div>
</div>
<!--/span-->
</div>
<!-- /Row -->
<!-- Row -->
<div class="row">
<div class="col-md-6">
<div class="form-group">
@if(!empty($transformedPackage['themes']))
<label class="control-label col-md-3">Themes:</label>
<div class="col-md-9">
<p class="form-control-static">
@foreach($transformedPackage['themes'] as $theme)
{{ $theme->name }}
@endforeach
</p>
</div>
@endif
</div>
</div>
<!--/span-->
@if(!empty($transformedPackage['gallery_images']))
<div class="col-md-6">
<div class="form-group">
<label class="control-label col-md-3">Gallery Images:</label>
<div class="col-md-9">
<p class="form-control-static">
<ul>
@foreach($transformedPackage['gallery_images'] as $image)
<li style="float: left; width: 100px;margin-right: 10px;">
<img src="{{ asset('storage/app/public/' . $image['path']) }}"
alt="Gallery Image"
class="img-fluid mt-2"
style="hight: 100px;width: 100px">
</li>
@endforeach
</ul>
</p>
</div>
</div>
</div>
@endif
<!--/span-->
</div>
<!-- /Row -->
<hr class="light-grey-hr">
<!-- Row -->
<div class="row">
<div class="col-md-6">
<div class="form-group">
<label class="control-label col-md-3">Overview:</label>
<div class="col-md-9">
<div class="scrollable">
<p class="form-control-static">{{ $transformedPackage['overview'] }}</p>
</div>
</div>
</div>
</div>

<div class="col-md-6">
<div class="form-group">
@if(!empty($transformedPackage['inclusions_list']))
<label class="control-label col-md-3">Inclusions:</label>
<div class="col-md-9">
<p class="form-control-static">
<ul class="uo-list">
@foreach($transformedPackage['inclusions_list'] as $inclusion)
<li class="mb-10">{{ $inclusion['name'] }}</li>
@endforeach
</ul>
</p>
</div>
@endif
</div>
</div>
<!--/span-->

</div>
<hr class="light-grey-hr">
<!-- Row -->
<div class="row">
<div class="col-md-6">
<div class="form-group">
@if(!empty($transformedPackage['exclusions_list']))
<label class="control-label col-md-3">Exclusions:</label>
<div class="col-md-9">
<p class="form-control-static">
<ul class="uo-list">
@foreach($transformedPackage['exclusions_list'] as $exclusion)
<li class="mb-10">{{ $exclusion['name'] }}</li>
@endforeach
</ul>
</p>
</div>
@endif
</div>
</div>
<!--/span-->
<!-- Terms and Condition -->
<div class="col-md-6">
<div class="form-group">
<label class="control-label col-md-3">Terms and Condition:</label>
<div class="col-md-9">
<div class="scrollable">
<p class="form-control-static">{{ $transformedPackage['terms_and_condition'] }}</p>
</div>
</div>
</div>
</div>
<!--/span-->
<!--/span-->
</div>
<hr class="light-grey-hr">

<!-- Row -->
<div class="row">
<div class="col-md-6">
<div class="form-group">
<label class="control-label col-md-3">Child discount:</label>
<div class="col-md-9">
<div class="scrollable">
<p class="form-control-static">
Rs. {{ number_format($transformedPackage['child_discount']) }}
</p>
</div>
</div>
</div>
</div>
<!--/span-->
<!-- Terms and Condition -->
<div class="col-md-6">
<div class="form-group">
<label class="control-label col-md-3">Single occupancy cost:</label>
<div class="col-md-9">
<div class="scrollable">
<p class="form-control-static">
Rs. {{ number_format($transformedPackage['single_occupancy_cost']) }}
</p>
</div>
</div>
</div>
</div>
<!--/span-->
<!--/span-->
</div>
<!-- /Row -->
<hr class="light-grey-hr">
<!-- Row -->
<!-- Row -->
<div class="row">
<div class="col-md-6">
<div class="form-group">
<label class="control-label col-md-3">Total Seat:</label>
<div class="col-md-9">
<div class="scrollable">
<p class="form-control-static">
{{ $transformedPackage['total_seat']}}
</p>
</div>
</div>
</div>
</div>
<!--/span-->
<!-- Terms and Condition -->
<div class="col-md-6">
<div class="form-group">
<label class="control-label col-md-3">Bulk no of pax:</label>
<div class="col-md-9">
<div class="scrollable">
<p class="form-control-static">
{{ $transformedPackage['bulk_no_of_pax'] }}
</p>
</div>
</div>
</div>
</div>
<!--/span-->
<!--/span-->
</div>
<!-- /Row -->
<hr class="light-grey-hr">
<!-- Row -->
<div class="row">
<div class="col-md-6">
<div class="form-group">
<label class="control-label col-md-3">Pax discount percent:</label>
<div class="col-md-9">
<div class="scrollable">
<p class="form-control-static">
{{ $transformedPackage['pax_discount_percent']}}
</p>
</div>
</div>
</div>
</div>
<!--/span-->
<!-- Terms and Condition -->
<div class="col-md-6">
<div class="form-group">
<label class="control-label col-md-3">Create Date:</label>
<div class="col-md-9">
<div class="scrollable">
<p class="form-control-static">
{{ $transformedPackage['created_at'] }}
</p>
</div>
</div>
</div>
</div>
<!--/span-->
<!--/span-->
</div>
<!-- /Row -->
<hr class="light-grey-hr">
<!-- Row -->
<div class="row">
<div class="col-md-6">
<div class="form-group">
<label class="control-label col-md-3">Tour circuit:</label>
<div class="col-md-9">
<div class="scrollable">
<p class="form-control-static">
{{ $transformedPackage['tour_circuit']}}
</p>
</div>
</div>
</div>
</div>
<!--/span-->
<!-- Terms and Condition -->
<div class="col-md-6">
<div class="form-group">
<label class="control-label col-md-3">Location:</label>
<div class="col-md-9">
<div class="scrollable">
<p class="form-control-static">
{{ $transformedPackage['location'] }}
</p>
</div>
</div>
</div>
</div>
<!--/span-->
<!--/span-->
</div>
<!-- /Row -->
<hr class="light-grey-hr">
<!-- Row -->
<div class="row">
<div class="col-md-6">
<div class="form-group">
<label class="control-label col-md-3">Status:</label>
<div class="col-md-9">
<div class="scrollable">
<p class="form-control-static">
{{ $transformedPackage['status']}}
</p>
</div>
</div>
</div>
</div>
<!--/span-->
<!-- Terms and Condition -->
<div class="col-md-6">
<div class="form-group">
<label class="control-label col-md-3">Triple sharing discount:</label>
<div class="col-md-9">
<div class="scrollable">
<p class="form-control-static">
{{ $transformedPackage['triple_sharing_discount'] }}
</p>
</div>
</div>
</div>
</div>
<!--/span-->
<!--/span-->
</div>
<!-- /Row -->
<hr class="light-grey-hr">
<div class="row">
<div class="col-md-6">
<div class="form-group">
<label class="control-label col-md-3">Addons:</label>
<div class="col-md-9">
<div class="scrollable">
<p class="form-control-static">
@foreach($transformedPackage['addons'] as $addon)
<strong>Title:</strong> {{ $addon['title'] }}
<br>
<strong>Description:</strong> {{ $addon['description'] }}
<br>
<strong>Price:</strong>
${{ $addon['price'] }}<br><br>
@endforeach
</p>
</div>
</div>
</div>
</div>
<!--/span-->
<!-- Terms and Condition -->
<div class="col-md-6">
<div class="form-group">
<label class="control-label col-md-3">Media Links:</label>
<div class="col-md-9">

<p class="form-control-static">
<ul>
{{-- Assuming $mediaLinks is an array --}}
@foreach ($transformedPackage['media_link'] as $mediaLink)
<li><a href="{{ $mediaLink['media_link'] }}"
target="_blank">{{ $mediaLink['media_link'] }}</a>
</li><br>
@endforeach
</ul>
</p>

</div>
</div>
</div>
</div>
<!--/span-->
<div class="seprator-block"></div>
<hr class="light-grey-hr">
<!-- Row -->
<div class="row">
<div class="col-md-12">
<div class="form-group">
@if(!empty($transformedPackage['itinerary']))
<label class="control-label col-md-3">Itinerary:</label>
<div class="row">
<div class="col-md-12">

<p class="form-control-static">
<ul>
@foreach($transformedPackage['itinerary'] as $day)
<li>
<label class="control-label col-md-3"> {{ $day['day'] }}
: {{ $day['place_name'] }}</label>
<div class="col-md-9">
<p>{{ $day['itinerary_title'] }}</p>
<p>{{ $day['itinerary_description'] }}</p>
<h6>Meals
: {{ $day['meal'] }}</h6>

<!-- Display flights -->
@if(!empty($day['flights']))
<h6>Flights:</h6>
<ul>
@foreach($day['flights'] as $flight)
<li>
Departure: {{ $flight['depart_destination']['name'] }}
-
Arrival: {{ $flight['arrive_destination']['name'] }}</li>
<!-- Add more flight details as needed -->
@endforeach
</ul>
@endif
<!-- Display trains -->
@if(!empty($day['trains']))
<h6>Trains:</h6>
<ul>
@foreach($day['trains'] as $train)
<li>
Departure: {{ $train['depart_destination']['name'] }}
-
Arrival: {{ $train['arrive_destination']['name'] }}</li>
<!-- Add more train details as needed -->
@endforeach
</ul>
@endif
<!-- local_transport -->

@if(!empty($day['local_transport']))
<h6>Local Transport:</h6>
<ul>
@foreach($day['local_transport'] as $transport)
<li>
<label class="control-label col-md-3">Transport:</label>
<div class="col-md-9">
<p>{{ $transport['name'] }}</p>
<!-- Add more details as needed -->
</div>
</li>
@endforeach
</ul>
@endif
<!-- sightseeing -->

@if(!empty($day['sightseeing']))
<h6>Sightseeing:</h6>
<ul>
@foreach($day['sightseeing'] as $sightseeing)
<li>
<label class="control-label col-md-3">Sightseeing Location:</label>
<div class="col-md-9">
<p>Morning: {{ $sightseeing['morning'] }}</p>
<p>Afternoon: {{ $sightseeing['afternoon'] }}</p>
<p>Evening: {{ $sightseeing['evening'] }}</p>
<p>Night: {{ $sightseeing['night'] }}</p>

<!-- Display sightseeing gallery images if available -->
@if(!empty($sightseeing['sightseeing_gallery']))
<h6>Gallery Images:</h6>
<ul>
@foreach($sightseeing['sightseeing_gallery'] as $image)
<li>
<img src="{{ asset('storage/app/public/' . $image['path']) }}"
alt="Sightseeing Image"
style="width: 100px; height: auto;">
</li>
@endforeach
</ul>
@endif

<!-- Add more sightseeing details as needed -->
</div>
</li>
@endforeach
</ul>
@endif


<!-- Display Hotels -->
@if(!empty($day['hotels']))
<h6>Hotels:</h6>
<ul>
@foreach($day['hotels'] as $hotel)
<li>
<label class="control-label col-md-3">Hotel
Name:</label>
<div class="col-md-9">
<p>{{ $hotel['hotel_name'] }}</p>
<p>
Star: {{ $hotel['star'] }}</p>
<!-- Display hotel gallery images if needed -->
@if(!empty($hotel['hotel_gallery']))
<h6>
Gallery
Images:</h6>
<ul>
@foreach($hotel['hotel_gallery'] as $image)
<li>
<img src="{{ asset('storage/app/public/' . $image['path']) }}"
alt="Hotel Image"
style="width: 100px; height: auto;">
</li>
@endforeach
</ul>
@endif
<!-- Add more hotel details as needed -->
</div>
</li>
@endforeach
</ul>
@endif


</div>
<div class="seprator-block"></div>
<hr class="light-grey-hr">
</li>

@endforeach

</ul>
</p>

</div>
</div>
@endif
</div>
</div>
</div>
<div class="seprator-block"></div>
<hr class="light-grey-hr">
@if($transformedPackage['admin_verified'] == 1)
<div class="row" id="vendor-verified-form">
<div class="col-md-4">
<div class="form-group">
<label class="control-label col-md-3">Vendor Approval:</label>
<div class="col-md-9">
<div class="form-group">


<p class="form-control-static">
@if($transformedPackage['vendor_verified'] == 1)
<span class="label label-success"> Verified</span>
@else
<span class="label label-warning">Not Verified</span>
@endif
</p>


</div>
</div>
</div>
</div>
</div>
@endif
<!-- /Row -->

<div class="seprator-block"></div>

<form class="form-horizontal" id="admin-verified-form"
action="{{ route('admin.packages.update', ['id' => $transformedPackage['package_id']]) }}"
method="POST">
@csrf
@method('PUT')
<h6 class="txt-dark capitalize-font"><i
class="zmdi zmdi-account-box mr-10"></i>Verified</h6>
<hr class="light-grey-hr"/>
<div class="row">
<div class="col-md-6">
<div class="form-group">
<label class="control-label col-md-3">Verified:</label>
<div class="col-md-9">
<div class="form-group">
<select name="admin_verified"
id="admin-verified-select"
class="form-control">
<option value="1" {{ $transformedPackage['admin_verified'] == 1 ? 'selected' : '' }}>
Verified
</option>
<option value="0" {{ $transformedPackage['admin_verified'] == 0 ? 'selected' : '' }}>
Not Verified
</option>
</select>
@error('admin_verified')
<span class="text-danger">{{ $message }}</span>
@enderror
</div>
</div>
</div>
</div>
</div>

<div class="form-actions mt-10">
<div class="row">
<div class="col-md-6">
<div class="row">
<div class="col-md-offset-3 col-md-9">
<div class="form-group mb-3">
<button class="btn btn-success"
type="submit">Update
</button>
</div>
</div>
</div>
</div>
<div class="col-md-6"></div>
</div>
</div>
</form>


</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
<!-- /Row -->
</div>
</div>

@endsection

@push('scripts')
<script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
<script>
$('#lfm').filemanager('image');
</script>
<style>
.scrollable {
max-height: 200px; /* Adjust the height as needed */
overflow-y: auto;
}
</style>
<script>
document.addEventListener("DOMContentLoaded", function () {
const adminVerifiedSelect = document.getElementById('admin-verified-select');
const adminVerifiedForm = document.getElementById('admin-verified-form');
const vendorVerifiedForm = document.getElementById('vendor-verified-form');
const updateButton = document.getElementById('update-button');

function toggleFormDisplay() {
if (adminVerifiedSelect.value == '1') {
adminVerifiedForm.style.display = 'none';
updateButton.style.display = 'none';
} else {
adminVerifiedForm.style.display = 'block';
updateButton.style.display = 'block';
vendorVerifiedForm.style.display = 'none';
}
}

// Initial check
toggleFormDisplay();

// Add event listener to the select element
adminVerifiedSelect.addEventListener('change', toggleFormDisplay);

// Add event listener to the form submit event
adminVerifiedForm.addEventListener('submit', function (event) {
if (adminVerifiedSelect.value == '1') {
adminVerifiedForm.style.display = 'none';
updateButton.style.display = 'none';
}
});
});
</script>
@endpush