<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
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
     * Get all the exam questions associated with the question.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function examQuestions()
    {
        return $this->hasMany(ExamQuestion::class, 'question_id', 'id');
    }


    /**
     * Get all the choices associated with the question.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function choices()
    {
        return $this->hasMany(Choice::class, 'question_id', 'id');
    }
}
