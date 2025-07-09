<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Centro extends Model
{
    public function unidads(){
        return $this->hasMany(Unidad::class);
    }
}
