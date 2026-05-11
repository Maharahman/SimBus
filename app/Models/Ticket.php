<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
    'ticket_id',
    'category',
    'subject',
    'message',
    'registration_data',
    'status',
    'handled_by',
    'user_num',
    'user_id'
    ];
    
    public function user(){
    return $this->belongsTo(User::class, 'user_id');
    }
}