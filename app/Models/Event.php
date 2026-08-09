<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;
    
    protected $guarded = ['id'];

    // TAMBAHIN KODINGAN INI BANG:
    public function eventDetails()
    {
        // 1 Event punya banyak (hasMany) EventDetail
        return $this->hasMany(EventDetail::class, 'event_id');
    }
}