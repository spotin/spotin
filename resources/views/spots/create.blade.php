@extends('layouts.app')
@section('title', 'Nouveau spot')
@section('content')
<h1 class="text-2xl font-bold mb-6">Nouveau spot</h1>
@include('spots._form', ['spot' => null])
@endsection
