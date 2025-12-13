<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Learner extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstname',
        'lastname',
    ];

    /**
     * Get the enrolments for the learner.
     */
    public function enrolments()
    {
        return $this->hasMany(Enrolment::class);
    }

    /**
     * Get the courses the learner is enrolled in.
     */
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'enrolments')
                    ->withPivot('progress')
                    ->withTimestamps();
    }
}
