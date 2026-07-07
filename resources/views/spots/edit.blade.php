@extends('layouts.app')
@section('title', 'Modifier ' . ($spot->name ?? 'le spot'))
@section('content')
<h1 class="text-2xl font-bold mb-6">Modifier le spot</h1>
@include('spots._form', ['spot' => $spot])
@endsection
