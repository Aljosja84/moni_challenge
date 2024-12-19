@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Meeting</h1>

    <form action="{{ route('meetings.update', $meeting->id) }}" method="POST">
        @csrf
        @method('POST')

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <input type="text" name="description" id="description" class="form-control" value="{{ old('description', $meeting->description) }}">
        </div>

        <div class="mb-3">
            <label for="start_date" class="form-label">Start Date</label>
            <input type="datetime-local" name="start_date" id="start_date" class="form-control" value="{{ old('start_date', $meeting->start_date->format('Y-m-d\TH:i')) }}">
        </div>

        <div class="mb-3">
            <label for="end_date" class="form-label">End Date</label>
            <input type="datetime-local" name="end_date" id="end_date" class="form-control" value="{{ old('end_date', $meeting->end_date->format('Y-m-d\TH:i')) }}">
        </div>

        <button type="submit" class="btn btn-success">Save Changes</button>
    </form>
</div>
@endsection
