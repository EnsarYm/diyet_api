<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diet extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'manager_note',
        'start_date',
        'end_date',
        'manager_id',
        'user_id',
        'file'
    ];
}
