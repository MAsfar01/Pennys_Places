<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;
    protected $primaryKey = 'jobs_id';

    // Indicate if the primary key is auto-incrementing
    public $incrementing = true;

    // Specify the primary key type (usually 'int' for bigIncrements)
    protected $keyType = 'int';
    protected $fillable = [
        'name',
        'date',
        'starting_time',
        'ending_time',
    ];
}
