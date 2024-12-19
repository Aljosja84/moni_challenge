<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Meeting;
use App\Models\Employee;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ExportMeetings extends Command
{
    protected $signature = 'meetings:export {email? : The email address of the consultant}';
    protected $description = 'Export meetings to a file based on consultant email or all meetings.';

    /**
     * This command will retrieve calendar items from an individual consultant if the parameter email is supplied
     * and will export the items ('meetings') to an iCal format so they can be imported to an external
     * calendar client such as Google Agenda, Outlook etc.
     *
     * If no email is supplied it will simply retrieve all meetings from DB and export them for each individual
     * consultant.
     *
     */
    public function handle()
    {
        // The only parameter (optional) for this command.
        $email = $this->argument('email');

        // Check if an email is supplied and base our calls on that
        // If an email is given we will export meetings for that individual consultant.
        if ($email) {
            // Check if there is an consultant with that email address in DB
            // If not, throw an error
            $employee = Employee::where('email', $email)->first();

            if(!$employee) {
                $this->error("No consultants with that email address found.");
                return Command::FAILURE;
            }

            // Consultant found, begin exporting process!
            $this->info("Exporting meetings for consultant with email: {$email}\n", 'info');
            $this->exportMeetingsForConsultant($email);
        } else {
            // No email supplied, so begin process of exporting for all consultants
            $this->info("Exporting meetings for all consultants...\n", 'info');
            $this->exportMeetingsForAll();
        }
    }

    /**
     * @param $email
     *
     * This function will be executed when an email address has been supplied
     * We will write the RFC 5545 format for iCal files ourselves so we don't
     * have to rely on a third party package
     *
     *
     */
    protected function exportMeetingsForConsultant($email)
    {
        // Get the consultant that matches the supplied email address
        $employee = Employee::where('email', $email)->firstOrFail(); // Make sure consultant exists

        // Get all meetings for that consultant
        $meetings = $employee->meetings;

        // Set path to the exports folder
        $path = storage_path('exports');

        // If the exports folder doesn't exist, create it and give it all permissions
        // Probably not the safest options but I'm in a rush
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        // Set the filename to a slug of the email address
        $filename = Str::slug($email) . '-meetings.ics';

        // Full path
        $fullPath = storage_path('exports/' . $filename);

        // Get the number of consultants meetings so we can display it in the command window
        $numberOfMeetings = $meetings->count();

        // Open the file with write mode
        $file = fopen($fullPath, 'w');

        // iCal headers
        fwrite($file, "BEGIN:VCALENDAR\r\n");
        fwrite($file, "VERSION:2.0\r\n");
        fwrite($file, "PRODID:-//Your Company//Your Product//EN\r\n");

        // Write progress to Command window
        $this->info("Writing {$numberOfMeetings} meetings to {$filename}...", 'info');

        // Create a progressbar to show process
        $progress = $this->output->createProgressBar($meetings->count());

        // These two lines are just for readability
        $this->line('');
        $this->line('');

        // For every meeting of the consultant write the RFC 5545 format
        foreach ($meetings as $meeting) {
            fwrite($file, "BEGIN:VEVENT\r\n");
            fwrite($file, "UID:" . uniqid() . "@yourdomain.com\r\n"); // Unique ID for each event
            fwrite($file, "DTSTAMP:" . now()->format('Ymd\THis\Z') . "\r\n"); // Timestamp
            fwrite($file, "DTSTART:" . \Carbon\Carbon::parse($meeting->start_date)->format('Ymd\THis') . "\r\n"); // Start time
            fwrite($file, "DTEND:" . \Carbon\Carbon::parse($meeting->end_date)->format('Ymd\THis') . "\r\n"); // End time
            fwrite($file, "SUMMARY:" . $meeting->description . "\r\n"); // Description
            fwrite($file, "END:VEVENT\r\n");

            // Make our progress bar advance after every meeting
            $progress->advance(); // Advance the progress bar

            // Add a delay for show
            usleep(20000); // 1 second = 1,000,000 microseconds

        }
        // iCal footer
        fwrite($file, "END:VCALENDAR\r\n");

        // Close the file
        fclose($file);

        // The progress bar is now full
        $progress->finish();

        // Readability again
        $this->line("");
        $this->line("");

        // Let the user know all meetings have been exported successfully
        $this->info("All {$numberOfMeetings} meetings for {$email} exported to {$filename}", 'success');
    }

    /**
     *
     * This function will be executed when the email address has been omitted
     * We'll write the RFC 5545 format ourselves so we don't have to rely
     * on third party packages
     *
     */
    protected function exportMeetingsForAll()
    {
        // Get all consultants
        $employees = Employee::all();

        // Total meetings found in DB. We'll need it later for feedback
        $totalMeetings = Meeting::count();

        // Folder for the iCal files will be exports
        $path = storage_path('exports');

        // If that folder doesn't exist yet, create it and give it permissions to store files in
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        // Let the user know we're processing the meetings
        $this->info("Processing meetings for all consultants...", 'info');

        // These two lines are for readability
        $this->line("");
        $this->line("");

        // For each consultant found in DB make an .ics files and write the meetings
        foreach ($employees as $employee) {
            // The created file will be a slug of the email ending in -meetings
            $filename = Str::slug($employee->email) . '-meetings.ics';
            $fullPath = storage_path('exports/' . $filename);

            // Create a progress bar for each consult
            $progress = $this->output->createProgressBar($employee->meetings->count());

            // Open the file in write mode
            $file = fopen($fullPath, 'w');

            // iCal headers
            fwrite($file, "BEGIN:VCALENDAR\r\n");
            fwrite($file, "VERSION:2.0\r\n");
            fwrite($file, "PRODID:-//Your Company//Your Product//EN\r\n");

            // Number of meetings each consultant has
            $number_of_meetings = $employee->meetings->count();

            // Feedback for the user
            $this->info("");
            $this->info("Writing {$number_of_meetings} meetings to {$filename} for consultant {$employee->email}...", 'info');

            // For every meeting of the consultant write the RFC 5545 format
            foreach ($employee->meetings as $meeting) {
                fwrite($file, "BEGIN:VEVENT\r\n");
                fwrite($file, "UID:" . uniqid() . "@yourdomain.com\r\n"); // Unique ID
                fwrite($file, "DTSTAMP:" . now()->format('Ymd\THis\Z') . "\r\n"); // Current timestamp
                fwrite($file, "DTSTART:" . \Carbon\Carbon::parse($meeting->start_date)->format('Ymd\THis') . "\r\n");
                fwrite($file, "DTEND:" . \Carbon\Carbon::parse($meeting->end_date)->format('Ymd\THis') . "\r\n");
                fwrite($file, "SUMMARY:" . $meeting->description . "\r\n");
                fwrite($file, "NOTES:" . $meeting->notes . "\r\n");
                fwrite($file, "END:VEVENT\r\n");

                // Advance the progress bar
                $progress->advance();

                // Add a delay for show
                usleep(20000); // 1 second = 1,000,000 microseconds
            }
            // iCal footer
            fwrite($file, "END:VCALENDAR\r\n");

            // Close the file
            fclose($file);

            // The progress bar is now finished
            $progress->finish();
        }

        // Feedback for the user
        $this->line('');
        $this->line('');
        $this->info("All {$totalMeetings} meetings exported successfully to iCal files", 'info');
    }
}

