<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lunch extends Model
{
    protected $fillable = [
        'name',
        'order',
        'notes'
    ];
}
