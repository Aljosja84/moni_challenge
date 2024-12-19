@extends('layouts.app')
@section('content')
    <div id="meetings_container">
        <div id="meetingsContainerHeader">
            <h1>Meetings for {{ $employee->first_name }} {{ $employee->last_name }} ({{ $employee->email }}) </h1>

        </div>
        @if ($meetings->isEmpty())
            <div class="meeting_container">No meetings found for this employee.</div>
        @else
            @foreach ($meetings as $meeting)
                <div class="meeting_container">
                    <div>{{ $meeting->description }}</div>
                    <div> {{ $meeting->start_date }} - {{ $meeting->end_date }} </div>
                    <a href="{{ route('meetings.edit', $meeting->id) }}" class="btn btn-primary">Edit</a>
                </div>
            @endforeach
        @endif

        <div><a href="{{ route('meetings.search') }}">Search Again</a></div>
    </div>
@endsection

