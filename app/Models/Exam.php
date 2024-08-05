<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'questions_count',
        'is_handled',
        'start_time',
        'end_time',
    ];


    /**
     * Get the prizes associated with the exam.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function prizes() 
    {
        return $this->hasMany(Prize::class, 'exam_id', 'id');
    }


    /**
     * Get all the prize records associated with the exam.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function prizeRecords() 
    {
        return $this->hasMany(PrizeRecord::class, 'exam_id', 'id');
    }


    /**
     * Get all the submissions associated with the exam.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function submissions() 
    {
        return $this->hasMany(Submission::class, 'exam_id', 'id');
    }


    /**
     * Get all the exam questions associated with the exam.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function examQuestions() 
    {
        return $this->hasMany(ExamQuestion::class, 'exam_id', 'id');
    }
}
