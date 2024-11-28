@extends('backend.layouts.master')
@section('title', 'Messages || Wishmytour Admin')
@section('main-content')

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-default card-view">
            <div class="panel-heading">
                <div class="pull-left">
                    <h6 class="panel-title txt-dark">Messages</h6>
                </div>
                <div class="pull-right">
                    <a href="{{ url()->previous() }}" class="btn btn-default"><i class="fa fa-arrow-left"></i> Back</a>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="panel-wrapper collapse in">
                <div class="panel-body">
                    <div class="chat-box">
                        <div class="chat-content" id="chat-content">
                            @foreach($allmsg as $msg)
                                <div class="chat-message @if($msg->sender_id == auth()->id()) chat-message-right @else chat-message-left @endif">
                                    <div class="message">
                                        <strong>
                                            @if($msg->sender_id != 34)
                                                <i class="fa fa-user"></i> {{ $msg->sender_name ?? 'Unknown' }}
                                            @else
                                                <i class="fa fa-user"></i> You
                                            @endif
                                        </strong><br> {{ $msg->message }}
                                        <br><small>{{ $msg->created_at->format('d M Y, h:i A') }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="chat-input">
                            <form action="{{ route('admin.packages.messages.store', ['id' => $package_id]) }}" method="POST">
                                @csrf
                                <div class="input-group">
                                    <input type="text" name="message" class="form-control" placeholder="Type your message..." required>
                                    <span class="input-group-btn">
                                        <button class="btn btn-primary" type="submit">Send</button>
                                    </span>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .chat-box {
        display: flex;
        flex-direction: column;
        height: 500px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }
    .chat-content {
        flex: 1;
        padding: 15px;
        overflow-y: auto;
        border-bottom: 1px solid #ccc;
    }
    .chat-input {
        padding: 15px;
    }
    .chat-message {
        margin-bottom: 15px;
    }
    .chat-message-right .message {
        padding: 10px;
        border-radius: 10px;
        text-align: right;
    }
    .chat-message-left .message {
        padding: 10px;
        border-radius: 10px;
    }
    small {
        font-size: x-small;
    }
    .fa-user {
        margin-right: 5px;
    }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var chatContent = document.getElementById('chat-content');
        chatContent.scrollTop = chatContent.scrollHeight;
    });
</script>
@endpush