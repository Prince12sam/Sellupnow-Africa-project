@extends('integrations::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('integrations.name') !!}</p>
@endsection
