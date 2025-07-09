<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function detalle_ventas(){
        return $this->hasMany(DetallesVenta::class);
    }
}
