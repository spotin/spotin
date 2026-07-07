@extends('layouts.app')
@section('title', __('ui.spots.create.title'))
@section('content')
<h1 class="text-2xl font-bold mb-6">{{ __('ui.spots.create.title') }}</h1>
@include('spots._form', ['spot' => null])
@endsection
