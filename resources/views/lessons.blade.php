@extends('layouts.app')

@section('title', ($pageTitle ?? 'Darslar') . ' | Kompyuter Tarmoqlarini O\'rganish')

@section('content')
    @include('pages.lessons')
@endsection
