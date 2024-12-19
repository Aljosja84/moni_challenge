<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Meeting extends Model
{
    use HasFactory;
    // Id is not incrementing because it's an uuid
    public $incrementing    = false;
    // Primary key is a string
    protected $keyType      = 'string';
    // Make sure laravel treats the dates as Carbon instances
    protected $casts = [
        'start_date'    => 'datetime',
        'end_date'      => 'datetime',
    ];
    // Calendar events don't have an created_at or updated_at column so bypass these when updating a record
    public $timestamps = false;
    // Table name is project_planning in DB
    protected $table        = 'project_planning';
    // Allow the following fields to be filled in DB
    protected $fillable = ['description', 'start_date', 'end_date'];

    /**
     * Define the relationship to the employee model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'emp_id');
    }

    /**
     *
     * Make a scope so as not to clutter the Controller
     *
     *
     * @param $query
     * @return mixed
     */
    public function scopeWithinCurrentWeek($query)
    {
        // Set the start and end of the current week date
        $startOfWeek    = Carbon::now()->startOfWeek();
        $endOfWeek      = Carbon::now()->endOfWeek();

        // Fetch the meetings for the employee for the current week
        // For this, we also have to to make sure to get meetings that overlap with the current week
        // It's a bit of an overkill, but a challenge is challenge :D
        // This query will handle different scenarios:
        return $query->where(function ($query) use ($startOfWeek, $endOfWeek) {
            // Finds meetings that start within the current week
            $query->whereBetween('start_date', [$startOfWeek, $endOfWeek])
                // Finds meetings that end within the current week
                ->orWhereBetween('end_date', [$startOfWeek, $endOfWeek])
                // Finds meetings that span across the entire week (start before the week begins and end after the week ends).
                ->orWhere(function ($query) use ($startOfWeek, $endOfWeek) {
                    $query->where('start_date', '<', $startOfWeek)
                        ->where('end_date', '>', $endOfWeek);
                });
        });
    }
}
