<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Get the enrolments for the course.
     */
    public function enrolments()
    {
        return $this->hasMany(Enrolment::class);
    }

    /**
     * Get the learners enrolled in the course.
     */
    public function learners()
    {
        return $this->belongsToMany(Learner::class, 'enrolments')
                    ->withPivot('progress')
                    ->withTimestamps();
    }
}
