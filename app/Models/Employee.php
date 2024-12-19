<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    // id is not incrementing because it's an uuid
    public $incrementing    = false;
    // primary key is a string
    protected $keyType      = 'string';

    protected $fillable = [];

    /**
     * Define the relationship to the Meeting model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function meetings()
    {
        return $this->hasMany(Meeting::class, 'emp_id');
    }

    /**
     *
     * Get the email from a consultant
     *
     */
    public function getEmailAttribute($value)
    {
        return $value;
    }
}
