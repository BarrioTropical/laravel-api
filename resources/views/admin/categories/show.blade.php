@extends('layouts.admin')

@section('content')

<h1>{{$category->name}}</h1>
   <ul>
    @foreach($category->projects as $project)
    <li>{{$project.title}}</li>
    @endforeach
   </ul>

@endsection