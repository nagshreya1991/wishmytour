@extends('package::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('package.name') !!}</p>
@endsection
