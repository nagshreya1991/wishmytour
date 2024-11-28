@extends('backend.layouts.master')
@section('title','Agents | Wishmytour Admin')
@section('main-content')
    <!-- Title -->
    <div class="row heading-bg">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h5 class="txt-dark">Agent Details</h5>
        </div>
        <!-- Breadcrumb -->
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">dashboard</a></li>
                <li><a href="{{ route('admin.agents') }}"><span>Agents</span></a></li>
                <li class="active"><span>Agent details</span></li>
            </ol>
        </div>
        <!-- /Breadcrumb -->
    </div>
    <!-- /Title -->
    <!-- Row -->
    <div class="row">

        <div class="col-md-12">
            <div class="panel panel-default card-view">
                <div class="panel-heading">
                    <div class="pull-left">
                        <h6 class="panel-title txt-dark">Agent</h6>
                    </div>
                    <div class="pull-right">
                        <a href="{{ route('admin.agents.commissions', ['id' => $agent->id]) }}"
                           class="pull-left btn btn-primary ">commissions</a>
                        <a href="{{ route('admin.agents.ledger', ['id' => $agent->id]) }}"
                           class="pull-left btn btn-primary ">ledger</a>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="panel-wrapper collapse in">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-wrap">
                                    <form class="form-horizontal">
                                        <div class="form-body">
                                            <h6 class="txt-dark capitalize-font"><i
                                                        class="zmdi zmdi-account mr-10"></i>Personal Info</h6>
                                            <hr class="light-grey-hr"/>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">First Name:</label>
                                                        <div class="col-md-9">
                                                            <p class="form-control-static">{{ $agent->agent_fname }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">Last Name:</label>
                                                        <div class="col-md-9">
                                                            <p class="form-control-static">{{ $agent->agent_lname }}</p>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div> <!----end row-->
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">Email Address:</label>
                                                        <div class="col-md-9">
                                                            <p class="form-control-static">{{ $agent->email }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">Mobile:</label>
                                                        <div class="col-md-9">
                                                            <p class="form-control-static">{{ $agent->mobile }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> <!----end row-->
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">Address:</label>
                                                        <div class="col-md-9">
                                                            <p class="form-control-static">{{ $agent->address }}</p>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div> <!----end row-->
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">Joined at:</label>
                                                        <div class="col-md-9">
                                                            <p class="form-control-static">{{ $agent->joined_at }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">Agent Code:</label>
                                                        <div class="col-md-9">
                                                            <p class="form-control-static"><span id="agentCode">{{ $agent->agent_code }}</span> <a href="#" id="copyButton" class="inline-block ml-15">
                                                                    <i class="zmdi zmdi-copy" style="font-size: 20px;color: #878787;"></i>
                                                                </a></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> <!----end row-->

                                            <div class="seprator-block"></div>
                                            <h6 class="txt-dark capitalize-font" style="padding-left: 20px;"><i
                                                        class="zmdi zmdi-account mr-10"></i>Bank's Info</h6>
                                            <hr class="light-grey-hr">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">Contact Person:</label>
                                                        <div class="col-md-9">
                                                            <p class="form-control-static">{{ $agent->bank_person_name }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> <!----end row-->

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">Bank Name:</label>
                                                        <div class="col-md-9">
                                                            <p class="form-control-static">{{ $agent->bank_name }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">Branch Name:</label>
                                                        <div class="col-md-9">
                                                            <p class="form-control-static">{{ $agent->branch_name }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!----end row-->
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">Account
                                                            Number:</label>
                                                        <div class="col-md-9">
                                                            <p class="form-control-static">{{ $agent->bank_acc_no }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">IFSC Code:</label>
                                                        <div class="col-md-9">
                                                            <p class="form-control-static">{{ $agent->ifsc_code }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!----end row-->
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">Pan Number:</label>
                                                        <div class="col-md-3">
                                                            <p class="form-control-static">{{ $agent->pan_number }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!----end row-->

                                            <div class="seprator-block"></div>
                                            <h6 class="txt-dark capitalize-font" style="padding-left: 20px;"><i
                                                        class="zmdi zmdi-collection-item mr-10"></i>Documents</h6>
                                            <hr class="light-grey-hr">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="row">
                                                        @if ($agent->profile_img)
                                                            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12  file-box">
                                                                <div class="file">
                                                                    <a href="{{ asset('storage/app/public/' . $agent->profile_img) }}" target="_blank">
                                                                        <div class="image"
                                                                             style="background-image:url({{ asset('storage/app/public/' . $agent->profile_img) }})">
                                                                        </div>
                                                                        <div class="file-name">
                                                                            Profile Picture
{{--                                                                            <br>--}}
{{--                                                                            <span>Added: Jan 11, 2016</span>--}}
                                                                        </div>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if ($agent->pan_card_file)
                                                            @php
                                                                $fileExtension = pathinfo($agent->pan_card_file, PATHINFO_EXTENSION);
                                                            @endphp
                                                            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12  file-box">
                                                                <div class="file">
                                                                    <a href="{{ asset('storage/app/public/' . $agent->pan_card_file) }}" target="_blank">
                                                                        @if (in_array($fileExtension, ['jpeg', 'jpg', 'png']))
                                                                            <div class="image"
                                                                                 style="background-image:url({{ asset('storage/app/public/' . $agent->pan_card_file) }})">
                                                                                @elseif ($fileExtension == 'pdf')
                                                                                    <div class="icon">
                                                                                        <i class="zmdi zmdi-file"></i>
                                                                                    </div>
                                                                                @elseif (in_array($fileExtension, ['doc', 'docx']))
                                                                                    <div class="icon">
                                                                                        <i class="zmdi zmdi-file-text"></i>
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                            <div class="file-name">
                                                                                PAN
{{--                                                                                <br>--}}
{{--                                                                                <span>Added: Jan 11, 2016</span>--}}
                                                                            </div>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if ($agent->pan_card_file)
                                                            @php
                                                                $fileExtension = pathinfo($agent->cancelled_cheque, PATHINFO_EXTENSION);
                                                            @endphp
                                                            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12  file-box">
                                                                <div class="file">
                                                                    <a href="{{ asset('storage/app/public/' . $agent->cancelled_cheque) }}" target="_blank">
                                                                        @if (in_array($fileExtension, ['jpeg', 'jpg', 'png']))
                                                                            <div class="image"
                                                                                 style="background-image:url({{ asset('storage/app/public/' . $agent->cancelled_cheque) }})">
                                                                                @elseif ($fileExtension == 'pdf')
                                                                                    <div class="icon">
                                                                                        <i class="zmdi zmdi-file"></i>
                                                                                    </div>
                                                                                @elseif (in_array($fileExtension, ['doc', 'docx']))
                                                                                    <div class="icon">
                                                                                        <i class="zmdi zmdi-file-text"></i>
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                            <div class="file-name">
                                                                                Cancelled Cheque
{{--                                                                                <br>--}}
{{--                                                                                <span>Added: Jan 11, 2016</span>--}}
                                                                            </div>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <!-- Row -->
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
@endsection

@push('styles')
@endpush
@push('scripts')
    <script>
        $(document).ready(function () {
            $('#copyButton').click(function (event) {
                event.preventDefault(); // Prevent the default anchor behavior

                // Get the text from the span
                var agentCode = $('#agentCode').text();

                // Create a temporary input element
                var tempInput = $('<input>');
                $('body').append(tempInput);
                tempInput.val(agentCode).select();

                // Copy the text to the clipboard
                document.execCommand("copy");

                // Remove the temporary input element
                tempInput.remove();

                // Show a toast notification
                $.toast({
                    heading: 'Copied to clipboard',
                    text: 'Agent code ' + agentCode + ' has been copied.',
                    position: 'top-right',
                    loaderBg: '#f0c541',
                    icon: 'success',
                    hideAfter: 3500,
                    stack: 6
                });
            });
        });
    </script>
@endpush