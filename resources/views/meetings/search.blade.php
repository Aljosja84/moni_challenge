@extends('layouts.app')

@section('content')
    <div id="searchContainer">
        <div id="searchContainerHeader">Search Meetings</div>
        <form action="{{ route('meetings.show') }}" method="POST">
            @csrf
            <div id="label">Email address consultant</div>
            <input type="email" id="email" name="email" required>
            <div id="button-div"><button type="submit" class="button">Search</button></div>
        </form>
    </div>
@endsection
