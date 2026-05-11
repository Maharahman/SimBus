<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    // 1. Tell Laravel you are using 'id' as the ID
    protected $primaryKey = 'id'; 
    
    // 2. Tell Laravel your table DOES NOT have created_at / updated_at
    public $timestamps = false; 

    protected $fillable = ['name', 'sur_name', 'num', 'pass', 'level', 'profile_photo'];

    protected $hidden = ['pass'];
}