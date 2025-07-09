<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Administrativo extends Model
{
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function unidad(){
        return $this->belongsTo(Unidad::class);
    }
}
