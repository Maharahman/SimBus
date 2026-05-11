<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    // Disable timestamps as they aren't in migration
    public $timestamps = false;

    protected $primaryKey = 'id'; 
    public $incrementing = true;

    // Defined fields from migration 
    protected $fillable = [
        'id',    
        'name', 
        'price'
    ];
}