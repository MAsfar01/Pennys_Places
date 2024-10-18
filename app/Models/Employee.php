<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    protected $primaryKey = 'employees_id';

    // Indicate if the primary key is auto-incrementing
    public $incrementing = true;

    // Specify the primary key type (usually 'int' for bigIncrements)
    protected $keyType = 'int';
    protected $fillable = [
        'name',
        'phone_number',
        'email',
        'password',
        'profile_image',
        'id_image',
        'document_image',
        'certificate_image',
    ];
}
