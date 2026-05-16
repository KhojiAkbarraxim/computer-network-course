@extends('layouts.app')

@section('title', ($pageTitle ?? 'Nazorat') . ' | Kompyuter Tarmoqlarini O\'rganish')

@section('content')
    @include('pages.quiz')
@endsection
