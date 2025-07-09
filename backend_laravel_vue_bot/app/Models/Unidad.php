<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unidad extends Model
{
    public function centro(){
        return $this->belongsTo(Centro::class);
    }

    public function administrativos(){
        return $this->hasMany(Administrativo::class);
    }
}
