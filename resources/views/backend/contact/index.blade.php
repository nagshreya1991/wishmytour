@extends('backend.layouts.master')
@section('title','Contact List || Wishmytour Admin')
@section('main-content')
    <!-- Row -->
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default card-view">
                <div class="panel-heading">
                    <div class="pull-left">
                        <h6 class="panel-title txt-dark">Contact List</h6>
                     </div>
                    
                    <div class="clearfix"></div>
                </div>
                <div class="panel-wrapper collapse in">
                    <div class="panel-body">
                        <div class="table-wrap">
                            <div class="table-responsive">
                                <table id="datable_1" class="table table-hover display  pb-30">
                               
            <thead>
            <tr>
                <th>S.N.</th>
                <th>Contact Name</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Message</th>
                <th>Date</th>
              
            </tr>
          </thead>
          <tfoot>
            <tr>
                <th>S.N.</th>
                <th>Contact Name</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Message</th>
                <th>Date</th>
              </tr>
          </tfoot>
          <tbody>
          @foreach($contactForm as $contact)   
                <tr>
                   <td>{{$contact->id}}</td>
                   <td>{{$contact->first_name}} {{$contact->last_name}}</td>
                   <td>{{$contact->email}}</td>
                   <td>{{$contact->phone_number}}</td>
                   <td ><div class="scrollable-cell">{{$contact->message}}</div></td>
                   <td style="white-space: nowrap;">{{ \Carbon\Carbon::parse($contact->created_at)->format('d-m-Y') }}</td>
                  
                </tr>  
            @endforeach
          </tbody>
        </table>
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

@push('styles')
<style>
    .scrollable-cell {
        max-height: 135px; /* Adjust the max height as needed */
        overflow-y: auto;
        display: inline-block;
    }
</style>
@endpush
@push('scripts')
<script type="text/javascript">
   $(document).ready(function() {
        // Initialize DataTable for datable_1
        $('#datable_1').DataTable();
    });
    $('.delete-warning').on('click', function (e) {
    e.preventDefault();

    console.log("Delete button clicked"); // Debug message

    // Check if the form ID exists
    var formId = $(this).closest('form').attr('id');
    if (!formId) {
        console.error("Form ID not found"); // Debug message
        return; // Exit the function
    }})
    </script>
@endpush