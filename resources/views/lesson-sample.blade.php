@extends('layouts.app')

@section('title', ($pageTitle ?? 'Namuna dars') . ' | Kompyuter Tarmoqlarini O\'rganish')

@section('content')
    @include('pages.lesson')
@endsection
