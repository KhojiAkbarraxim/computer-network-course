@extends('layouts.app')

@section('title', ($pageTitle ?? 'Nazoratlar') . ' | Kompyuter Tarmoqlarini O\'rganish')

@section('content')
    @include('pages.quizzes')
@endsection
