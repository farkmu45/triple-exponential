<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prediction extends Model
{
    use HasFactory;

    protected $primaryKey = 'date';
    protected $guarded = [''];

    protected $casts = [
        'date' => 'datetime:Y-m-d',
    ];
}
