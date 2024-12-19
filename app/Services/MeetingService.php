<?php
namespace App\Services;

use App\Models\Meeting;

class MeetingService
{
    /**
     * Retrieve meetings from DB.
     *
     * @param array $filters (optional)
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getMeetings(array $filters = [])
    {
        $query = Meeting::query();

        /*
         * Add filters here like start_date and end_date
         *
         */

        return $query->get();
    }
}
