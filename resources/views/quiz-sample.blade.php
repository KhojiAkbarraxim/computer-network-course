@extends('layouts.app')

@section('title', ($pageTitle ?? 'Namuna nazorat') . ' | Kompyuter Tarmoqlarini O\'rganish')

@section('content')
    @include('pages.quiz')
@endsection
