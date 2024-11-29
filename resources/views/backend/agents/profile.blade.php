@extends('backend.layouts.master')
@section('title','Edit Agent | Wishmytour Admin')
@section('main-content')
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
            <h5 class="txt-dark">Edit Agent</h5>
        </div>
        <!-- Breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">dashboard</a></li>
                <li><a href="{{ route('admin.agents') }}"><span>Agents</span></a></li>
                <li class="active"><span>Edit Agent</span></li>
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
                            <form action="{{ route('admin.agents.update', ['id' => $agents->id]) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <h6 class="txt-dark capitalize-font"><i class="zmdi zmdi-info-outline mr-10"></i>about
                                    Agent</h6>
                                <hr class="light-grey-hr"/>
                                <div class="row">
                                    <a href="{{ route('admin.agents.bookings', ['id' => $agents->user_id]) }}" class="btn btn-primary">Booking List</a>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">

                                                <div class="col-md-3">
                                                    <p class="form-control-static">Full Name :</p>
                                                </div>
                                                <div class="col-md-3">
                                                    <p class="form-control-static">{{ $agents->agent_fname }} {{ $agents->agent_lname }}</p>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="col-md-3">
                                                    <p class="form-control-static">Email Address :</p>
                                                </div>
                                                <div class="col-md-3">
                                                    <p class="form-control-static">{{ $agents->email }}</p>
                                                </div>
                                            </div>
                                        </div>

                                    </div> <!----end row-->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="col-md-3">
                                                    <p class="form-control-static">Mobile :</p>
                                                </div>
                                                <div class="col-md-3">
                                                    <p class="form-control-static">{{ $agents->mobile }}</p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="col-md-3">
                                                    <p class="form-control-static">Group Type :</p>
                                                </div>
                                                <div class="col-md-3">
                                                    {{ $agents->group_name }} - {{ $agents->group_percentage }}%
                                                </div>
                                            </div>
                                        </div>
                                        <!-- New Dropdown Field -->
                                    </div> <!----end row-->

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">

                                                <div class="col-md-3">
                                                    <p class="form-control-static">Agent Code  :</p>
                                                </div>
                                                <div class="col-md-3">
                                                    <p class="form-control-static">{{ $agents->agent_code  }}</p>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="col-md-3">
                                                    <p class="form-control-static">Address :</p>
                                                </div>
                                                <div class="col-md-3">
                                                    <p class="form-control-static">{{ $agents->address }}</p>
                                                </div>
                                            </div>
                                        </div>

                                    </div> <!----end row-->


                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="col-md-3">
                                                    <p class="form-control-static">Pan Number :</p>
                                                </div>
                                                <div class="col-md-3">
                                                    <p class="form-control-static">{{ $agents->pan_number }}</p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <!-- Pancard  -->
                                            <div class="form-group">

                                                <div class="col-md-3">
                                                    <p class="form-control-static">Pancard File :</p>
                                                </div>
                                                <div class="col-md-3">
                                                    @if ($agents->pan_card_file)
                                                        @php
                                                            $fileExtension = pathinfo($agents->pan_card_file, PATHINFO_EXTENSION);
                                                        @endphp
                                                        <div class="mt-2">
                                                            @if (in_array($fileExtension, ['jpeg', 'jpg', 'png']))
                                                                <img src="{{ asset('storage/app/public/' . $agents->pan_card_file) }}" alt="Pancard" class="img-fluid">
                                                            @elseif ($fileExtension == 'pdf')
                                                                <a href="{{ asset('storage/app/public/' . $agents->pan_card_file) }}" target="_blank" class="btn btn-primary">
                                                                    <i class="fas fa-file-pdf"></i> View Pancard (PDF)
                                                                </a>
                                                            @elseif (in_array($fileExtension, ['doc', 'docx']))
                                                                <a href="{{ asset('storage/app/public/' . $agents->pan_card_file) }}" target="_blank" class="btn btn-primary">
                                                                    <i class="fas fa-file-word"></i> View Pancard (DOC)
                                                                </a>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!----end row-->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <!-- Pancard  -->
                                            <div class="form-group">

                                                <div class="col-md-3">
                                                    <p class="form-control-static">Profile Image :</p>
                                                </div>
                                                <div class="col-md-3">
                                                    @if ($agents->profile_img)
                                                        @php
                                                            $fileExtension = pathinfo($agents->profile_img, PATHINFO_EXTENSION);
                                                        @endphp
                                                        <div class="mt-2">
                                                            @if (in_array($fileExtension, ['jpeg', 'jpg', 'png']))
                                                                <img src="{{ asset('storage/app/public/' . $agents->profile_img) }}" alt="Pancard" class="img-fluid">
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="light-grey-hr">
                                    <h6 class="txt-dark capitalize-font" style="padding-left: 20px;"><i class="zmdi zmdi-account mr-10"></i>Bank's Info</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="col-md-3">
                                                    <p class="form-control-static">Bank Person Name :</p>
                                                </div>
                                                <div class="col-md-3">
                                                    <p class="form-control-static">{{ $agents->bank_person_name }}</p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="col-md-3">
                                                    <p class="form-control-static">Bank Account Name :</p>
                                                </div>
                                                <div class="col-md-3">
                                                    <p class="form-control-static">{{ $agents->bank_acc_no }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div> <!----end row-->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="col-md-3">
                                                    <p class="form-control-static">IFSC Code :</p>
                                                </div>
                                                <div class="col-md-3">
                                                    <p class="form-control-static">{{ $agents->ifsc_code }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="col-md-3">
                                                    <p class="form-control-static">Bank Name :</p>
                                                </div>
                                                <div class="col-md-3">
                                                    <p class="form-control-static">{{ $agents->bank_name }}</p>
                                                </div>

                                            </div>
                                        </div>
                                        <!----end row-->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="col-md-3">
                                                        <p class="form-control-static">Branch Name :</p>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <p class="form-control-static">{{ $agents->branch_name }}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <!-- Cancelled Cheque -->
                                                <div class="form-group">

                                                    <div class="col-md-3">
                                                        <p class="form-control-static">Cancelled Cheque :</p>
                                                    </div>
                                                    <div class="col-md-3">
                                                        @if ($agents->cancelled_cheque)
                                                            @php
                                                                $fileExtension = pathinfo($agents->cancelled_cheque, PATHINFO_EXTENSION);
                                                            @endphp
                                                            <div class="mt-2">
                                                                @if (in_array($fileExtension, ['jpeg', 'jpg', 'png']))
                                                                    <img src="{{ asset('storage/app/public/' . $agents->cancelled_cheque) }}" alt="Cancelled Cheque" class="img-fluid">
                                                                @elseif ($fileExtension == 'pdf')
                                                                    <a href="{{ asset('storage/app/public/' . $agents->cancelled_cheque) }}" target="_blank" class="btn btn-primary">
                                                                        <i class="fas fa-file-pdf"></i> View Cancelled Cheque (PDF)
                                                                    </a>
                                                                @elseif (in_array($fileExtension, ['doc', 'docx']))
                                                                    <a href="{{ asset('storage/app/public/' . $agents->cancelled_cheque) }}" target="_blank" class="btn btn-primary">
                                                                        <i class="fas fa-file-word"></i> View Cancelled Cheque (DOC)
                                                                    </a>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!----end row-->
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <div class="col-md-3">
                                                    <p class="form-control-static">Bank Details Verified :</p>
                                                </div>
                                                <div class="col-md-3">
                                                    @if ($agents->bank_verified == 1)
                                                        <p class="form-control-static">
                                                            <span class="label label-success"> Verified</span>
                                                        </p>
                                                    @else

                                                        <select name="bank_verified" class="form-control">
                                                            @if($agents)
                                                                <option value="1" {{ $agents->bank_verified == 1 ? 'selected' : '' }}>Verified</option>
                                                                <option value="0" {{ $agents->bank_verified == 0 ? 'selected' : '' }}>Not Verified</option>
                                                            @else
                                                                <option value="0" selected>Not Available</option>
                                                            @endif
                                                        </select>
                                                    @endif

                                                    @error('bank_verified')
                                                    <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <hr class="light-grey-hr">
                                        <div class="col-md-12">

                                            <div class="form-group">
                                                <div class="col-md-3">
                                                    <p class="form-control-static">User Verified :</p>
                                                </div>
                                                <div class="col-md-3">
                                                    @if ($agents->is_verified == 1)
                                                        <p class="form-control-static">
                                                            <span class="label label-success"> Verified</span>
                                                        </p>
                                                    @else
                                                        <select name="is_verified" class="form-control">
                                                            <option value="1" {{(($agents->is_verified==1) ? 'selected' : '')}}>Verified</option>
                                                            <option value="0" {{(($agents->is_verified==0) ? 'selected' : '')}}>Not Verified</option>
                                                        </select>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Row -->
                                    @if($agents->bank_verified != 1 || $agents->is_verified != 1)
                                        <div class="form-group mb-0" style="padding-left: 53px;padding-top: 20px;">
                                            <button type="submit" class="btn btn-success btn-icon left-icon mr-10 pull-left"><i
                                                        class="fa fa-check"></i> <span>update</span></button>
                                            <!-- <button type="button" class="btn btn-warning pull-left">Cancel</button> -->
                                            <div class="clearfix"></div>
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