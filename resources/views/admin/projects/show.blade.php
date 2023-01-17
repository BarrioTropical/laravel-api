@extends('layouts.admin')

@section('content')

<h1>{{$project->title}}</h1>
@if($project->category)
<small>{{$project->category->name}}</small>
@endif

    <p>{{$project->content}}</p>
    <img width="300" src="{{ asset('storage/' . $project->cover_image)}}">

    @if($project->tags && count($project->tags) > 0)
       @foreach ($project->tags as $tag)
        <span>{{$tag->name}}</span>

       @endforeach
    @endif
@endsection