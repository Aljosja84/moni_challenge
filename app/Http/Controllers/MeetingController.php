<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Services\MeetingService;
use App\Models\Meeting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MeetingController extends Controller
{
    protected $meetingService;

    public function __construct(MeetingService $meetingService)
    {
        $this->meetingService = $meetingService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        // Here we will display the search form
        return view('meetings.search');
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function show(Request $request)
    {
        // Validate the email
        $request->validate([
            'email' => 'required|email'
        ]);

        // Find the employee by given email
        $employee = Employee::where('email', $request->email)->first();

        // Show error when no employees are found
        if(!$employee) {
            return redirect()->route('meetings.search')->withErrors(['email' => 'There are no employees with this email address']);
        }

        // Retrieve all meetings for the current week
        $meetings = $employee->meetings()->withinCurrentWeek()->get();

        // Pass meetings and employee data to view
        return view('meetings.show', compact('employee', 'meetings'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit(Meeting $meeting)
    {
        return view('meetings.edit', compact('meeting'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Meeting $meeting
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Meeting $meeting)
    {
        // Validate request data and set restrictions to the form fields
        $request->validate([
            'description' => 'required|string|max:255',
            'start_date' => 'required|date|before_or_equal:end_date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'note' => 'string|max:255'
        ]);

        // Update the record
        $meeting->update([
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        // Lead us back to the previous page
        return redirect()->route('meetings.show', ['email' => $meeting->employee->email])
            ->with('success', 'Meeting updated successfully.');
    }
}
