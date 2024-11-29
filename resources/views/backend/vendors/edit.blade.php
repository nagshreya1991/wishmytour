@extends('backend.layouts.master')
@section('title','Edit Vendor | Wishmytour Admin')
@section('main-content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
  .btn-primary { float: right;
    margin-right: 50px;}
    .img-fluid {
        width: 200px;
        height: 200px;
    }
</style>
    <!-- Title -->
    <div class="row heading-bg">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h5 class="txt-dark">Edit Vendor</h5>
        </div>
        <!-- Breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">dashboard</a></li>
                <li><a href="{{ route('admin.vendors') }}"><span>Vendors</span></a></li>
                <li class="active"><span>Edit Vendor</span></li>
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
                        
                                <h6 class="txt-dark capitalize-font"><i class="zmdi zmdi-info-outline mr-10"></i>about
                                Vendor</h6>
                                <hr class="light-grey-hr"/>
          <div class="row">
          <a href="{{ route('admin.vendors.packages', ['id' => $vendor->userId]) }}" class="btn btn-primary">Package List</a>
          
          <div class="col-md-6">
          <div class="form-group">
          <div class="col-md-3">
          <p class="form-control-static">Full Name :</p>
          </div>
          <div class="col-md-3">
          <p class="form-control-static">{{ $vendor->vendor_name }} </p>
          </div>
          </div>
          </div>
           </div> <!---end row-->

           <div class="row">
          <div class="col-md-6">
          <div class="form-group">
          <div class="col-md-3">
          <p class="form-control-static">Email Address :</p>
          </div>
          <div class="col-md-3">
          <p class="form-control-static">{{ $vendor->email }} </p>
          </div>
          </div>
          </div>

         <div class="col-md-6">
          <div class="form-group">
          <div class="col-md-3">
          <p class="form-control-static">Phone Number :</p>
          </div>
          <div class="col-md-3">
          <p class="form-control-static">{{ $vendor->mobile }} </p>
          </div>
          </div>
          </div>
          </div> <!---end row-->

          <div class="row">
          <div class="col-md-6">
          <div class="form-group">
          <div class="col-md-3">
          <p class="form-control-static">Address :</p>
          </div>
          <div class="col-md-3">
          <p class="form-control-static">{{ $vendor->address }} </p>
          </div>
          </div>
          </div>

         <div class="col-md-6">
          <div class="form-group">
          <div class="col-md-3">
          <p class="form-control-static">Zipcode :</p>
          </div>
          <div class="col-md-3">
          <p class="form-control-static">{{ $vendor->zip_code }} </p>
          </div>
          </div>
          </div>
          </div> <!---end row-->

          <div class="row">
          <div class="col-md-6" style="padding-left: 34px;">
           <!-- Pan Card -->
            <div class="form-group">
            <p class="form-control-static">Pan Card</p>
            @if ($vendor->pan_card_file)
            @php
            $fileExtension = pathinfo($vendor->pan_card_file, PATHINFO_EXTENSION);
            @endphp
            <div class="mt-2">
            @if (in_array($fileExtension, ['jpeg', 'jpg', 'png']))
            <img src="{{ asset('storage/app/public/' . $vendor->pan_card_file) }}" alt="Cancelled Cheque" class="img-fluid">
            @elseif ($fileExtension == 'pdf')
            <a href="{{ asset('storage/app/public/' . $vendor->pan_card_file) }}" target="_blank" class="btn btn-primary">
            <i class="fas fa-file-pdf"></i> View Cancelled Cheque (PDF)
            </a>
            @elseif (in_array($fileExtension, ['doc', 'docx']))
            <a href="{{ asset('storage/app/public/' . $vendor->pan_card_file) }}" target="_blank" class="btn btn-primary">
            <i class="fas fa-file-word"></i> View Cancelled Cheque (DOC)
            </a>
            @endif
            </div>
            @endif
            </div>
         </div>

         <div class="col-md-6">
        <!-- Authorization Letter -->
        <div class="form-group">
        <p class="form-control-static">Authorization File</p>
         @if ($vendor->authorization_file)
            @php
                $fileExtension = pathinfo($vendor->authorization_file, PATHINFO_EXTENSION);
            @endphp
            <div class="mt-2">
                @if (in_array($fileExtension, ['jpeg', 'jpg', 'png']))
                    <img src="{{ asset('storage/app/public/' . $vendor->authorization_file) }}" alt="Authorization Letter" class="img-fluid">
                @elseif ($fileExtension == 'pdf')
                    <a href="{{ asset('storage/app/public/' . $vendor->authorization_file) }}" target="_blank" class="btn btn-primary">
                        <i class="fas fa-file-pdf"></i> View Authorization Letter (PDF)
                    </a>
                @elseif (in_array($fileExtension, ['doc', 'docx']))
                    <a href="{{ asset('storage/app/public/' . $vendor->authorization_file) }}" target="_blank" class="btn btn-primary">
                        <i class="fas fa-file-word"></i> View Authorization Letter (DOC)
                    </a>
                @endif
            </div>
         @endif
         </div>
         </div>
         </div> <!---end row-->
         <hr class="light-grey-hr">
         <h6 class="txt-dark capitalize-font"><i class="zmdi zmdi-account mr-10"></i>Bank's Info</h6>

         <div class="row">
          <div class="col-md-6">
          <div class="form-group">
          <div class="col-md-3">
          <p class="form-control-static">Bank Person Name :</p>
          </div>
          <div class="col-md-3">
          <p class="form-control-static">{{ $vendor->contact_person_name }} </p>
          </div>
          </div>
          </div>

          <div class="col-md-6">
          <div class="form-group">
          <div class="col-md-3">
          <p class="form-control-static">Bank Account Number :</p>
          </div>
          <div class="col-md-3">
          <p class="form-control-static">{{ $vendor->bank_account_number }} </p>
          </div>
          </div>
          </div>
          </div> <!---end row-->

          <div class="row">
          <div class="col-md-6">
          <div class="form-group">
          <div class="col-md-3">
          <p class="form-control-static">IFSC Code :</p>
          </div>
          <div class="col-md-3">
          <p class="form-control-static">{{ $vendor->ifsc_code }} </p>
          </div>
          </div>
          </div>

         <div class="col-md-6">
          <div class="form-group">
          <div class="col-md-3">
          <p class="form-control-static">Bank Name :</p>
          </div>
          <div class="col-md-3">
          <p class="form-control-static">{{ $vendor->bank_name }} </p>
          </div>
          </div>
          </div>
          </div> <!---end row-->

          <div class="row">
          <div class="col-md-6">
          <div class="form-group">
          <div class="col-md-3">
          <p class="form-control-static">Branch Name :</p>
          </div>
          <div class="col-md-3">
          <p class="form-control-static">{{ $vendor->branch_name }} </p>
          </div>
          </div>
          </div>
          </div> <!---end row-->
          
          <div class="row">
         <div class="col-md-6">
          <div class="form-group">
          <div class="col-md-3">
          <p class="form-control-static">Cancelled Cheque :</p>
          </div>
          <div class="col-md-3">
          @if ($vendor->cancelled_cheque)
            @php
            $fileExtension = pathinfo($vendor->cancelled_cheque, PATHINFO_EXTENSION);
            @endphp
            <div class="mt-2">
            @if (in_array($fileExtension, ['jpeg', 'jpg', 'png']))
            <img src="{{ asset('storage/app/public/' . $vendor->cancelled_cheque) }}" alt="Cancelled Cheque" class="img-fluid">
            @elseif ($fileExtension == 'pdf')
            <a href="{{ asset('storage/app/public/' . $vendor->cancelled_cheque) }}" target="_blank" class="btn btn-primary">
            <i class="fas fa-file-pdf"></i> View Cancelled Cheque (PDF)
            </a>
            @elseif (in_array($fileExtension, ['doc', 'docx']))
            <a href="{{ asset('storage/app/public/' . $vendor->cancelled_cheque) }}" target="_blank" class="btn btn-primary">
            <i class="fas fa-file-word"></i> View Cancelled Cheque (DOC)
            </a>
            @endif
            </div>
            @endif
          </div>
          </div>
          </div>
          

          </div> <!---end row-->
         
        
       

          <hr class="light-grey-hr">
          <h6 class="txt-dark capitalize-font"><i class="zmdi zmdi-account mr-10"></i>Organization Details</h6>
          <div class="row">
          <div class="col-md-6">
          <div class="form-group">
          <div class="col-md-3">
          <p class="form-control-static">Organization Name :</p>
          </div>
          <div class="col-md-3">
          <p class="form-control-static">{{ $vendor->organization_name }} </p>
          </div>
          </div>
          </div>

         <div class="col-md-6">
          <div class="form-group">
          <div class="col-md-3">
          <p class="form-control-static">Organization Pan Number :</p>
          </div>
          <div class="col-md-3">
          <p class="form-control-static">{{ $vendor->organization_pan_number }} </p>
          </div>
          </div>
          </div>
          </div> <!---end row-->

          <hr class="light-grey-hr">
          <h6 class="txt-dark capitalize-font">
                                <i class="zmdi zmdi-account mr-10"></i>
                                Organization Type: 
                                @if ($vendor->organization_type == 1)
                                    Proprietorship
                                @elseif ($vendor->organization_type == 2)
                                    Partnership
                                @elseif ($vendor->organization_type == 3)
                                    Private Limited
                                @else
                                    Unknown
                                @endif
                            </h6>
                            @foreach($vendorDetails as $index => $detail)
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <div class="col-md-3">
                    <p class="form-control-static"><strong>{{ $index + 1 }} > </strong> Name:</p>
                </div>
                <div class="col-md-3">
                    <p class="form-control-static">{{ $detail->name }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <div class="col-md-3">
                    <p class="form-control-static">Phone Number:</p>
                </div>
                <div class="col-md-3">
                    <p class="form-control-static">{{ $detail->phone_number }}</p>
                </div>
            </div>
        </div>
    </div> <!---end row-->

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <div class="col-md-3">
                    <p class="form-control-static">Pan Number:</p>
                </div>
                <div class="col-md-3">
                    <p class="form-control-static">{{ $detail->pan_number }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <div class="col-md-3">
                    <p class="form-control-static">Address:</p>
                </div>
                <div class="col-md-3">
                    <p class="form-control-static">{{ $detail->address }}</p>
                </div>
            </div>
        </div>
    </div> <!---end row-->

  
    <hr class="light-grey-hr">
@endforeach

<form action="{{ route('admin.vendors.update',  $vendor->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
          @if($vendor->have_gst == 1)
       <h6 class="txt-dark capitalize-font"><i class="zmdi zmdi-account mr-10"></i>GST Details</h6>

       <div class="row">
          <div class="col-md-6">
          <div class="form-group">
          <div class="col-md-3">
          <p class="form-control-static">GST Number :</p>
          </div>
          <div class="col-md-3">
          <p class="form-control-static">{{ $vendor->gst_number }} </p>
          </div>
          </div>
          </div>

          <div class="col-md-6">
          <div class="form-group">
          <p class="form-control-static">GST Certificate File</labepl>
                @if ($vendor->gst_certificate_file)
                @php
                $fileExtension = pathinfo($vendor->gst_certificate_file, PATHINFO_EXTENSION);
                @endphp
                <div class="mt-2">
                @if (in_array($fileExtension, ['jpeg', 'jpg', 'png']))
                <img src="{{ asset('storage/app/public/' . $vendor->gst_certificate_file) }}" alt="GST Certificate File" class="img-fluid">
                @elseif ($fileExtension == 'pdf')
                <a href="{{ asset('storage/app/public/' . $vendor->gst_certificate_file) }}" target="_blank" class="btn btn-primary">
                <i class="fas fa-file-pdf"></i> View GST Certificate (PDF)
                </a>
                @elseif (in_array($fileExtension, ['doc', 'docx']))
                <a href="{{ asset('storage/app/public/' . $vendor->gst_certificate_file) }}" target="_blank" class="btn btn-primary">
                <i class="fas fa-file-word"></i> View GST Certificate (DOC)
                </a>
                @endif
                </div>
                @endif
         </div>
        </div>
        
          </div> 
          <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="status" class="col-form-label">GST Verified</label>
                                            @if ($vendor->gst_verified == 1)
                                                <p class="form-control-static">
                                                    <span class="label label-success"> Verified</span>
                                                </p>
                                            @else
                                                <select name="gst_verified" class="form-control">
                                                    <option value="1" {{ ($vendor->gst_verified == 1) ? 'selected' : '' }}>Verified</option>
                                                    <option value="0" {{ ($vendor->gst_verified == 0) ? 'selected' : '' }}>Not Verified</option>
                                                </select>
                                            @endif
                                            @error('gst_verified')
                                            <span class="text-danger">{{$message}}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
      @endif

      <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="status" class="col-form-label">Bank Details Verified</label>
                                            @if ($vendor->bank_verified == 1)
                                                <p class="form-control-static">
                                                    <span class="label label-success"> Verified</span>
                                                </p>
                                            @else
                                                <select name="bank_verified" class="form-control">
                                                    <option value="1" {{ ($vendor->bank_verified == 1) ? 'selected' : '' }}>Verified</option>
                                                    <option value="0" {{ ($vendor->bank_verified == 0) ? 'selected' : '' }}>Not Verified</option>
                                                </select>
                                            @endif
                                            @error('bank_verified')
                                            <span class="text-danger">{{$message}}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div><!----end row -->
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="status" class="col-form-label">User Verified</label>
                                            @if ($vendor->is_verified == 1)
                                                <p class="form-control-static">
                                                    <span class="label label-success"> Verified</span>
                                                </p>
                                            @else
                                                <select name="is_verified" class="form-control">
                                                    <option value="1" {{ ($vendor->is_verified == 1) ? 'selected' : '' }}>Verified</option>
                                                    <option value="0" {{ ($vendor->is_verified == 0) ? 'selected' : '' }}>Not Verified</option>
                                                </select>
                                            @endif
                                            @error('is_verified')
                                            <span class="text-danger">{{$message}}</span>
                                            @enderror
                                        </div>
                                    </div>
                                <!-- Row -->
                                <div class="clearfix"></div>
                               @if($vendor->gst_verified != 1 || $vendor->bank_verified != 1 || $vendor->is_verified != 1)
                                <div class="form-group mb-0" style="padding-left: 20px;padding-top: 20px;">
                                    <button class="btn btn-success" type="submit">Update</button>
                                </div>
                                @endif

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Row -->
@endsection

@push('styles')
@endpush
@push('scripts')
@endpush